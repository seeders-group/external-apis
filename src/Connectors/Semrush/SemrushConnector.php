<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Semrush;

use Saloon\Http\Connector;

class SemrushConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.semrush.com';
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
