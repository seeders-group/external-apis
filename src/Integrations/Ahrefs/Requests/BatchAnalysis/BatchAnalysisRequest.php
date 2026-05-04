<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\BatchAnalysis;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\BatchAnalysis\BatchAnalysisRequestData;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\BatchAnalysis\BatchAnalysisResponseData;

class BatchAnalysisRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public BatchAnalysisRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/batch-analysis/batch-analysis';
    }

    protected function defaultBody(): array
    {
        return array_filter($this->data->toArray(), fn ($value): bool => ! is_null($value));
    }

    public function createDtoFromResponse(Response $response): BatchAnalysisResponseData
    {
        return BatchAnalysisResponseData::from($response->json());
    }
}
