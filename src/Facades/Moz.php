<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Saloon\Http\Response;
use Saloon\Http\Request;
use Saloon\Http\Faking\MockClient;
use Illuminate\Support\Facades\Facade;
use Seeders\ExternalApis\Integrations\Moz\MozLinksConnector;

/**
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Integrations\Moz\MozLinksConnector
 */
final class Moz extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MozLinksConnector::class;
    }
}
