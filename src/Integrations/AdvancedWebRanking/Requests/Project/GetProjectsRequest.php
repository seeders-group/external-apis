<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\AdvancedWebRanking\Requests\Project;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetProjectsRequest extends Request
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
