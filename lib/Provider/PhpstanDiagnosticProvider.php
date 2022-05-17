<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan\Provider;

use Amp\Promise;
use Phpactor\Extension\LanguageServerPhpstan\Model\Linter;
use Phpactor\LanguageServerProtocol\TextDocumentItem;
use Phpactor\LanguageServer\Core\Diagnostics\DiagnosticsProvider;

final class PhpstanDiagnosticProvider implements DiagnosticsProvider
{
    public function __construct(private Linter $linter)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function provideDiagnostics(TextDocumentItem $textDocument): Promise
    {
        return $this->linter->lint($textDocument->uri, $textDocument->text);
    }
}
