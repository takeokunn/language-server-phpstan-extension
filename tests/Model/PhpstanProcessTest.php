<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan\Tests\Model;

use Generator;
use Phpactor\Extension\LanguageServerPhpstan\Model\PhpstanConfig;
use Phpactor\Extension\LanguageServerPhpstan\Model\PhpstanProcess;
use Phpactor\LanguageServerProtocol\DiagnosticSeverity;
use Phpactor\LanguageServerProtocol\Position;
use Phpactor\LanguageServerProtocol\Range;
use Phpactor\LanguageServerProtocol\Diagnostic;
use Psr\Log\NullLogger;
use Phpactor\Extension\LanguageServerPhpstan\Tests\IntegrationTestCase;

final class PhpstanProcessTest extends IntegrationTestCase
{
    /**
     * @dataProvider provideLint
     */
    public function testLint(string $source, array $expectedDiagnostics): void
    {
        $this->workspace()->reset();
        $this->workspace()->put('test.php', $source);
        $linter = new PhpstanProcess(
            $this->workspace()->path(),
            new PhpstanConfig(__DIR__ . '/../../vendor/bin/phpstan', 7),
            new NullLogger()
        );
        $diagnostics = \Amp\Promise\wait($linter->analyse($this->workspace()->path('test.php')));
        $this->assertSame($expectedDiagnostics, $diagnostics);
    }

    /**
     * @return Generator<mixed>
     */
    public function provideLint(): Generator
    {
        yield [
            '<?php $foobar = "string";',
            []
        ];

        yield [
            '<?php $foobar = $barfoo;',
            [
                Diagnostic::fromArray([
                    'range' => new Range(
                        new Position(0, 0),
                        new Position(0, 24)
                    ),
                    'message' => 'Variable $barfoo might not be defined.',
                    'severity' => DiagnosticSeverity::ERROR,
                    'source' => 'phpstan'
                ])
            ]
        ];
    }
}
