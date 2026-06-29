<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Carbon\CarbonInterface;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DomainRatingRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $domain,
        public ?CarbonInterface $date = null,
        public ?string $select = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/domain-rating';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'target' => $this->domain,
            'date' => $this->date instanceof CarbonInterface ?
                $this->date->format('Y-m-d') : now()->subDay()->format('Y-m-d'),
            'select' => $this->select,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
