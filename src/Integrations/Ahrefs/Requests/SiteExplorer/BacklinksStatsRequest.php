<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Carbon\CarbonInterface;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class BacklinksStatsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $domain, public ?CarbonInterface $date = null) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/backlinks-stats';
    }

    protected function defaultQuery(): array
    {
        return [
            'target' => $this->domain,
            'date' => $this->date instanceof CarbonInterface ?
                $this->date->format('Y-m-d') : now()->subDay()->format('Y-m-d'),
        ];
    }
}
