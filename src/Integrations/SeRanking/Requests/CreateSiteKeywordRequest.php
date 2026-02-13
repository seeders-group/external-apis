<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateSiteKeywordRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public int $site_id,
        public ?int $keyword_group_id,
        public array $keywords,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/sites/'.$this->site_id.'/keywords';
    }

    protected function defaultBody(): array
    {
        $keywords = [];
        foreach ($this->keywords as $keyword) {
            $keywords[] = [
                'keyword' => $keyword,
                'group_id' => $this->keyword_group_id,
            ];
        }

        return $keywords;
    }
}
