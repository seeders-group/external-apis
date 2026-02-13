<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Prensalink;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class PrensalinkConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://shop.prensalink.com/api';
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
