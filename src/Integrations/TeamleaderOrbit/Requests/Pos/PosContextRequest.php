<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Pos;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Undocumented but functional endpoint (verified Sept 2026): returns the
 * lookup data needed for pos.set — currencies, legalentities, finaccounts
 * and folders.
 */
class PosContextRequest extends Request
{
    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/pos.context';
    }
}
