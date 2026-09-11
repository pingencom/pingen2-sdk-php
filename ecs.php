<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/ecs.php',
    ])
    ->withPreparedSets(
        arrays: true,
        namespaces: true,
        spaces: true,
        comments: true,
        cleanCode: true,
        strict: true,
        controlStructures: true,
        psr12: true,
    )
    ->withConfiguredRule(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
