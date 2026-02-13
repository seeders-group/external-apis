<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GoogleApis\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class PageSpeedOnlineRequest extends Request
{
    public string $apiKey;

    public function __construct(public string $url)
    {
        $this->apiKey = config('external-apis.google_pagespeed.key');
    }

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        $categories = implode('&', [
            'category=accessibility',
            'category=best-practices',
            'category=performance',
            'category=seo',
        ]);

        $query = http_build_query([
            'url' => $this->url,
            'key' => $this->apiKey,
            'strategy' => 'mobile',
        ]);

        return '/pagespeedonline/v5/runPagespeed?'.$query.'&'.$categories;
    }

    protected function defaultQuery(): array
    {
        return [];
    }
}
