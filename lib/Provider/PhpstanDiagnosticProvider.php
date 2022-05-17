<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan\Provider;

use Amp\Promise;
use Phpactor\Extension\LanguageServerPhpstan\Model\Linter;
use Phpactor\LanguageServerProtocol\TextDocumentItem;
use Phpactor\LanguageServer\Core\Diagnostics\DiagnosticsProvider;

final class PhpstanDiagnosticProvider implements DiagnosticsProvider
{
    private \Phpactor\Extension\LanguageServerPhpstan\Model\Linter $linter;

    public function __construct(Linter $linter)
    {
        $this->linter = $linter;
    }

    /**
     * {@inheritDoc}
     */
    public function provideDiagnostics(TextDocumentItem $textDocument): Promise
    {
        return $this->linter->lint($textDocument->uri, $textDocument->text);
    }
}
