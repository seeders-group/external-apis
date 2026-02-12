<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Semrush\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Connectors\Semrush\Support\SemrushCsvParser;
use Seeders\ExternalApis\Data\Semrush\BacklinksOverviewResponseData;

class BacklinksOverviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $target,
        public string $targetType,
        public string $database,
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
        $query = [
            'type' => 'backlinks_overview',
            'target' => $this->target,
            'target_type' => $this->targetType,
            'database' => $this->database,
            'export_columns' => $this->exportColumns,
            'api_key' => config('external-apis.semrush.api_key'),
        ];

        if (! is_null($this->displayLimit)) {
            $query['display_limit'] = $this->displayLimit;
        }

        if (! is_null($this->displayOffset)) {
            $query['display_offset'] = $this->displayOffset;
        }

        return $query;
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
