<?php 
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Babelfisch\Babelfisch;
use Babelfisch\BabelfischException;
use Babelfisch\StorageAdapter\FilesystemAdapter;

final class NotFoundActionTest extends TestCase
{
    public function testException(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $this->expectException(BabelfischException::class);
        $bf->setNotFoundAction(Babelfisch::NOT_FOUND_ACTION_EXCEPTION);
        $bf->output('foo:foo');
    }

    public function testShowId(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $bf->setNotFoundAction(Babelfisch::NOT_FOUND_ACTION_SHOW_ID);
        $this->assertSame(
            'foo [bar] foo',
            $bf->output('foo:foo')
        ); 
    }

    public function testEmptyString(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $bf->setNotFoundAction(Babelfisch::NOT_FOUND_ACTION_EMPTY_STRING);
        $this->assertSame(
            'foo  foo',
            $bf->output('foo:foo')
        );
    }

    public function testCallable(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $bf->setNotFoundAction(
            function($id) {
                return "*{$id}*";
            }
        );
        $this->assertSame(
            'foo *bar* foo',
            $bf->output('foo:foo')
        );
    }

    public function testCallableWithWrongReturnType(): void
    {
        $bf = new Babelfisch(
            new FilesystemAdapter(__DIR__ . '/storage'),
            'DE'
        );
        $bf->setNotFoundAction(
            function($id) {
                return [$id];
            }
        );
        $this->expectException(BabelfischException::class);
        $bf->output('foo:foo');
    }
}
