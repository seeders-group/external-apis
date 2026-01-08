<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Serp\Google;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class OrganicTaskPostRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $data) {}

    public function resolveEndpoint(): string
    {
        return '/serp/google/organic/task_post';
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
