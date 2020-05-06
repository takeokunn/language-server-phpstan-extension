<?php

namespace Phpactor\Extension\LanguageServerPhpstan\Tests\Handler;

use Amp\Delayed;
use Amp\PHPUnit\AsyncTestCase;
use Generator;
use LanguageServerProtocol\TextDocumentIdentifier;
use LanguageServerProtocol\VersionedTextDocumentIdentifier;
use Phpactor\Extension\LanguageServerPhpstan\Handler\PhpstanHandler;
use Phpactor\Extension\LanguageServerPhpstan\Model\Linter\TestLinter;
use Phpactor\Extension\LanguageServerPhpstan\Tests\Util\DiagnosticBuilder;
use Phpactor\LanguageServer\Event\TextDocumentSaved;
use Phpactor\LanguageServer\Event\TextDocumentUpdated;
use Phpactor\LanguageServer\Test\HandlerTester;

class PhpstanHandlerTest extends AsyncTestCase
{
    /**
     * @var HandlerTester
     */
    private $tester;

    /**
     * @var PhpstanHandler
     */
    private $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new PhpstanHandler($this->createTestLinter());
        $this->tester = new HandlerTester($this->handler);

        $this->tester->serviceManager()->start('phpstan');
    }

    /**
     * @return Generator<mixed>
     */
    public function testHandleSingle(): Generator
    {
        $updated = new TextDocumentUpdated(new VersionedTextDocumentIdentifier('file://path', 12), 'asd');
        $this->handler->lintUpdated($updated);

        yield new Delayed(10);

        $message = $this->tester->transmitter()->shift();

        self::assertNotNull($message);
        $this->tester->serviceManager()->stop('phpstan');
    }

    /**
     * @return Generator<mixed>
     */
    public function testHandleMany(): Generator
    {
        $updated = new TextDocumentUpdated(new VersionedTextDocumentIdentifier('file://path', 12), 'asd');
        $this->handler->lintUpdated($updated);

        yield new Delayed(10);

        $updated = new TextDocumentUpdated(new VersionedTextDocumentIdentifier('file://path', 12), 'asd');
        $this->handler->lintUpdated($updated);

        yield new Delayed(10);

        self::assertNotNull($this->tester->transmitter()->shift(), 'has message');

        $this->tester->serviceManager()->stop('phpstan');
    }

    /**
     * @return Generator<mixed>
     */
    public function testHandleManyFast(): Generator
    {
        $updated = new TextDocumentUpdated(new VersionedTextDocumentIdentifier('file://path', 12), 'asd');
        $this->handler->lintUpdated($updated);
        $this->handler->lintUpdated($updated);
        $this->handler->lintUpdated($updated);
        $this->handler->lintUpdated($updated);
        $this->handler->lintUpdated($updated);

        yield new Delayed(100);

        $messages = [];
        while ($message = $this->tester->transmitter()->shift()) {
            $messages[] = $message;
        }

        $this->tester->serviceManager()->stop('phpstan');

        self::assertCount(2, $messages);
    }

    /**
     * @return Generator<mixed>
     */
    public function testHandleSaved(): Generator
    {
        $saved = new TextDocumentSaved(new TextDocumentIdentifier('file://path'));
        $this->handler->lintSaved($saved);

        yield new Delayed(10);

        $message = $this->tester->transmitter()->shift();

        self::assertNotNull($message);
        $this->tester->serviceManager()->stop('phpstan');
    }


    private function createTestLinter(): TestLinter
    {
        return new TestLinter([
            DiagnosticBuilder::create()->build(),
        ], 10);
    }
}
