<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpstan;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\Extension\LanguageServerPhpstan\Model\Linter;
use Phpactor\Extension\LanguageServerPhpstan\Model\Linter\PhpstanLinter;
use Phpactor\Extension\LanguageServerPhpstan\Model\PhpstanConfig;
use Phpactor\Extension\LanguageServerPhpstan\Model\PhpstanProcess;
use Phpactor\Extension\LanguageServerPhpstan\Provider\PhpstanDiagnosticProvider;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\MapResolver\Resolver;

final class LanguageServerPhpstanExtension implements Extension
{
    public const PARAM_PHPSTAN_BIN = 'language_server_phpstan.bin';
    public const PARAM_LEVEL = 'phpstan.level';

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container): void
    {
        $container->register(PhpstanDiagnosticProvider::class, fn (Container $container) => new PhpstanDiagnosticProvider(
            $container->get(Linter::class)
        ), [
            LanguageServerExtension::TAG_DIAGNOSTICS_PROVIDER=> [
                'name' => 'phpstan'
            ],
        ]);

        $container->register(Linter::class, fn (Container $container) => new PhpstanLinter($container->get(PhpstanProcess::class)));

        $container->register(PhpstanProcess::class, function (Container $container) {
            $binPath = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER)->resolve($container->getParameter(self::PARAM_PHPSTAN_BIN));
            $root = $container->get(FilePathResolverExtension::SERVICE_FILE_PATH_RESOLVER)->resolve('%project_root%');

            return new PhpstanProcess(
                $root,
                new PhpstanConfig($binPath, $container->getParameter(self::PARAM_LEVEL)),
                $container->get(LoggingExtension::SERVICE_LOGGER)
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Resolver $schema): void
    {
        $schema->setDefaults([
            self::PARAM_PHPSTAN_BIN => '%project_root%/vendor/bin/phpstan',
            self::PARAM_LEVEL => null,
        ]);
    }
}
