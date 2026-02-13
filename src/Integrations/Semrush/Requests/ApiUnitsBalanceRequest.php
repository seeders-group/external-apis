<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Requests;

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
        return 'https://www.semrush.com/users/countapiunits.html';
    }

    protected function defaultQuery(): array
    {
        return [
            'key' => config('external-apis.semrush.api_key'),
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        $units = str_replace(',', '', trim($response->body()));

        if (! preg_match('/^-?\d+$/', $units)) {
            throw new RuntimeException('Invalid Semrush API units balance response. Expected numeric body.');
        }

        return new ApiUnitsBalanceResponseData(
            units: (int) $units,
        );
    }
}
