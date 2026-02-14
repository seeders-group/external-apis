<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\DataForSeo\Data\BusinessData\Google\ReviewsTaskPostData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Maps\GoogleMapsResponseData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Reviews\GoogleReviewsByCidRequestData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Reviews\GoogleReviewsRequestData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Reviews\GoogleReviewsResponseData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google\OrganicResultData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google\OrganicTaskPostData;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google\OrganicTaskResponseData;

beforeEach(function (): void {
    config()->set('data', require dirname(__DIR__, 3).'/vendor/spatie/laravel-data/config/data.php');
});

it('builds helper request objects for reviews and cid', function (): void {
    $company = GoogleReviewsRequestData::forCompany('Seeders', 'Amsterdam', 25, 'https://example.test/webhook');
    $withCid = GoogleReviewsRequestData::forCompanyWithCid('Seeders', 'cid123', 'Amsterdam', 50);
    $cidOnly = GoogleReviewsByCidRequestData::forCid('cid789', 40);

    expect($company->keyword)->toBe('Seeders');
    expect($company->depth)->toBe(25);
    expect($withCid->cid)->toBe('cid123');
    expect($cidOnly->sort_by)->toBe('newest');
});

it('maps google maps results and finds best matches', function (): void {
    $items = [
        ['title' => 'Other Company'],
        [
            'title' => 'Seeders Amsterdam',
            'rating' => ['value' => 4.7],
            'coordinates' => ['latitude' => 52.37, 'longitude' => 4.89],
        ],
    ];

    $match = GoogleMapsResponseData::findBestMatch($items, 'seeders');
    $fallback = GoogleMapsResponseData::findBestMatch([['title' => 'Fallback']], 'missing');
    $none = GoogleMapsResponseData::findBestMatch([], 'none');

    expect($match?->title)->toBe('Seeders Amsterdam');
    expect($match?->rating)->toBe(4.7);
    expect($match?->coordinates)->toMatchArray(['lat' => 52.37, 'lng' => 4.89]);
    expect($fallback?->title)->toBe('Fallback');
    expect($none)->toBeNull();
});

it('supports mutable helper methods and pipeline preparation', function (): void {
    $reviewsPost = (new ReviewsTaskPostData(keyword: 'seeders'))->withPingbackUrl('https://example.test/reviews');
    $organicPost = (new OrganicTaskPostData(keyword: 'seeders', location_name: 'Amsterdam'))->withPingbackUrl('https://example.test/organic');

    $preparedOrganic = OrganicResultData::prepareForPipeline([
        'items' => [[
            'type' => 'organic',
            'rank_group' => 1,
            'rank_absolute' => 1,
            'page' => 1,
            'domain' => 'example.com',
            'title' => 'Example',
            'description' => 'Example description',
            'breadcrumb' => 'example.com',
            'url' => 'https://example.com',
        ]],
    ]);
    $preparedTaskResponse = OrganicTaskResponseData::prepareForPipeline([
        'tasks' => [[
            'id' => 'task-1',
            'status_code' => 20000,
            'status_message' => 'Ok.',
            'time' => '0.01 sec.',
            'cost' => 0.001,
            'result_count' => 0,
            'path' => ['v3', 'serp', 'google', 'organic'],
            'data' => [
                'api' => 'serp',
                'function' => 'task_get',
                'se' => 'google',
                'se_type' => 'organic',
                'keyword' => 'seeders',
                'location_name' => 'Amsterdam',
                'language_code' => 'en',
                'device' => 'desktop',
                'os' => 'windows',
            ],
            'result' => [],
        ]],
    ]);

    expect($reviewsPost->pingback_url)->toBe('https://example.test/reviews');
    expect($organicPost->pingback_url)->toBe('https://example.test/organic');
    expect($preparedOrganic['items'])->toBeArray()->not->toBeEmpty();
    expect($preparedTaskResponse['tasks'])->toBeArray()->not->toBeEmpty();
});

it('casts review ratings from array and scalar values', function (): void {
    $arrayRating = GoogleReviewsResponseData::from([
        'type' => 'reviews_element',
        'rank_group' => 1,
        'rank_absolute' => 1,
        'position' => 'left',
        'xpath' => null,
        'domain' => null,
        'title' => null,
        'url' => null,
        'rating' => ['value' => '4.5'],
        'review_text' => null,
        'review_images' => null,
        'user_name' => null,
        'user_url' => null,
        'user_image' => null,
        'review_date' => null,
        'review_datetime' => null,
        'responses_count' => null,
        'review_id' => null,
    ]);

    $numericRating = GoogleReviewsResponseData::from([
        'type' => 'reviews_element',
        'rank_group' => 1,
        'rank_absolute' => 1,
        'position' => 'left',
        'xpath' => null,
        'domain' => null,
        'title' => null,
        'url' => null,
        'rating' => 3.9,
        'review_text' => null,
        'review_images' => null,
        'user_name' => null,
        'user_url' => null,
        'user_image' => null,
        'review_date' => null,
        'review_datetime' => null,
        'responses_count' => null,
        'review_id' => null,
    ]);

    $invalidRating = GoogleReviewsResponseData::from([
        'type' => 'reviews_element',
        'rank_group' => 1,
        'rank_absolute' => 1,
        'position' => 'left',
        'xpath' => null,
        'domain' => null,
        'title' => null,
        'url' => null,
        'rating' => 'n/a',
        'review_text' => null,
        'review_images' => null,
        'user_name' => null,
        'user_url' => null,
        'user_image' => null,
        'review_date' => null,
        'review_datetime' => null,
        'responses_count' => null,
        'review_id' => null,
    ]);

    expect($arrayRating->rating)->toBe(4.5);
    expect($numericRating->rating)->toBe(3.9);
    expect($invalidRating->rating)->toBeNull();
});
