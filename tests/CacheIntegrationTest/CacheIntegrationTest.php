<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Babelfisch\Babelfisch;
use Babelfisch\BabelfischException;
use Babelfisch\StorageAdapter\FilesystemAdapter;
use Babelfisch\Cache\FilesystemCache;

final class CacheIntegrationTest extends TestCase
{
    protected function setUp() : void
    {
        $this->cleanup();
    }

    public function testResolveWithCache(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $bf->setCache(
            new FilesystemCache(__DIR__ . '/cache')
        );
        $bf->outputWithCache('greet');
        $cached_content = glob(__DIR__ . '/cache/*');
        $this->assertEquals(1, count($cached_content));
        $this->assertEquals(
            'Grüße aus Deutschland!',
            file_get_contents($cached_content[0])    
        );
        file_put_contents($cached_content[0], 'Successfully retrieved from cache!');
        $this->assertEquals(
            'Successfully retrieved from cache!',
            $bf->outputWithCache('greet')
        );
        $this->cleanup();
    }

    public function testLanguageFallbackWithCache()
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'EN', 'DE'
        );
        $bf->setCache(
            new FilesystemCache(__DIR__ . '/cache')
        );
        $bf->outputWithCache('greet');
        $cached_content = glob(__DIR__ . '/cache/*');
        $this->assertEquals(1, count($cached_content));
        $this->assertEquals(
            'Greetings from Deutschland!',
            file_get_contents($cached_content[0])    
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
