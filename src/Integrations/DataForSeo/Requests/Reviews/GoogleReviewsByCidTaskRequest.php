<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\Reviews;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Reviews\GoogleReviewsByCidRequestData;

class GoogleReviewsByCidTaskRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public GoogleReviewsByCidRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/business_data/google/reviews/task_post';
    }

    protected function defaultBody(): array
    {
        return [
            [
                'cid' => $this->data->cid,
                'location_name' => $this->data->location_name,
                'language_name' => $this->data->language_name,
                'depth' => $this->data->depth,
                'sort_by' => $this->data->sort_by,
                'pingback_url' => $this->data->pingback_url,
            ],
        ];
    }
}
