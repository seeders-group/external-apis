<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\AdvancedWebRanking\Requests\Project;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetProjectDetailsRequest extends Request
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
        return '/example';
    }
}
