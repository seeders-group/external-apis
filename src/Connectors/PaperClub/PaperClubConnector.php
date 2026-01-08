<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\PaperClub;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class PaperClubConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://app.paper.club/api/public';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'X-PaperClub-API-Key' => config('external-apis.paperclub.auth_header'),
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
