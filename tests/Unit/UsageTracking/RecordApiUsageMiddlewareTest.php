<?php

declare(strict_types=1);

use Saloon\Http\Response;
use Saloon\Http\PendingRequest;
use Saloon\Repositories\ArrayStore;
use Seeders\ExternalApis\UsageTracking\Middleware\RecordApiUsage;

afterEach(function (): void {
    \Mockery::close();
});

it('returns early when usage tracking is disabled', function (): void {
    config()->set('external-apis.usage_tracking.enabled', false);

    $middleware = new TestRecordApiUsageMiddleware;
    $response = \Mockery::mock(Response::class);

    expect(fn () => $middleware->__invoke($response))->not->toThrow(\Throwable::class);
});

it('extracts expected consumption when value is zero', function (): void {
    $middleware = new TestRecordApiUsageMiddleware;

    $response = \Mockery::mock(Response::class);
    $pendingRequest = \Mockery::mock(PendingRequest::class);
    $headers = new ArrayStore([
        'X-Seeders-Expected-Consumption' => '0',
    ]);

    $response->shouldReceive('getPendingRequest')->andReturn($pendingRequest);
    $response->shouldReceive('successful')->andReturn(true);

    $pendingRequest->shouldReceive('headers')->andReturn($headers);

    expect($middleware->runExtractConsumption($response))->toBe([
        'value' => 0.0,
        'type' => 'units',
    ]);
});

it('extracts zero cost and zero tokens correctly', function (): void {
    $middleware = new TestRecordApiUsageMiddleware;

    $responseWithCost = \Mockery::mock(Response::class);
    $pendingRequestForCost = \Mockery::mock(PendingRequest::class);
    $headersForCost = new ArrayStore();

    $responseWithCost->shouldReceive('getPendingRequest')->andReturn($pendingRequestForCost);
    $pendingRequestForCost->shouldReceive('headers')->andReturn($headersForCost);
    $responseWithCost->shouldReceive('header')->with('x-api-units-cost-total-actual')->andReturn('');
    $responseWithCost->shouldReceive('json')->with('cost')->andReturn(0);

    expect($middleware->runExtractConsumption($responseWithCost))->toBe([
        'value' => 0.0,
        'type' => 'dollars',
    ]);

    $responseWithTokens = \Mockery::mock(Response::class);
    $pendingRequestForTokens = \Mockery::mock(PendingRequest::class);
    $headersForTokens = new ArrayStore();

    $responseWithTokens->shouldReceive('getPendingRequest')->andReturn($pendingRequestForTokens);
    $pendingRequestForTokens->shouldReceive('headers')->andReturn($headersForTokens);
    $responseWithTokens->shouldReceive('header')->with('x-api-units-cost-total-actual')->andReturn(null);
    $responseWithTokens->shouldReceive('json')->with('cost')->andReturn(null);
    $responseWithTokens->shouldReceive('json')->with('usage.total_tokens')->andReturn(0);

    expect($middleware->runExtractConsumption($responseWithTokens))->toBe([
        'value' => 0.0,
        'type' => 'tokens',
    ]);
});

it('extracts metadata units with zero value and error payloads', function (): void {
    $middleware = new TestRecordApiUsageMiddleware;

    $response = \Mockery::mock(Response::class);
    $response->shouldReceive('json')->with('usage')->andReturn(['total_tokens' => 10]);
    $response->shouldReceive('header')->with('x-api-units-cost-total-actual')->andReturn('0');
    $response->shouldReceive('header')->with('x-api-units-limit-reset')->andReturn('2026-02-13T12:00:00Z');
    $response->shouldReceive('successful')->andReturn(false);
    $response->shouldReceive('json')->with('error')->andReturn(['message' => 'failed']);

    expect($middleware->runExtractMetadata($response))->toBe([
        'usage' => ['total_tokens' => 10],
        'units' => [
            'actual' => 0,
            'limit_reset' => '2026-02-13T12:00:00Z',
        ],
        'error' => ['message' => 'failed'],
    ]);
});

it('falls back to request counting when no consumption signals are present', function (): void {
    $middleware = new TestRecordApiUsageMiddleware;

    $response = \Mockery::mock(Response::class);
    $pendingRequest = \Mockery::mock(PendingRequest::class);
    $headers = new ArrayStore();

    $response->shouldReceive('getPendingRequest')->andReturn($pendingRequest);
    $pendingRequest->shouldReceive('headers')->andReturn($headers);
    $response->shouldReceive('header')->with('x-api-units-cost-total-actual')->andReturn('');
    $response->shouldReceive('json')->with('cost')->andReturn(null);
    $response->shouldReceive('json')->with('usage.total_tokens')->andReturn(null);

    expect($middleware->runExtractConsumption($response))->toBe([
        'value' => 1,
        'type' => 'requests',
    ]);
});

class TestRecordApiUsageMiddleware extends RecordApiUsage
{
    public function runExtractConsumption(Response $response): array
    {
        return $this->extractConsumption($response);
    }

    public function runExtractMetadata(Response $response): ?array
    {
        return $this->extractMetadata($response);
    }
}
