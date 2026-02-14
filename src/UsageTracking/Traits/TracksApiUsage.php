<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Traits;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Saloon\Http\PendingRequest;
use Seeders\ExternalApis\UsageTracking\Middleware\RecordApiUsage;

/** @phpstan-consistent-constructor */
trait TracksApiUsage
{
    protected ?Model $trackableModel = null;

    protected ?string $trackingScope = null;

    protected bool $trackingEnabled = false;

    /** @var array<int, true> */
    private array $trackingBootedRequestIds = [];

    /**
     * Create a connector instance with model tracking context.
     */
    public static function forModel(Model $model, ?string $scope = null): static
    {
        $instance = new static;
        $instance->trackableModel = $model;
        $instance->trackingScope = $scope;
        $instance->trackingEnabled = true;

        return $instance;
    }

    /**
     * Create a connector instance with scope-only tracking.
     */
    public static function forScope(string $scope): static
    {
        $instance = new static;
        $instance->trackingScope = $scope;
        $instance->trackingEnabled = true;

        return $instance;
    }

    /**
     * Set tracking context on an existing connector instance.
     * Used for connectors that require constructor parameters.
     */
    public function withTracking(Model $model, ?string $scope = null): static
    {
        $this->trackableModel = $model;
        $this->trackingScope = $scope;
        $this->trackingEnabled = true;

        return $this;
    }

    /**
     * Set scope-only tracking on an existing connector instance.
     * Used for connectors that require constructor parameters but don't have a model.
     */
    public function withScope(string $scope): static
    {
        $this->trackingScope = $scope;
        $this->trackingEnabled = true;

        return $this;
    }

    /**
     * Boot method named bootTracksApiUsage to follow Saloon's plugin naming convention.
     */
    public function bootTracksApiUsage(PendingRequest $pendingRequest): void
    {
        if (! config('external-apis.usage_tracking.enabled', true)) {
            return;
        }

        $requestId = spl_object_id($pendingRequest);

        // Guard against double-booting when a wrapper trait uses this trait
        // (Saloon's class_uses_recursive finds both traits with the same base name)
        if (isset($this->trackingBootedRequestIds[$requestId])) {
            return;
        }

        if (! $this->trackingEnabled) {
            throw new RuntimeException(
                sprintf(
                    'API Call to %s requires tracking context. Use %s::forModel($model, $scope) or %s::forScope($scope) instead of new %s().',
                    static::class,
                    static::class,
                    static::class,
                    static::class
                )
            );
        }

        $this->trackingBootedRequestIds[$requestId] = true;

        if ($this->trackableModel !== null) {
            $pendingRequest->headers()->add('X-Seeders-Model-Type', $this->trackableModel->getMorphClass());
            $pendingRequest->headers()->add('X-Seeders-Model-Id', (string) $this->trackableModel->getKey());
        }

        if ($this->trackingScope !== null) {
            $pendingRequest->headers()->add('X-Seeders-Scope', $this->trackingScope);
        }

        $pendingRequest->middleware()->onResponse(new RecordApiUsage);
    }

    /**
     * Get integration name for logging.
     * Must be implemented by each connector.
     */
    abstract public function getIntegrationName(): string;
}
