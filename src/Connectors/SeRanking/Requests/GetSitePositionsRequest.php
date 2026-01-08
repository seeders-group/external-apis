<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\SeRanking\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetSitePositionsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public int $site_id, public ?string $date_from, public ?string $date_to) {}

    public function resolveEndpoint(): string
    {
        return "/sites/{$this->site_id}/positions";
    }

    protected function defaultQuery(): array
    {
        return [
            'date_from' => $this->date_from ?: now()->subMonth()->format('Y-m-d'),
            'date_to' => $this->date_to ?: now()->format('Y-m-d'),
            'with_landing_pages' => true,
            'with_serp_features' => true,
        ];
    }
}
