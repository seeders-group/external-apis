<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\KeywordsExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Data\Ahrefs\KeywordsExplorer\VolumeHistoryRequestData;

class VolumeHistoryRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public VolumeHistoryRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/keywords-explorer/volume-history';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
