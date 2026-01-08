<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Maps;

use Spatie\LaravelData\Data;

class GoogleMapsResponseData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?string $address = null,
        public ?string $phone = null,
        public ?string $website = null,
        public ?float $rating = null,
        public ?int $reviews_count = null,
        public ?string $cid = null,
        public ?string $place_id = null,
        public ?array $coordinates = null,
        public ?string $business_status = null,
        public ?array $categories = null,
        public ?array $operating_hours = null,
        public ?string $description = null,
    ) {}

    /**
     * Get the most relevant business from results
     */
    public static function findBestMatch(array $items, string $businessName): ?self
    {
        if (empty($items)) {
            return null;
        }

        // First try to find exact match
        foreach ($items as $item) {
            if (isset($item['title']) &&
                str_contains(strtolower($item['title']), strtolower($businessName))) {
                return self::fromArray($item);
            }
        }

        // Return first result if no exact match
        return self::fromArray($items[0]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            address: $data['address'] ?? null,
            phone: $data['phone'] ?? null,
            website: $data['website'] ?? null,
            rating: isset($data['rating']['value']) ? (float) $data['rating']['value'] : null,
            reviews_count: $data['reviews_count'] ?? null,
            cid: $data['cid'] ?? null,
            place_id: $data['place_id'] ?? null,
            coordinates: isset($data['coordinates']) ? [
                'lat' => $data['coordinates']['latitude'] ?? null,
                'lng' => $data['coordinates']['longitude'] ?? null,
            ] : null,
            business_status: $data['business_status'] ?? null,
            categories: $data['categories'] ?? null,
            operating_hours: $data['operating_hours'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
