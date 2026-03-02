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

    public string $exportColumns;

    public ?int $displayLimit;

    public ?int $displayOffset;

    /**
     * @param  array<int, string|BatchComparisonTargetData>|BatchComparisonRequestData  $targets
     * @param  array<int, string>|null  $targetTypes
     */
    public function __construct(
        array|BatchComparisonRequestData $targets,
        ?array $targetTypes = null,
        ?string $exportColumns = null,
        ?int $displayLimit = null,
        ?int $displayOffset = null,
    ) {
        if ($targets instanceof BatchComparisonRequestData) {
            [$this->targets, $this->targetTypes] = $this->normalizeFromData($targets);
            $this->exportColumns = $exportColumns ?? $targets->exportColumns;
            $this->displayLimit = $displayLimit ?? $targets->displayLimit;
            $this->displayOffset = $displayOffset ?? $targets->displayOffset;

            return;
        }

        [$this->targets, $this->targetTypes] = $this->normalizeFromArrays($targets, $targetTypes);
        $this->exportColumns = $exportColumns ?? 'target,ascore,total';
        $this->displayLimit = $displayLimit;
        $this->displayOffset = $displayOffset;
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
            'export_columns' => $this->exportColumns,
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

    /**
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function normalizeFromData(BatchComparisonRequestData $data): array
    {
        if ($data->targets === []) {
            throw new InvalidArgumentException('Targets array cannot be empty.');
        }

        $targets = [];
        $targetTypes = [];

        foreach ($data->targets as $index => $target) {
            if ($target instanceof BatchComparisonTargetData) {
                $targets[] = $target->target;
                $targetTypes[] = $target->targetType;

                continue;
            }

            if (is_string($target)) {
                $targets[] = $target;
                $targetTypes[] = 'root_domain';

                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Target at index %d must be a string or an instance of %s.',
                    $index,
                    BatchComparisonTargetData::class
                )
            );
        }

        return [$targets, $targetTypes];
    }

    /**
     * @param  array<int, string|BatchComparisonTargetData>  $targets
     * @param  array<int, string>|null  $targetTypes
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function normalizeFromArrays(array $targets, ?array $targetTypes): array
    {
        if ($targets === []) {
            throw new InvalidArgumentException('Targets array cannot be empty.');
        }

        if ($targetTypes !== null) {
            if (count($targets) !== count($targetTypes)) {
                throw new InvalidArgumentException('Targets and target types must have the same number of items.');
            }

            return [
                array_map(
                    static function (string|BatchComparisonTargetData $target): string {
                        if (! is_string($target)) {
                            throw new InvalidArgumentException(
                                'When targetTypes are provided, targets must be an array of strings.'
                            );
                        }

                        return $target;
                    },
                    $targets
                ),
                $targetTypes,
            ];
        }

        $normalizedTargets = [];
        $normalizedTargetTypes = [];

        foreach ($targets as $target) {
            if ($target instanceof BatchComparisonTargetData) {
                $normalizedTargets[] = $target->target;
                $normalizedTargetTypes[] = $target->targetType;

                continue;
            }

            if (is_string($target)) {
                $normalizedTargets[] = $target;
                $normalizedTargetTypes[] = 'root_domain';

                continue;
            }

            throw new InvalidArgumentException('Targets must be strings or BatchComparisonTargetData instances.');
        }

        return [$normalizedTargets, $normalizedTargetTypes];
    }
}
