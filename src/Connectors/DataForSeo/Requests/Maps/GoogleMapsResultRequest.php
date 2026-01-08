<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Maps;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GoogleMapsResultRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $taskId) {}

    public function resolveEndpoint(): string
    {
        return "/v3/business_data/google/search/task_get/{$this->taskId}";
    }
}
