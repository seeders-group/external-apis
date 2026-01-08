<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\SeRanking\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetSystemSearchEnginesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/system/search-engines';
    }
}
