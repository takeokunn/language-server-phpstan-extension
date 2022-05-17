<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan\Tests\Model;

use PHPUnit\Framework\TestCase;
use Phpactor\Extension\LanguageServerPhpstan\Model\DiagnosticsParser;
use RuntimeException;

final class DiagnosticsParserTest extends TestCase
{
    /**
     * @dataProvider provideParse
     */
    public function testParse(string $phpstanJson, int $count): void
    {
        $this->assertCount($count, (new DiagnosticsParser())->parse($phpstanJson));
    }

    public function provideParse()
    {
        yield [
            '{"totals":{"errors":0,"file_errors":1},"files":{"'.__FILE__.'":{"errors":1,"messages":[{"message":"Undefined variable: $bar","line":3,"ignorable":true}]}},"errors":[]}',
            1
        ];
    }

    // public function testExceptionOnNonJsonString(): void
    // {
    //     $this->expectException(RuntimeException::class);
    //     $this->expectExceptionMessage('stdout was not JSON');
    //     (new DiagnosticsParser())->parse('stdout was not JSON');
    // }
}
