<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\DataForSeo\DataForSeoConnector;

/**
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Integrations\DataForSeo\DataForSeoConnector
 */
final class DataForSeo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DataForSeoConnector::class;
    }
}
