<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\AdvancedWebRanking\Requests\Project;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteProjectsRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/example';
    }
}
