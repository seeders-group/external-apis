<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\DataForSeo\Data\BusinessData\Trustpilot\ReviewsTaskPostData;
use Seeders\ExternalApis\Integrations\DataForSeo\Requests\BusinessData\Trustpilot\ReviewsTaskGetRequest;
use Seeders\ExternalApis\Integrations\DataForSeo\Requests\BusinessData\Trustpilot\ReviewsTaskPostRequest;

it('builds the Trustpilot reviews task_post endpoint from the domain payload', function (): void {
    $data = new ReviewsTaskPostData(
        domain: 'spejlshoppen.dk',
        depth: 10,
        priority: 2,
    );

    $request = new ReviewsTaskPostRequest($data);

    expect($request->resolveEndpoint())->toBe('/business_data/trustpilot/reviews/task_post')
        ->and($data->domain)->toBe('spejlshoppen.dk')
        ->and($data->depth)->toBe(10)
        ->and($data->priority)->toBe(2)
        ->and($data->sort_by)->toBe('recency');
});

it('builds the Trustpilot reviews task_get endpoint with the task id', function (): void {
    $request = new ReviewsTaskGetRequest('task-123');

    expect($request->resolveEndpoint())->toBe('/business_data/trustpilot/reviews/task_get/task-123');
});
