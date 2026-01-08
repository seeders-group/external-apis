<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Ahrefs\Requests\SiteExplorer;

use Carbon\Carbon;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DomainRatingRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $domain, public ?Carbon $date = null) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/domain-rating';
    }

    protected function defaultQuery(): array
    {
        return [
            'target' => $this->domain,
            'date' => $this->date ?
                $this->date->format('Y-m-d') : now()->format('Y-m-d'),
        ];
    }
}
