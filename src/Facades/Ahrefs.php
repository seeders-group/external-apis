<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Saloon\Http\Response;
use Saloon\Http\Request;
use Saloon\Http\Faking\MockClient;
use Illuminate\Support\Facades\Facade;
use Seeders\ExternalApis\Connectors\Ahrefs\AhrefsConnector;

/**
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Connectors\Ahrefs\AhrefsConnector
 */
final class Ahrefs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AhrefsConnector::class;
    }
}
