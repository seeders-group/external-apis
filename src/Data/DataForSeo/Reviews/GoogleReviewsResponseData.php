<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Reviews;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class GoogleReviewsResponseData extends Data
{
    public function __construct(
        public string $type,
        public int $rank_group,
        public int $rank_absolute,
        public string $position,
        public ?string $xpath,
        public ?string $domain,
        public ?string $title,
        public ?string $url,
        #[WithCast(RatingCast::class)]
        public ?float $rating,
        public ?string $review_text,
        public ?string $review_images,
        public ?string $user_name,
        public ?string $user_url,
        public ?string $user_image,
        public ?string $review_date,
        public ?string $review_datetime,
        public ?int $responses_count,
        public ?string $review_id,
    ) {}
}

class RatingCast implements Cast, Castable
{
    public static function dataCastUsing(...$arguments): Cast
    {
        return new self;
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?float
    {
        if (is_array($value) && isset($value['value'])) {
            return (float) $value['value'];
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }
}
