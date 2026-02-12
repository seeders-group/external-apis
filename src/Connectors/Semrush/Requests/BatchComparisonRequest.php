<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Semrush\Requests;

use InvalidArgumentException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Connectors\Semrush\Support\SemrushCsvParser;
use Seeders\ExternalApis\Data\Semrush\BatchComparisonResponseData;

class BatchComparisonRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  array<string>  $targets
     * @param  array<string>  $targetTypes
     */
    public function __construct(
        public array $targets,
        public array $targetTypes,
        public string $database,
        public string $exportColumns,
        public ?int $displayLimit = null,
        public ?int $displayOffset = null,
    ) {
        if ($this->targets === []) {
            throw new InvalidArgumentException('Targets array cannot be empty.');
        }

        if (count($this->targets) !== count($this->targetTypes)) {
            throw new InvalidArgumentException('Targets and target types must have the same number of items.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/analytics/v1/';
    }

    protected function defaultQuery(): array
    {
        $query = [
            'type' => 'backlinks_comparison',
            'targets' => implode(',', $this->targets),
            'target_types' => implode(',', $this->targetTypes),
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

        return new BatchComparisonResponseData(
            headers: $parsed['headers'],
            rows: $parsed['rows'],
            rowCount: $parsed['rowCount'],
        );
    }
}
