<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TreeNation;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class TreeNationConnector extends Connector
{
    use AcceptsJson;

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator(config('external-apis.tree_nation.token'));
    }

    public function resolveBaseUrl(): string
    {
        return config('external-apis.tree_nation.endpoint');
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
