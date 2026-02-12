<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Semrush\Requests;

use RuntimeException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Data\Semrush\ApiUnitsBalanceResponseData;

class ApiUnitsBalanceRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultQuery(): array
    {
        return [
            'type' => 'api_units',
            'key' => config('external-apis.semrush.api_key'),
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $units = trim($response->body());

        if (! preg_match('/^-?\d+$/', $units)) {
            throw new RuntimeException('Invalid Semrush API units balance response. Expected numeric body.');
        }

        return new ApiUnitsBalanceResponseData(
            units: (int) $units,
        );
    }
}
