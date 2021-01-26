<?php
namespace Babelfisch;
use Babelfisch\Cache\CacheInterface;
use Babelfisch\StorageAdapter\StorageAdapterInterface;


class Babelfisch
{
    const NOT_FOUND_ACTION_EXCEPTION = 1;
    const NOT_FOUND_ACTION_SHOW_ID = 2;
    const NOT_FOUND_ACTION_EMPTY_STRING = 3;

    protected $storage_adapter = NULL;
    protected $language_ids = NULL;
    protected $cache_module = NULL;
    protected $cache_exceptions = NULL;
    protected $id_separator = ':';
    protected $loaded = [];
    protected $not_found_action = 1;

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

    public function setNotFoundAction($not_found_action) : void
    {
        $allowed_constants = [
            self::NOT_FOUND_ACTION_EXCEPTION,
            self::NOT_FOUND_ACTION_SHOW_ID,
            self::NOT_FOUND_ACTION_EMPTY_STRING
        ];
        if(!is_callable($not_found_action) &&  !in_array($not_found_action, $allowed_constants)) {
            throw new \InvalidArgumentException(
                "Please provide a callable or a defined NOT_FOUND_ACTION constant."
            );
        }
        $this->not_found_action = $not_found_action;
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

    protected function load(string $id)
    {
        foreach($this->language_ids as $language_id) {
            if (false !== $loaded_string = $this->storage_adapter->load($this->splitId($id), $language_id)) {
                return $loaded_string;
            }
        }
        return false;
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
                    $load_res = $this->load($match);
                    if(false === $load_res) {
                        $load_res = $this->applyNotFoundAction($match);   
                    }
                    $this->loaded[$match] = $load_res;
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

    protected function applyNotFoundAction(string $entry_id) : string
    {
        if(is_callable($this->not_found_action)) { 
            $func = $this->not_found_action;
            $result = $func($entry_id);
            if(is_string($result)) {
                return $result;
            }
            throw new BabelfischException(
                'The provided not-found-action-callable must return a string but returned ' . gettype($result)
            );  
        }
        switch($this->not_found_action) {
            case self::NOT_FOUND_ACTION_EXCEPTION:
                throw new BabelfischException(
                    "No entry found for id {$entry_id} and language(s) " . join(', ', $this->language_ids) . "."
                ); 
            case self::NOT_FOUND_ACTION_SHOW_ID:
                return "[{$entry_id}]";
            case self::NOT_FOUND_ACTION_EMPTY_STRING:
                return '';
            default:
                throw new BabelfischException("Undefined NOT_FOUND_ACTION provided.");     
        }
    }
}