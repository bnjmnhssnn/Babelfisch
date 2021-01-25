<?php
namespace Babelfisch;
use Babelfisch\Cache\CacheInterface;
use Babelfisch\StorageAdapter\StorageAdapterInterface;


class Babelfisch
{
    protected $storage_adapter = NULL;
    protected $language_ids = NULL;
    protected $cache_module = NULL;
    protected $cache_exceptions = NULL;
    protected $id_separator = ':';
    protected $loaded = [];

    public function __construct(StorageAdapterInterface $storage_adapter, string ...$language_ids)
    {
        $this->storage_adapter = $storage_adapter;
        $this->language_ids = $language_ids;    
    }

    public function setCache(CacheInterface $cache_module, bool $cache_exceptions = false) : void
    {
        $this->cache_module = $cache_module;
        $this->cache_exceptions = $cache_exceptions;
    }

    public function outputWithCache(string $id, array $temp_replacements = []) : string
    {
        return $this->output($id, $temp_replacements, true);
    }

    public function output(string $id, array $temp_replacements = [], $use_cache = false) : string
    {
        if($use_cache) {
            if(NULL === $this->cache_module) {
                throw new BabelfischException("No cache module set.");
            }
            if (false !== $from_cache = $this->cache_module->retrieve($id, $this->language_ids[0], $temp_replacements)) {
                return $from_cache;   
            }
        }
        if(!array_key_exists($id, $this->loaded)) {
            $this->loaded[$id] = $this->load($id);   
        }
        $output = $this->resolve($this->loaded[$id], $temp_replacements);
        if($use_cache && NULL !== $this->cache_module) {
            if(!$this->cache_module->store($id, $this->language_ids[0], $temp_replacements, $output) && $this->cache_exceptions) {
                throw new BabelfischException("Cache module cannot store generated output.");
            }    
        }
        return $output;
    }

    protected function load(string $id) : string
    {
        foreach($this->language_ids as $language_id) {
            if (false !== $loaded_string = $this->storage_adapter->load($this->splitId($id), $language_id)) {
                return $loaded_string;
            }
        }
        throw new BabelfischException(
            "No entry found for id {$id} and language(s) " . join(', ', $this->language_ids) . "."
        );
    }

    protected function resolve(string $string, array $replacements = [], array $loop_detect = []) : string
    {
        preg_match_all('/{{(.*?)}}/', $string, $matches);
        if (empty($matches[1])) {
            return $string;
        }
        foreach($matches[1] as $match) {
            if (in_array($match, $loop_detect)) {
                throw new BabelfischException(
                    'Unable to resolve endless loop ' . join(' > ', $loop_detect) . ' > ' . $match
                );
            }
            array_push($loop_detect, $match);
            if(array_key_exists($match, $replacements)) {
                $replacement = $this->resolve($replacements[$match], $replacements, $loop_detect);
            } else {
                if(!array_key_exists($match, $this->loaded)) {
                    $this->loaded[$match] = $this->load($match);
                }
                $replacement = $this->resolve($this->loaded[$match], $replacements, $loop_detect);
            }
            $string = preg_replace('/{{' . $match . '}}/', $replacement, $string);
        }
        return $string;
    }

    protected function splitId(string $entry_id) : array
    {
        return preg_split('/' . $this->id_separator . '/', $entry_id);
    }
}