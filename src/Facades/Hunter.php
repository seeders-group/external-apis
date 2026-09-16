<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Hunter\HunterConnector;

/**
 * Hunter is a tracked connector, so calls must carry tracking context:
 * `Hunter::withScope('my_feature')->send($request)`. Sending straight off the
 * facade throws, as it does for Semrush.
 *
 * @method static HunterConnector withScope(string $scope)
 * @method static HunterConnector withTracking(Model $model, string|null $scope = null)
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see HunterConnector
 */
final class Hunter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HunterConnector::class;
    }
}
