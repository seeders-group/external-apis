<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Serp;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Data\DataForSeo\Serp\GoogleNewsLiveRequestData;

class GoogleNewsLiveRequest extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(public GoogleNewsLiveRequestData $data) {}

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/serp/google/news/live/advanced';
    }

    protected function defaultBody(): array
    {
        return [
            $this->data->toArray(),
        ];
    }
}
