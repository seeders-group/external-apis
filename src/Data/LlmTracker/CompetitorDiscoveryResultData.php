<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class CompetitorDiscoveryResultData extends Data
{
    public function __construct(
        #[Required]
        /** @var DataCollection<CompetitorData> */
        public DataCollection $competitors,
        #[Required, IntegerType]
        public int $totalFound,
        #[Required, StringType]
        public string $status,
        #[Nullable, ArrayType]
        public ?array $directCompetitors = null,
        #[Nullable, ArrayType]
        public ?array $indirectCompetitors = null,
        #[Nullable, ArrayType]
        public ?array $emergingCompetitors = null,
        #[Nullable, ArrayType]
        public ?array $marketLeaders = null,
        #[Nullable, ArrayType]
        public ?array $competitiveGaps = null,
        #[Nullable, ArrayType]
        public ?array $insights = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $discoveredAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): static
    {
        if (isset($data['competitors']) && is_array($data['competitors'])) {
            /** @phpstan-ignore-next-line */
            $data['competitors'] = CompetitorData::collection($data['competitors']);
        }

        return parent::from($data);
    }
}
