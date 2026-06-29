<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Semrush\Data\BacklinksOverviewRequestData;
use Seeders\ExternalApis\Integrations\Semrush\Data\BatchComparisonRequestData;
use Seeders\ExternalApis\Integrations\Semrush\Data\BatchComparisonTargetData;
use Seeders\ExternalApis\Integrations\Semrush\Requests\ApiUnitsBalanceRequest;
use Seeders\ExternalApis\Integrations\Semrush\Requests\BacklinksOverviewRequest;
use Seeders\ExternalApis\Integrations\Semrush\Requests\BatchComparisonRequest;
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;
use Seeders\ExternalApis\UsageTracking\Models\ApiLog;

beforeEach(function (): void {
    Schema::dropIfExists('api_logs');

    Schema::create('api_logs', function (Blueprint $table): void {
        $table->id();
        $table->nullableMorphs('trackable');
        $table->string('scope')->nullable();
        $table->string('integration');
        $table->string('endpoint');
        $table->integer('status')->default(200);
        $table->decimal('consumption', 12, 6)->default(0);
        $table->string('consumption_type')->nullable();
        $table->integer('latency_ms')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
});

afterEach(function (): void {
    Mockery::close();
});

it('requires tracking context for semrush requests', function (): void {
    $connector = new SemrushConnector;
    $connector->withMockClient(new MockClient([
        ApiUnitsBalanceRequest::class => MockResponse::make('1000', 200),
    ]));

    expect(fn (): Response => $connector->send(new ApiUnitsBalanceRequest))
        ->toThrow(RuntimeException::class, 'requires tracking context');
});

it('records api_log for backlinks overview', function (): void {
    $connector = SemrushConnector::forScope('semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make("domain;ascore;backlinks\nexample.com;12;100", 200),
    ]));

    $connector->send(new BacklinksOverviewRequest(
        data: new BacklinksOverviewRequestData(
            target: 'example.com',
            targetType: 'root_domain',
            exportColumns: 'ascore,total,domains_num',
        ),
    ));

    $apiLog = ApiLog::query()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('semrush');
    expect($apiLog->consumption_type)->toBe('units');
    expect((float) $apiLog->consumption)->toBe(40.0);
});

it('records trackable model metadata in api_logs when using forModel', function (): void {
    $model = new SemrushTrackableModel;
    $model->setAttribute($model->getKeyName(), 42);
    $model->exists = true;

    $connector = SemrushConnector::forModel($model, 'semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make("domain;ascore;backlinks\nexample.com;12;100", 200),
    ]));

    $connector->send(new BacklinksOverviewRequest(
        data: new BacklinksOverviewRequestData(
            target: 'example.com',
            targetType: 'root_domain',
            exportColumns: 'ascore,total,domains_num',
        ),
    ));

    $apiLog = ApiLog::query()->latest()->first();

    expect($apiLog->trackable_type)->toBe($model->getMorphClass());
    expect((int) $apiLog->trackable_id)->toBe(42);
    expect($apiLog->scope)->toBe('semrush_tracking_test');
});

it('records batch comparison units as 40 per target domain', function (): void {
    $connector = SemrushConnector::forScope('semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        BatchComparisonRequest::class => MockResponse::make("target;metric\nexample.com;10", 200),
    ]));

    $connector->send(new BatchComparisonRequest(
        data: new BatchComparisonRequestData(
            targets: [
                new BatchComparisonTargetData(target: 'example.com'),
                new BatchComparisonTargetData(target: 'example.org'),
                new BatchComparisonTargetData(target: 'example.net'),
            ],
            exportColumns: 'target,ascore,total',
        ),
    ));

    $apiLog = ApiLog::query()->latest()->first();

    expect((float) $apiLog->consumption)->toBe(120.0);
});

it('logs balance request with zero units', function (): void {
    $connector = SemrushConnector::forScope('semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        ApiUnitsBalanceRequest::class => MockResponse::make('1000000', 200),
    ]));

    $connector->send(new ApiUnitsBalanceRequest);

    $apiLog = ApiLog::query()->latest()->first();

    expect((float) $apiLog->consumption)->toBe(0.0);
    expect($apiLog->consumption_type)->toBe('units');
});

it('logs failed semrush requests as zero units', function (): void {
    $connector = SemrushConnector::forScope('semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make('internal error', 500),
    ]));

    $connector->send(new BacklinksOverviewRequest(
        data: new BacklinksOverviewRequestData(
            target: 'example.com',
            targetType: 'root_domain',
            exportColumns: 'ascore,total,domains_num',
        ),
    ));

    $apiLog = ApiLog::query()->latest()->first();

    expect((float) $apiLog->consumption)->toBe(0.0);
    expect($apiLog->status)->toBe(500);
});

it('records semrush usage without any budget side effects', function (): void {
    $connector = SemrushConnector::forScope('semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make('ok', 200),
    ]));

    $connector->send(new BacklinksOverviewRequest(
        data: new BacklinksOverviewRequestData(
            target: 'example.com',
            targetType: 'root_domain',
            exportColumns: 'ascore,total,domains_num',
        ),
    ));

    expect(ApiLog::query()->count())->toBe(1);
});

it('fails fast for unsupported semrush requests', function (): void {
    $connector = SemrushConnector::forScope('semrush_tracking_test');
    $connector->withMockClient(new MockClient([
        UnsupportedSemrushRequest::class => MockResponse::make('ok', 200),
    ]));

    expect(fn (): Response => $connector->send(new UnsupportedSemrushRequest))
        ->toThrow(RuntimeException::class, 'Unsupported Semrush request class');
});

class UnsupportedSemrushRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/analytics/v1/';
    }

    protected function defaultQuery(): array
    {
        return [
            'type' => 'unsupported_type',
            'key' => 'test-semrush-key',
        ];
    }
}

class SemrushTrackableModel extends Model
{
    protected $table = 'projects';
}
