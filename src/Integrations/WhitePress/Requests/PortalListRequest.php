<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\WhitePress\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class PortalListRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(public readonly string $language, public readonly int $page = 1) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/portal/list/'.$this->language;
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => $this->page,
            'size' => config('external-apis.whitepress.limit'),
            'offer_dofollow' => 1,
        ];
    }
}
