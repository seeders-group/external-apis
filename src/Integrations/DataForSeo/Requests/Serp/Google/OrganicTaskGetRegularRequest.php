<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\Serp\Google;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class OrganicTaskGetRegularRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $taskId) {}

    public function resolveEndpoint(): string
    {
        return "/serp/google/organic/task_get/regular/{$this->taskId}";
    }
}
