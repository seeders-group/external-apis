<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\WebsiteCategorization\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Traits\Plugins\HasTimeout;

class WebsiteCategorizationRequest extends Request implements HasBody
{
    use HasFormBody;
    use HasTimeout;

    protected int $connectTimeout = 60;

    protected int $requestTimeout = 120;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(public string $domain) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '';
    }

    protected function defaultBody(): array
    {
        return [
            'query' => $this->domain,
            'data_type' => 'url',
            'api_key' => config('external-apis.website_categorization.api_key'),
        ];
    }
}
