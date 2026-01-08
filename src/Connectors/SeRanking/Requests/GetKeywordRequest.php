<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\SeRanking\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetKeywordRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public int $site_id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/sites/'.$this->site_id.'/keywords';
    }
}
