<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Reviews;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GoogleReviewsResultRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $taskId) {}

    public function resolveEndpoint(): string
    {
        return "/business_data/google/reviews/task_get/{$this->taskId}";
    }
}
