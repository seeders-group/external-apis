<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;
use Seeders\ExternalApis\Integrations\Semrush\Data\BacklinksOverviewRequestData;
use Seeders\ExternalApis\Integrations\Semrush\Data\BacklinksOverviewResponseData;
use Seeders\ExternalApis\Integrations\Semrush\Support\SemrushCsvParser;

class BacklinksOverviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly BacklinksOverviewRequestData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/analytics/v1/';
    }

    protected function defaultQuery(): array
    {
        $apiKey = config('external-apis.semrush.api_key');

        if (empty($apiKey)) {
            throw new MissingConfigurationException('external-apis.semrush.api_key');
        }

        return [
            'type' => 'backlinks_overview',
            'target' => $this->data->target,
            'target_type' => $this->data->targetType,
            'export_columns' => $this->data->exportColumns,
            'key' => $apiKey,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $parsed = SemrushCsvParser::parse($response->body());

        return new BacklinksOverviewResponseData(
            headers: $parsed['headers'],
            rows: $parsed['rows'],
            rowCount: $parsed['rowCount'],
        );
    }
}
