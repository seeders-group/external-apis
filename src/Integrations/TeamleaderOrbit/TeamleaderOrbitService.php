<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit;

use RuntimeException;
use Saloon\Contracts\OAuthAuthenticator;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\AssetsResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\CompaniesResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\ContactsResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\ContractsResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\DealsResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\EntitiesResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\ExpensesResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\OffersResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\PosResource;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources\UsersResource;

class TeamleaderOrbitService
{
    private ?TeamleaderOrbitConnector $connector = null;

    public function authenticate(OAuthAuthenticator $authenticator): self
    {
        $this->connector = resolve(TeamleaderOrbitConnector::class);
        $this->connector->authenticate($authenticator);

        return $this;
    }

    public function assets(): AssetsResource
    {
        return new AssetsResource($this->resolveConnector());
    }

    public function companies(): CompaniesResource
    {
        return new CompaniesResource($this->resolveConnector());
    }

    public function contacts(): ContactsResource
    {
        return new ContactsResource($this->resolveConnector());
    }

    public function contracts(): ContractsResource
    {
        return new ContractsResource($this->resolveConnector());
    }

    public function deals(): DealsResource
    {
        return new DealsResource($this->resolveConnector());
    }

    public function entities(): EntitiesResource
    {
        return new EntitiesResource($this->resolveConnector());
    }

    public function expenses(): ExpensesResource
    {
        return new ExpensesResource($this->resolveConnector());
    }

    public function offers(): OffersResource
    {
        return new OffersResource($this->resolveConnector());
    }

    public function pos(): PosResource
    {
        return new PosResource($this->resolveConnector());
    }

    public function users(): UsersResource
    {
        return new UsersResource($this->resolveConnector());
    }

    private function resolveConnector(): TeamleaderOrbitConnector
    {
        if (! $this->connector instanceof TeamleaderOrbitConnector) {
            throw new RuntimeException('You must call authenticate() before accessing resources.');
        }

        return $this->connector;
    }
}
