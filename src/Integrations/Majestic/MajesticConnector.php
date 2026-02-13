<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Majestic;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class MajesticConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.majestic.com/api/json';
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
