<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\ScraperAPI\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ScrapeRequest extends Request
{
    public function __construct(public string $url) {}

    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '';
    }

    protected function defaultQuery(): array
    {
        return [
            'api_key' => config('external-apis.scraperapi.key'),
            'url' => $this->url,
        ];
    }
}
