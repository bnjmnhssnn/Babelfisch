<?php
namespace Babelfisch\StorageAdapter;
use Babelfisch\BabelfischException;

class FilesystemAdapter implements StorageAdapterInterface
{
    protected $source_dir = NULL;

    public function __construct(string $source_dir)
    {
        if (!is_dir($source_dir)) {
            throw new \InvalidArgumentException("Source directory {$source_dir} does not exist.");
        }
        $this->source_dir = rtrim($source_dir, '/');
    }

    public function load(array $id_segments, string $language_id)
    {
        array_unshift($id_segments, $this->source_dir);
        $filesystem_path = join('/', $id_segments) . '_' . $language_id . '.txt';
        if(!is_file($filesystem_path)) {
            return false;   
        }
        return file_get_contents($filesystem_path);
    }
}