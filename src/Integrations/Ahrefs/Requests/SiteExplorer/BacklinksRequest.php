<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Data\Ahrefs\KeywordsExplorer\BacklinksRequestData;

class BacklinksRequest extends Request
{
    public function __construct(public BacklinksRequestData $data) {}

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/site-explorer/all-backlinks';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
