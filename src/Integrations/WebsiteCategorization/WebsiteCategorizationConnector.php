<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\WebsiteCategorization;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class WebsiteCategorizationConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.websitecategorizationapi.com/api/iab/iab_web_content_filtering.php';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
