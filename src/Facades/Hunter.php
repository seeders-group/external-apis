<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Hunter\HunterConnector;

/**
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Integrations\Hunter\HunterConnector
 */
final class Hunter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HunterConnector::class;
    }
}
