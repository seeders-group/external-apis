<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Facade;

dataset('facade classes', function (): array {
    $root = dirname(__DIR__, 3);
    $files = glob($root.'/src/Facades/*.php') ?: [];

    $classes = [];

    foreach ($files as $file) {
        $relativePath = substr($file, strlen($root.'/src/'));
        $class = 'Seeders\\ExternalApis\\'.str_replace(['/', '.php'], ['\\', ''], $relativePath);

        if (! class_exists($class)) {
            require_once $file;
        }

        if (! class_exists($class)) {
            continue;
        }

        if (is_subclass_of($class, Facade::class) && ! (new \ReflectionClass($class))->isAbstract()) {
            $classes[$class] = [$class];
        }
    }

    return $classes;
});

it('defines facade accessors', function (string $facadeClass): void {
    $accessor = \Closure::bind(
        static fn () => $facadeClass::getFacadeAccessor(),
        null,
        $facadeClass
    )();

    test()->assertIsString($accessor);
    test()->assertNotSame('', $accessor);
})->with('facade classes');
