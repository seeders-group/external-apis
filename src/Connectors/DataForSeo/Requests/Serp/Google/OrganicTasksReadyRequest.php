<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Serp\Google;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class OrganicTasksReadyRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/serp/google/organic/tasks_ready';
    }
}
