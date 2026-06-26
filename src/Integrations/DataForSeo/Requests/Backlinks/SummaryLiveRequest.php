<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\Backlinks;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SummaryLiveRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, array<string, mixed>>  $data
     */
    public function __construct(protected array $data) {}

    public function resolveEndpoint(): string
    {
        return '/backlinks/summary/live';
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
