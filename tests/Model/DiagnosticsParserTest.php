<?php

namespace Phpactor\Extension\LanguageServerPhpstan\Tests\Model;

use PHPUnit\Framework\TestCase;
use Phpactor\Extension\LanguageServerPhpstan\Model\DiagnosticsParser;

class DiagnosticsParserTest extends TestCase
{
    /**
     * @dataProvider provideParse
     */
    public function testParse(string $phpstanJson, int $count): void
    {
        self::assertCount($count, (new DiagnosticsParser())->parse($phpstanJson));
    }

    public function provideParse()
    {
        yield [
            '{"totals":{"errors":0,"file_errors":1},"files":{"/home/daniel/www/phpactor/language-server-phpstan/test.php":{"errors":1,"messages":[{"message":"Undefined variable: $bar","line":3,"ignorable":true}]}},"errors":[]}',
            1
        ];
    }
}
