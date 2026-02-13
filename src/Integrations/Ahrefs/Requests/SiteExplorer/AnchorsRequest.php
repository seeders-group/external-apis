<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\AnchorsRequestData;

class AnchorsRequest extends Request
{
    public function __construct(public AnchorsRequestData $data) {}

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/site-explorer/anchors';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
