<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
use Seeders\ExternalApis\Connectors\Hunter\HunterConnector;

/**
 * @method static \Saloon\Http\Response send(\Saloon\Http\Request $request, \Saloon\Http\Faking\MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Connectors\Hunter\HunterConnector
 */
final class Hunter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HunterConnector::class;
    }
}
