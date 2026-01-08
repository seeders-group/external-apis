<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\TeamleaderOrbit;

use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Contracts\TeamleaderOrbitOAuthServiceInterface;

class TeamleaderOrbitConnector extends Connector
{
    use AcceptsJson;

    protected TeamleaderOrbitOAuthServiceInterface $oauthService;

    public function __construct(TeamleaderOrbitOAuthServiceInterface $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    protected function defaultAuth(): HeaderAuthenticator
    {
        $accessToken = $this->oauthService->getValidAccessToken();

        return new HeaderAuthenticator('Bearer '.$accessToken, 'Authorization');
    }

    public function resolveBaseUrl(): string
    {
        return config('external-apis.teamleader_orbit.base_url', 'https://api.yadera.com/');
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
