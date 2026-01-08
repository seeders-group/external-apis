<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\OrganicKeywordsRequestData;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\OrganicKeywordsResponseData;

class OrganicKeywordsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public OrganicKeywordsRequestData $data
    ) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/organic-keywords';
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
        return OrganicKeywordsResponseData::from($response->json());
    }
}
