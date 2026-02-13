<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\AdvancedWebRanking;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class AdvancedWebRankingConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.awrcloud.com/v2/get.php';
    }

    protected function defaultAuth(): ?Authenticator
    {
        $token = config('external-apis.advanced_web_ranking.token');

        return new TokenAuthenticator($token);
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
