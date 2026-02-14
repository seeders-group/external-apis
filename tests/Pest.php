<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Seeders\ExternalApis\Tests\TestCase;

require_once __DIR__.'/Support/reflection_helpers.php';

uses(TestCase::class)
    ->beforeEach(function (): void {
        Http::preventStrayRequests();
        Sleep::fake();

        $this->freezeTime();
    })
    ->in(__DIR__);
