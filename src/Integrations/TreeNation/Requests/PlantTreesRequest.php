<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TreeNation\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class PlantTreesRequest extends Request
{
    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/plant';
    }

    protected function defaultQuery(): array
    {
        return [
            'quantity' => 1,
        ];
    }
}
