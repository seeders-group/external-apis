<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;

/**
 * @method static SemrushConnector withScope(string $scope)
 * @method static SemrushConnector withTracking(Model $model, string|null $scope = null)
 * @method static Response send(Request $request, MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Integrations\Semrush\SemrushConnector
 */
final class Semrush extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SemrushConnector::class;
    }
}
