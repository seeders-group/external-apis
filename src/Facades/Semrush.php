<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
use Seeders\ExternalApis\Connectors\Semrush\SemrushConnector;

/**
 * @method static \Seeders\ExternalApis\Connectors\Semrush\SemrushConnector withScope(string $scope)
 * @method static \Seeders\ExternalApis\Connectors\Semrush\SemrushConnector withTracking(\Illuminate\Database\Eloquent\Model $model, string|null $scope = null)
 * @method static \Saloon\Http\Response send(\Saloon\Http\Request $request, \Saloon\Http\Faking\MockClient|null $mockClient = null, callable|null $handleRetry = null)
 *
 * @see \Seeders\ExternalApis\Connectors\Semrush\SemrushConnector
 */
final class Semrush extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SemrushConnector::class;
    }
}
