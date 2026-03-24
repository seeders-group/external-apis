<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer\DomainRatingHistoryRequestData;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer\DomainRatingHistoryResponseData;
use Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer\DomainRatingHistoryRequest;
use Spatie\LaravelData\LaravelDataServiceProvider;

beforeEach(function (): void {
    $this->app->register(LaravelDataServiceProvider::class);
    config()->set('data.date_format', ['Y-m-d\TH:i:sP', 'Y-m-d']);
});

it('serializes request data to the expected query array', function (): void {
    $data = new DomainRatingHistoryRequestData(
        target: 'example.com',
        date_from: Date::parse('2024-01-01'),
    );

    expect($data->toArray())->toBe([
        'target' => 'example.com',
        'date_from' => '2024-01-01',
        'date_to' => null,
        'history_grouping' => 'monthly',
    ]);
});

it('serializes request data with date_to when provided', function (): void {
    $data = new DomainRatingHistoryRequestData(
        target: 'example.com',
        date_from: Date::parse('2024-01-01'),
        date_to: Date::parse('2024-06-30'),
        history_grouping: 'daily',
    );

    expect($data->toArray())->toBe([
        'target' => 'example.com',
        'date_from' => '2024-01-01',
        'date_to' => '2024-06-30',
        'history_grouping' => 'daily',
    ]);
});

it('constructs response data from a typical API response', function (): void {
    $response = DomainRatingHistoryResponseData::from([
        'date' => '2024-01-01',
        'domain_rating' => 45.3,
    ]);

    expect($response->date)->toBeInstanceOf(Carbon::class);
    expect($response->date->format('Y-m-d'))->toBe('2024-01-01');
    expect($response->domain_rating)->toBe(45.3);
});

it('resolves to the correct endpoint', function (): void {
    $data = new DomainRatingHistoryRequestData(
        target: 'example.com',
        date_from: Date::parse('2024-01-01'),
    );

    $request = new DomainRatingHistoryRequest($data);

    expect($request->resolveEndpoint())->toBe('/site-explorer/domain-rating-history');
});

it('produces the correct query parameters from its data object', function (): void {
    $data = new DomainRatingHistoryRequestData(
        target: 'example.com',
        date_from: Date::parse('2024-03-15'),
        history_grouping: 'weekly',
    );

    $request = new DomainRatingHistoryRequest($data);

    expect($request->query()->all())->toBe([
        'target' => 'example.com',
        'date_from' => '2024-03-15',
        'date_to' => null,
        'history_grouping' => 'weekly',
    ]);
});
