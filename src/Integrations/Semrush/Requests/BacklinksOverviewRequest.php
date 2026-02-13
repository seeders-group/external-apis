<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Semrush\Support\SemrushCsvParser;
use Seeders\ExternalApis\Integrations\Semrush\Data\BacklinksOverviewResponseData;

class BacklinksOverviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $target,
        public string $targetType,
        public string $exportColumns,
        public ?int $displayLimit = null,
        public ?int $displayOffset = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/analytics/v1/';
    }

    protected function defaultQuery(): array
    {
        return [
            'type' => 'backlinks_overview',
            'target' => $this->target,
            'target_type' => $this->targetType,
            'export_columns' => $this->exportColumns,
            'key' => config('external-apis.semrush.api_key'),
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
