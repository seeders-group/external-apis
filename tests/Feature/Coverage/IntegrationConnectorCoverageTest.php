<?php

declare(strict_types=1);

use Saloon\Http\Connector;

dataset('integration connector classes', function (): array {
    $root = dirname(__DIR__, 3);
    $files = glob($root.'/src/Integrations/*/*Connector.php') ?: [];

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

        if (is_subclass_of($class, Connector::class) && ! (new \ReflectionClass($class))->isAbstract()) {
            $classes[$class] = [$class];
        }
    }

    return $classes;
});

it('can resolve connector defaults', function (string $connectorClass): void {
    config()->set('external-apis.google_search.key', 'test-google-search-key');
    config()->set('external-apis.google_search.cx', 'test-google-search-cx');
    config()->set('external-apis.dataforseo.username', 'test-user');
    config()->set('external-apis.dataforseo.password', 'test-pass');

    $connector = instantiateClass($connectorClass);

    expect($connector)->toBeInstanceOf(Connector::class);
    expect($connector->resolveBaseUrl())->toBeString();

    invokeConnectorOptionalMethod($connector, 'defaultHeaders');
    invokeConnectorOptionalMethod($connector, 'defaultConfig');
    invokeConnectorOptionalMethod($connector, 'defaultQuery');
    invokeConnectorOptionalMethod($connector, 'defaultAuth');
})->with('integration connector classes');

function invokeConnectorOptionalMethod(object $instance, string $methodName): void
{
    if (! method_exists($instance, $methodName)) {
        return;
    }

    $method = new \ReflectionMethod($instance, $methodName);
    $method->setAccessible(true);
    $result = $method->invoke($instance);

    if (in_array($methodName, ['defaultHeaders', 'defaultConfig', 'defaultQuery'], true)) {
        expect($result)->toBeArray();

        return;
    }

    if ($methodName === 'defaultAuth') {
        return;
    }
}
