<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AddSearchEngineRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public int $site_id) {}

    public function resolveEndpoint(): string
    {
        return "sites/$this->site_id/search-engines";
    }

    protected function defaultBody(): array
    {
        return [
            'search_engine_id' => 320,
        ];
    }
}
