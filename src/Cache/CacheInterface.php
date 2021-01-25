<?php
namespace Babelfisch\Cache;

interface CacheInterface
{
    public function store(string $id, string $language_id, array $temp_replacements, string $data) : bool; 
    
    public function retrieve(string $id, string $language_id, array $temp_replacements);
}