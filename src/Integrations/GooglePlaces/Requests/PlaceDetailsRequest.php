<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GooglePlaces\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class PlaceDetailsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $placeId,
        public string $fields = 'formatted_phone_number,opening_hours,url,website',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/maps/api/place/details/json';
    }

    protected function defaultQuery(): array
    {
        return [
            'place_id' => $this->placeId,
            'fields' => $this->fields,
        ];
    }
}
