<?php

declare(strict_types=1);

use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

it('includes the config key in the message', function (): void {
    $exception = new MissingConfigurationException('external-apis.ahrefs.token');

    expect($exception->getMessage())
        ->toContain('external-apis.ahrefs.token')
        ->toContain('environment variable');
});

it('is a runtime exception', function (): void {
    $exception = new MissingConfigurationException('some.key');

    expect($exception)->toBeInstanceOf(RuntimeException::class);
});
