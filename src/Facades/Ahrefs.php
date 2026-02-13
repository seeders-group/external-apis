<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Ahrefs\AhrefsConnector;

/**
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Integrations\Ahrefs\AhrefsConnector
 */
final class Ahrefs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AhrefsConnector::class;
    }
}
