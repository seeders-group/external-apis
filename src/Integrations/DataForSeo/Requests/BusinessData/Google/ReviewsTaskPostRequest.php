<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\BusinessData\Google;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\BusinessData\Google\ReviewsTaskPostData;

class ReviewsTaskPostRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected ReviewsTaskPostData $data) {}

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
