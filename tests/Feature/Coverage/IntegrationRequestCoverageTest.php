<?php

declare(strict_types=1);

use Saloon\Http\Request;

dataset('integration request classes', function (): array {
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

        if (! str_contains($file, '/Requests/') || ! str_ends_with($file, '.php')) {
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

        if (is_subclass_of($class, Request::class) && ! (new \ReflectionClass($class))->isAbstract()) {
            $classes[$class] = [$class];
        }
    }

    return $classes;
});

it('can instantiate and resolve integration requests', function (string $requestClass): void {
    config()->set('external-apis.google_pagespeed.key', 'test-pagespeed-key');

    $request = instantiateClass($requestClass);

    expect($request)->toBeInstanceOf(Request::class);
    expect($request->resolveEndpoint())->toBeString();

    invokeOptionalMethod($request, 'defaultQuery');
    invokeOptionalMethod($request, 'defaultBody');
})->with('integration request classes');

function invokeOptionalMethod(object $instance, string $methodName): void
{
    if (! method_exists($instance, $methodName)) {
        return;
    }

    $method = new \ReflectionMethod($instance, $methodName);
    $method->setAccessible(true);

    try {
        $result = $method->invoke($instance);

        expect($result)->toBeArray();
    } catch (\TypeError $e) {
        if (! str_contains($e->getMessage(), 'TransformationContext::__construct')) {
            throw $e;
        }
    }
}
