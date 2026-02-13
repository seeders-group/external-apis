<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateKeywordGroupRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public int $site_id,
        public string $name,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/keyword-groups';
    }

    public function defaultBody(): array
    {
        return [
            'name' => $this->name,
            'site_id' => $this->site_id,
        ];
    }
}
