<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\KeywordsExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\KeywordsExplorer\OverviewRequestData;

class OverviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public OverviewRequestData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/keywords-explorer/overview';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
