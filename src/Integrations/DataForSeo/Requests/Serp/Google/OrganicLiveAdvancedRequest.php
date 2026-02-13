<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\Serp\Google;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class OrganicLiveAdvancedRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $data) {}

    public function resolveEndpoint(): string
    {
        return '/serp/google/organic/live/advanced';
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
