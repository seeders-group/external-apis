<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Requests;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\RequestInterface;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;
use Seeders\ExternalApis\Integrations\Semrush\Data\BatchComparisonRequestData;
use Seeders\ExternalApis\Integrations\Semrush\Data\BatchComparisonResponseData;
use Seeders\ExternalApis\Integrations\Semrush\Data\BatchComparisonTargetData;
use Seeders\ExternalApis\Integrations\Semrush\Support\SemrushCsvParser;

class BatchComparisonRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @var array<int, string>
     */
    public array $targets;

    /**
     * @var array<int, string>
     */
    public array $targetTypes;

    public function __construct(
        public readonly BatchComparisonRequestData $data,
    ) {
        if ($data->targets === []) {
            throw new InvalidArgumentException('Targets array cannot be empty.');
        }

        $targets = [];
        $targetTypes = [];

        foreach ($data->targets as $target) {
            if ($target instanceof BatchComparisonTargetData) {
                $targets[] = $target->target;
                $targetTypes[] = $target->targetType;

                continue;
            }

            $targets[] = $target;
            $targetTypes[] = 'root_domain';
        }

        $this->targets = $targets;
        $this->targetTypes = $targetTypes;
    }

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
            'type' => 'backlinks_comparison',
            'targets' => $this->targets,
            'target_types' => $this->targetTypes,
            'export_columns' => $this->data->exportColumns,
            'key' => $apiKey,
        ];
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

    #[Override]
    public function handlePsrRequest(RequestInterface $request, PendingRequest $pendingRequest): RequestInterface
    {
        $uri = $request->getUri();
        $query = $uri->getQuery();

        $query = preg_replace('/targets(?:%5B|\\[)\\d+(?:%5D|\\])=/i', 'targets%5B%5D=', $query) ?? $query;
        $query = preg_replace('/target_types(?:%5B|\\[)\\d+(?:%5D|\\])=/i', 'target_types%5B%5D=', $query) ?? $query;

        return $request->withUri($uri->withQuery($query));
    }
}
