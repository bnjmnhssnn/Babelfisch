<?php
namespace Babelfisch\Cache;
use Babelfisch\BabelfischException;
use Babelfisch\StorageAdapter\StorageAdapterInterface;

class FilesystemCache implements CacheInterface
{
    protected $cache_dir = NULL;
    protected $loaded = [];

    public function __construct(string $cache_dir)
    {
        if (!is_dir($cache_dir)) {
            throw new \InvalidArgumentException("Source directory {$cache_dir} does not exist.");
        }
        $this->cache_dir = rtrim($cache_dir, '/');
    }

    public function store(string $id, string $language_id, array $temp_replacements, string $data) : bool
    {
        $hash = $this->get_hash($id, $language_id, $temp_replacements);
        return file_put_contents($this->cache_dir . '/' . $hash, $data);
    }

    public function retrieve(string $id, string $language_id, array $temp_replacements)
    {
        $hash = $this->get_hash($id, $language_id, $temp_replacements);
        if(!array_key_exists($hash, $this->loaded)) {
            if(false === $file_content = @file_get_contents($this->cache_dir . '/' . $hash)) {
                return false;
            }
            $this->loaded[$hash] = $file_content;
        }
        return $this->loaded[$hash];
    }

    protected function get_hash(string $id, string $language_id, array $temp_replacements) : string
    {
        ksort($temp_replacements);
        return md5(serialize([$id, $language_id, $temp_replacements]));
    }

}