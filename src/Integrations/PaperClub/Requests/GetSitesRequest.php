<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\PaperClub\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetSitesRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/sites';
    }
}
