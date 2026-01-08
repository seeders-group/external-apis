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

final class QuestionGenerationResultData extends Data
{
    public function __construct(
        #[Required]
        /** @var DataCollection<QuestionData> */
        public DataCollection $questions,
        #[Required, IntegerType]
        public int $totalGenerated,
        #[Required, StringType]
        public string $status,
        #[Nullable, ArrayType]
        public ?array $categoryBreakdown = null,
        #[Nullable, ArrayType]
        public ?array $intentBreakdown = null,
        #[Nullable, ArrayType]
        public ?array $buyerJourneyMapping = null,
        #[Nullable, ArrayType]
        public ?array $priorityQuestions = null,
        #[Nullable, ArrayType]
        public ?array $contentOpportunities = null,
        #[Nullable, ArrayType]
        public ?array $insights = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $generatedAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): static
    {
        if (isset($data['questions']) && is_array($data['questions'])) {
            /** @phpstan-ignore-next-line */
            $data['questions'] = QuestionData::collection($data['questions']);
        }

        return parent::from($data);
    }
}
