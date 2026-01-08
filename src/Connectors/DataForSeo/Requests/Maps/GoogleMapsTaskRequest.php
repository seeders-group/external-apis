<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Maps;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Data\DataForSeo\Maps\GoogleMapsRequestData;

class GoogleMapsTaskRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public GoogleMapsRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/v3/business_data/google/search/task_post';
    }

    protected function defaultBody(): array
    {
        return [
            [
                'keyword' => $this->data->keyword,
                'location_name' => $this->data->location_name,
                'language_code' => $this->data->language_code,
                'limit' => $this->data->limit,
                'pingback_url' => $this->data->pingback_url,
            ],
        ];
    }
}
