<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Babelfisch\Babelfisch;
use Babelfisch\BabelfischException;
use Babelfisch\StorageAdapter\FilesystemAdapter;

final class BasicUsageTest extends TestCase
{
    public function testBasicOutput(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $this->assertSame(
            $bf->output('greetings:greet'),
            file_get_contents(__DIR__ . '/storage/greetings/greet_DE.txt')   
        );
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'EN'
        );
        $this->assertSame(
            $bf->output('greetings:greet'),
            file_get_contents(__DIR__ . '/storage/greetings/greet_EN.txt')   
        );
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'NL', 'EN'
        );
        $this->assertSame(
            $bf->output('greetings:greet'),
            file_get_contents(__DIR__ . '/storage/greetings/greet_EN.txt')
        );
    }
}
