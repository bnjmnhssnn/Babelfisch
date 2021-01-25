<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Babelfisch\BabelfischException;
use Babelfisch\Cache\FilesystemCache;

final class FilesystemCacheTest extends TestCase
{
    protected function setUp() : void
    {
        $this->cleanup();
    }

    public function testInvalidCacheDir(): void
    {
        $this->expectException(
            \InvalidArgumentException::class,
            'Instanciation of ' . FilesystemCache::class . ' must throw an exception when an invalid cache dir is provided.'
        );
        $storage_adapter = new FilesystemCache(__DIR__ . '/non_existing_path');  
    }

    public function testStoreAndRetrieve(): void
    {
        $cache_module = new FilesystemCache(__DIR__ . '/cache');

        $cache_module->store('foo', 'DE', ['bar' => 1, 'baz' => 2], 'Testdata 1');
        $cache_module->store('foo1', 'DE', ['bar' => 1, 'baz' => 2], 'Testdata 2');
        $cache_module->store('foo', 'EN', ['bar' => 1, 'baz' => 2], 'Testdata 3');

        $this->assertSame(
            'Testdata 1',
            $cache_module->retrieve('foo', 'DE', ['bar' => 1, 'baz' => 2])
        );
        $this->assertSame(
            'Testdata 1',
            $cache_module->retrieve('foo', 'DE', ['baz' => 2, 'bar' => 1]),
            'When the order of the 3rd argument array changes, the cache module must still retrieve the same entry.'
        );
        $this->assertSame(
            'Testdata 2',
            $cache_module->retrieve('foo1', 'DE', ['bar' => 1, 'baz' => 2])
        );
        $this->assertSame(
            'Testdata 3',
            $cache_module->retrieve('foo', 'EN', ['bar' => 1, 'baz' => 2])
        );
    }

    protected function tearDown() : void
    {
        $this->cleanup();
    }

    protected function cleanup() : void
    {
        $files = glob(__DIR__ . '/cache/*');
        foreach($files as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }
}
