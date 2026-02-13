<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiServicePricing;

beforeEach(function (): void {
    Schema::dropIfExists('api_service_pricing');

    Schema::create('api_service_pricing', function (Blueprint $table): void {
        $table->id();
        $table->string('integration');
        $table->string('endpoint')->nullable();
        $table->decimal('cost_per_unit', 10, 6)->default(0);
        $table->string('unit_type')->default('api_units');
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
});

it('can create service pricing', function (): void {
    $pricing = ApiServicePricing::create([
        'integration' => 'ahrefs',
        'cost_per_unit' => 0.01,
        'unit_type' => 'api_units',
    ]);

    expect($pricing->integration)->toBe('ahrefs');
    expect((float) $pricing->cost_per_unit)->toBe(0.01);
});

it('gets pricing for an integration from database', function (): void {
    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'cost_per_unit' => 0.05,
        'unit_type' => 'api_units',
        'is_active' => true,
    ]);

    $pricing = ApiServicePricing::getPricing('ahrefs');

    expect($pricing)->not->toBeNull();
    expect($pricing['cost_per_unit'])->toBe(0.05);
    expect($pricing['unit_type'])->toBe('api_units');
});

it('gets endpoint-specific pricing', function (): void {
    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'endpoint' => '/v3/site-explorer/domain-rating',
        'cost_per_unit' => 0.10,
        'unit_type' => 'api_units',
        'is_active' => true,
    ]);

    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'cost_per_unit' => 0.01,
        'unit_type' => 'api_units',
        'is_active' => true,
    ]);

    $pricing = ApiServicePricing::getPricing('ahrefs', '/v3/site-explorer/domain-rating');

    expect($pricing['cost_per_unit'])->toBe(0.10);
});

it('falls back to default pricing when endpoint not found', function (): void {
    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'endpoint' => null,
        'cost_per_unit' => 0.01,
        'unit_type' => 'api_units',
        'is_active' => true,
    ]);

    $pricing = ApiServicePricing::getPricing('ahrefs', '/v3/unknown-endpoint');

    expect($pricing['cost_per_unit'])->toBe(0.01);
});

it('falls back to config pricing when no database record exists', function (): void {
    $pricing = ApiServicePricing::getPricing('semrush');

    expect($pricing)->not->toBeNull();
    expect($pricing['cost_per_unit'])->toBe(0.00005);
    expect($pricing['unit_type'])->toBe('api_units');
});

it('returns null for unknown integration with no config', function (): void {
    $pricing = ApiServicePricing::getPricing('nonexistent');

    expect($pricing)->toBeNull();
});

it('calculates cost from units', function (): void {
    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'cost_per_unit' => 0.01,
        'unit_type' => 'api_units',
        'is_active' => true,
    ]);

    $cost = ApiServicePricing::calculateCost('ahrefs', 100);

    expect($cost)->toBe(1.0);
});

it('returns zero cost for unknown integration', function (): void {
    $cost = ApiServicePricing::calculateCost('nonexistent', 100);

    expect($cost)->toBe(0.0);
});

it('ignores inactive pricing records', function (): void {
    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'cost_per_unit' => 999.0,
        'unit_type' => 'api_units',
        'is_active' => false,
    ]);

    // Should fall back to config since db record is inactive
    $pricing = ApiServicePricing::getPricing('ahrefs');

    expect($pricing['cost_per_unit'])->toBe(0.01);
});
