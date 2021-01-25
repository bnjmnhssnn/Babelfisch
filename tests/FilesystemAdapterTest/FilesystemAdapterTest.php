<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Babelfisch\BabelfischException;
use Babelfisch\StorageAdapter\FilesystemAdapter;

final class FilesystemAdapterTest extends TestCase
{
    public function testInvalidSourceDir(): void
    {
        $this->expectException(
            \InvalidArgumentException::class,
            'Instanciation of ' . FilesystemAdapter::class . ' must throw an exception when an invalid source dir is provided.'
        );
        $storage_adapter = new FilesystemAdapter(__DIR__ . '/non_existing_path');  
    }

    public function testLoad(): void
    {
        $storage_adapter = new FilesystemAdapter(__DIR__ . '/storage');
        $this->assertSame(
            $storage_adapter->load(['foo', 'foo1'], 'DE'),
            file_get_contents(__DIR__ . '/storage/foo/foo1_DE.txt')    
        );
        $this->assertFalse(
            $storage_adapter->load(['non_existing_entry'], 'EN'),
            'Trying to load a non-existing entry must return false.'
        );
        $this->assertFalse(
            $storage_adapter->load(['non_existing_entry'], 'DE'),
            'Trying to load a non-existing entry must return false.'
        );
        $this->assertFalse(
            $storage_adapter->load(['foo'], 'DE'),
            'Trying to load a non-existing entry must return false.'
        );
        $storage_adapter = new FilesystemAdapter(__DIR__ . '/storage/');
        $this->assertSame(
            $storage_adapter->load(['foo', 'foo1'], 'DE'),
            file_get_contents(__DIR__ . '/storage/foo/foo1_DE.txt'),
            'A trailing slash on the source dir must be trimmed.'    
        );
    }
}
