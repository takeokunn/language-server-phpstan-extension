<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan\Tests\Provider;

use Generator;
use PHPUnit\Framework\TestCase;
use Phpactor\Extension\LanguageServerPhpstan\Model\Linter\TestLinter;
use Phpactor\Extension\LanguageServerPhpstan\Provider\PhpstanDiagnosticProvider;
use Phpactor\Extension\LanguageServerPhpstan\Tests\Util\DiagnosticBuilder;
use Phpactor\LanguageServer\Event\TextDocumentUpdated;
use Phpactor\LanguageServer\LanguageServerTesterBuilder;
use Phpactor\LanguageServer\Test\LanguageServerTester;
use Phpactor\LanguageServer\Test\ProtocolFactory;
use function Amp\Promise\wait;
use function Amp\delay;

final class PhpstanDiagnosticProviderTest extends TestCase
{
    private \Phpactor\LanguageServer\Test\LanguageServerTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $tester = LanguageServerTesterBuilder::create();
        $tester->addDiagnosticsProvider(new PhpstanDiagnosticProvider(
            $this->createTestLinter()
        ));
        $tester->enableDiagnostics();
        $tester->enableTextDocuments();
        $this->tester = $tester->build();
        $this->tester->initialize();
    }

    /**
     * @return Generator<mixed>
     */
    public function testHandleSingle(): void
    {
        $updated = new TextDocumentUpdated(ProtocolFactory::versionedTextDocumentIdentifier('file://path', 12), 'asd');
        $this->tester->textDocument()->open('file:///path', 'asd');
        $this->tester->textDocument()->update('file:///path', 'zar');

        wait(delay(10));

        $this->assertSame(1, $this->tester->transmitter()->count());
    }

    private function createTestLinter(): TestLinter
    {
        return new TestLinter([
            DiagnosticBuilder::create()->build(),
        ], 10);
    }
}
