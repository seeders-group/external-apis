<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Saloon\Http\PendingRequest;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

it('configures tracking context through helper methods', function (): void {
    $prototype = makeTrackingConnector();
    $connectorClass = $prototype::class;

    $model = new class extends Model
    {
        protected $table = 'tests';
    };

    $forModel = new \ReflectionMethod($connectorClass, 'forModel');
    $forScope = new \ReflectionMethod($connectorClass, 'forScope');

    /** @var object $connectorFromModel */
    $connectorFromModel = $forModel->invoke(null, $model, 'scope-a');
    /** @var object $connectorFromScope */
    $connectorFromScope = $forScope->invoke(null, 'scope-b');

    $connector = makeTrackingConnector();
    $connector->withTracking($model, 'scope-c')->withScope('scope-d');

    $modelTrackingEnabled = \Closure::bind(fn (): bool => $this->trackingEnabled, $connectorFromModel, $connectorClass)();
    $scopeTrackingEnabled = \Closure::bind(fn (): bool => $this->trackingEnabled, $connectorFromScope, $connectorClass)();
    $connectorTrackingEnabled = \Closure::bind(fn (): bool => $this->trackingEnabled, $connector, $connectorClass)();

    test()->assertTrue($modelTrackingEnabled);
    test()->assertTrue($scopeTrackingEnabled);
    test()->assertTrue($connectorTrackingEnabled);
});

it('guards against missing tracking context and double-booting', function (): void {
    $connector = makeTrackingConnector();
    $pendingRequest = \Mockery::mock(PendingRequest::class);

    expect(fn () => $connector->bootTracksApiUsage($pendingRequest))
        ->toThrow(\RuntimeException::class, 'requires tracking context');

    expect(fn () => $connector->bootTracksApiUsage($pendingRequest))
        ->toThrow(\RuntimeException::class, 'requires tracking context');
});

it('skips tracking guard when usage tracking is disabled', function (): void {
    config()->set('external-apis.usage_tracking.enabled', false);

    $connector = makeTrackingConnector();
    $pendingRequest = \Mockery::mock(PendingRequest::class);

    expect(fn () => $connector->bootTracksApiUsage($pendingRequest))->not->toThrow(\Throwable::class);
});

function makeTrackingConnector(): object
{
    return new class
    {
        use TracksApiUsage;

        public function getIntegrationName(): string
        {
            return 'test';
        }
    };
}
