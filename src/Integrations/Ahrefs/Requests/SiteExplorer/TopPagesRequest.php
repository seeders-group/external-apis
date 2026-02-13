<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\TopPagesRequestData;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\TopPagesResponseData;

class TopPagesRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public TopPagesRequestData $data
    ) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/top-pages';
    }

    protected function defaultQuery(): array
    {
        $query = $this->data->toArray();

        if (isset($query['where']) && is_array($query['where'])) {
            $query['where'] = json_encode($query['where']);
        }

        return array_filter($query);
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return TopPagesResponseData::from($response->json());
    }
}
