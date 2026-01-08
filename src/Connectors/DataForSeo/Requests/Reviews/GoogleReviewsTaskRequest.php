<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Reviews;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Data\DataForSeo\Reviews\GoogleReviewsRequestData;

class GoogleReviewsTaskRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public GoogleReviewsRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/business_data/google/reviews/task_post';
    }

    protected function defaultBody(): array
    {
        return [
            $this->data->toArray(),
        ];
    }
}
