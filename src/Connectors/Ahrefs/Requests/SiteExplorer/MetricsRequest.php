<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class MetricsRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(public readonly string $domain) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/site-explorer/metrics';
    }

    protected function defaultQuery(): array
    {
        return [
            'target' => $this->domain,
            'date' => now()->subDay()->format('Y-m-d'),
        ];
    }
}
