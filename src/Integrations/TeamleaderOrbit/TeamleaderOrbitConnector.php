<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit;

use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Repositories\Body\JsonBodyRepository;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;

class TeamleaderOrbitConnector extends Connector
{
    use AcceptsJson;
    use AuthorizationCodeGrant;

    public function resolveBaseUrl(): string
    {
        return config('external-apis.teamleader_orbit.base_url', 'https://api.orbit.teamleader.eu/');
    }

    /**
     * The TLO API requires JSON objects, not arrays. When the request body is
     * empty, json_encode([]) produces "[]" which the API rejects. This forces
     * empty bodies to serialize as "{}".
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        $body = $pendingRequest->body();

        if ($body instanceof JsonBodyRepository && empty($body->all())) {
            $body->setJsonFlags(JSON_FORCE_OBJECT | JSON_THROW_ON_ERROR);
        }
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId(config('external-apis.teamleader_orbit.client_id'))
            ->setClientSecret(config('external-apis.teamleader_orbit.client_secret'))
            ->setRedirectUri(config('external-apis.teamleader_orbit.redirect_uri'))
            ->setAuthorizeEndpoint(config('external-apis.teamleader_orbit.authorize_url'))
            ->setTokenEndpoint(config('external-apis.teamleader_orbit.token_url'));
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
