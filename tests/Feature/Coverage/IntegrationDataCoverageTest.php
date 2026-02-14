<?php

declare(strict_types=1);

use Spatie\LaravelData\Data;

dataset('integration data classes', function (): array {
    $root = dirname(__DIR__, 3);

    $classes = [];

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($root.'/src/Integrations')
    );

    foreach ($iterator as $fileInfo) {
        if (! $fileInfo->isFile()) {
            continue;
        }

        $file = $fileInfo->getPathname();

        if (! str_contains($file, '/Data/') || ! str_ends_with($file, '.php')) {
            continue;
        }

        $relativePath = substr($file, strlen($root.'/src/'));
        $class = 'Seeders\\ExternalApis\\'.str_replace(['/', '.php'], ['\\', ''], $relativePath);

        if (! class_exists($class)) {
            require_once $file;
        }

        if (! class_exists($class)) {
            continue;
        }

        if (is_subclass_of($class, Data::class) && ! (new \ReflectionClass($class))->isAbstract()) {
            $classes[$class] = [$class];
        }
    }

    return $classes;
});

it('can instantiate integration data objects', function (string $dataClass): void {
    $instance = instantiateClass($dataClass);

    expect($instance)->toBeInstanceOf($dataClass);
})->with('integration data classes');
