<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateSiteRequest extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(
        public string $url,
        public string $title,
    ) {}

    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/sites';
    }

    protected function defaultBody(): array
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
        ];
    }
}
