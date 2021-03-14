<?php

declare(strict_types=1);

error_reporting(E_ERROR | E_WARNING | E_PARSE);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class);
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [['syntax' => 'short']]);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/config', __DIR__ . '/ecs.php']);

    $parameters->set(
        Option::SETS,
        [
            SetList::SPACES,
            SetList::CLEAN_CODE,
            SetList::ARRAY,
            SetList::COMMENTS,
            SetList::STRICT,
            SetList::NAMESPACES,
            SetList::CONTROL_STRUCTURES,
            SetList::PSR_12,
        ]
    );
};