<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Babelfisch\Babelfisch;
use Babelfisch\BabelfischException;
use Babelfisch\StorageAdapter\FilesystemAdapter;

final class ResolveTest extends TestCase
{
    public function testResolve(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $this->assertSame(
            'foo bar baz',   
            $bf->output('foo:foo')
        );
    }

    public function testResolvePrecedence(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $this->assertSame(
            'foo bar boo',   
            $bf->output('foo:foo', ['baz:baz' => 'boo'])
        );
    }

    public function testEndlessLoopException(): void
    {
        $this->expectException(
            BabelfischException::class,
            Babelfisch::class . ' must throw an exception when an unresolveable, endless loop is detected.'
        );
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $bf->output('endless_loop:foo');
    }
}
