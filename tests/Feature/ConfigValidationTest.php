<?php

declare(strict_types=1);

use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;
use Seeders\ExternalApis\Integrations\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Integrations\DataForSeo\DataForSeoConnector;
use Seeders\ExternalApis\Integrations\GoogleSearch\GoogleSearchConnector;
use Seeders\ExternalApis\Integrations\Hunter\HunterConnector;
use Seeders\ExternalApis\Integrations\Moz\MozLinksConnector;
use Seeders\ExternalApis\Integrations\SeRanking\SeRankingConnector;

it('throws when ahrefs token is missing', function (): void {
    config()->set('external-apis.ahrefs.token', null);

    $connector = new AhrefsConnector;
    $connector->withMockClient(new MockClient([MockResponse::make([], 200)]));

    $connector->send(new ConfigValidationDummyRequest);
})->throws(MissingConfigurationException::class, 'external-apis.ahrefs.token');

it('throws when dataforseo username is missing', function (): void {
    config()->set('external-apis.dataforseo.username', null);

    $connector = new DataForSeoConnector;
    $connector->withMockClient(new MockClient([MockResponse::make([], 200)]));

    $connector->send(new ConfigValidationDummyRequest);
})->throws(MissingConfigurationException::class, 'external-apis.dataforseo.username');

it('throws when moz client id is missing', function (): void {
    config()->set('external-apis.moz.client_id', null);

    $connector = new MozLinksConnector;
    $connector->withMockClient(new MockClient([MockResponse::make([], 200)]));

    $connector->send(new ConfigValidationDummyRequest);
})->throws(MissingConfigurationException::class, 'external-apis.moz.client_id');

it('throws when seranking token is missing', function (): void {
    config()->set('external-apis.seranking.token', null);

    $connector = new SeRankingConnector;
    $connector->withMockClient(new MockClient([MockResponse::make([], 200)]));

    $connector->send(new ConfigValidationDummyRequest);
})->throws(MissingConfigurationException::class, 'external-apis.seranking.token');

it('throws when hunter api key is missing', function (): void {
    config()->set('external-apis.hunter.api_key', null);

    $connector = new HunterConnector;
    $connector->withMockClient(new MockClient([MockResponse::make([], 200)]));

    $connector->send(new ConfigValidationDummyRequest);
})->throws(MissingConfigurationException::class, 'external-apis.hunter.api_key');

it('throws when google search key is missing', function (): void {
    config()->set('external-apis.google_search.key', null);

    $connector = new GoogleSearchConnector;
    $connector->withMockClient(new MockClient([MockResponse::make([], 200)]));

    $connector->send(new ConfigValidationDummyRequest);
})->throws(MissingConfigurationException::class, 'external-apis.google_search.key');

class ConfigValidationDummyRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/test';
    }
}
