<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ereplace;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class EreplaceConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://crm.1ereplace.com/api';
    }

    protected function defaultHeaders(): array
    {
        return [];
    }

    protected function defaultConfig(): array
    {
        return [];
    }
}
