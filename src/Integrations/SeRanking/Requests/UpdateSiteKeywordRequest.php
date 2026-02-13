<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateSiteKeywordRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public int $site_id,
        public int $keyword_id,
        public string $keyword,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/sites/{$this->site_id}/keywords/{$this->keyword_id}";
    }

    protected function defaultBody(): array
    {
        return [
            'keyword' => $this->keyword,
        ];
    }
}
