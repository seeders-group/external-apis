<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\Serp\Google;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class OrganicTasksFixedRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/serp/google/organic/tasks_fixed';
    }
}
