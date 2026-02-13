<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Prensalink\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetProductsRequest extends Request
{
    public function __construct(public int $page = 1) {}

    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/ecommerce-managers/products';
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => $this->page,
            'email' => config('external-apis.prensalink.email'),
            'api_key' => config('external-apis.prensalink.api_key'),
        ];
    }
}
