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

final class TrackingSessionData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $sessionId,
        #[Required, StringType]
        public string $brandName,
        #[Required, StringType]
        public string $status,
        #[Nullable, ArrayType]
        public ?array $llmSources = null,
        #[Nullable, IntegerType]
        public ?int $totalQueries = null,
        #[Nullable, IntegerType]
        public ?int $mentionsFound = null,
        #[Nullable, ArrayType]
        public ?array $topCompetitors = null,
        #[Nullable]
        /** @var DataCollection<BrandMentionData>|null */
        public ?DataCollection $mentions = null,
        #[Nullable, ArrayType]
        public ?array $insights = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $startedAt = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $completedAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): static
    {
        if (isset($data['mentions']) && is_array($data['mentions'])) {
            /** @phpstan-ignore-next-line */
            $data['mentions'] = BrandMentionData::collection($data['mentions']);
        }

        return parent::from($data);
    }
}
