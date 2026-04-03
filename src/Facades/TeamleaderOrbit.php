<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
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
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitService;

/**
 * @method static TeamleaderOrbitService authenticate(OAuthAuthenticator $authenticator)
 * @method static AssetsResource assets()
 * @method static CompaniesResource companies()
 * @method static ContactsResource contacts()
 * @method static ContractsResource contracts()
 * @method static DealsResource deals()
 * @method static EntitiesResource entities()
 * @method static ExpensesResource expenses()
 * @method static OffersResource offers()
 * @method static PosResource pos()
 * @method static UsersResource users()
 *
 * @see TeamleaderOrbitService
 */
final class TeamleaderOrbit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TeamleaderOrbitService::class;
    }
}
