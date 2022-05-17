<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan\Model;

final class PhpstanConfig
{
    private string $phpstanBin;

    private ?string $level;

    public function __construct(string $phpstanBin, ?string $level)
    {
        $this->phpstanBin = $phpstanBin;
        $this->level = $level;
    }

    public function level(): ?string
    {
        return $this->level;
    }

    public function phpstanBin(): string
    {
        return $this->phpstanBin;
    }
}
