<?php
namespace Babelfisch\StorageAdapter;

interface StorageAdapterInterface
{
    public function load(array $id_segments, string $language_id);
}