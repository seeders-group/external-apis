<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\GoogleApis\Requests\YouTube\ChannelLookupRequest;
use Seeders\ExternalApis\Integrations\GoogleApis\Requests\YouTube\ChannelSearchRequest;

beforeEach(function (): void {
    config()->set('external-apis.youtube.key', 'test-youtube-key');
});

/**
 * @return array<string, mixed>
 */
function youtubeQuery(Request $request): array
{
    $method = new ReflectionMethod($request, 'defaultQuery');

    return $method->invoke($request);
}

it('builds the channel search endpoint and query', function (): void {
    $request = new ChannelSearchRequest('Acme');

    expect($request->resolveEndpoint())->toBe('/youtube/v3/search')
        ->and(youtubeQuery($request))->toBe([
            'key' => 'test-youtube-key',
            'q' => 'Acme',
            'type' => 'channel',
            'part' => 'snippet',
            'maxResults' => 5,
        ]);
});

it('builds the channel lookup endpoint and query', function (): void {
    $request = new ChannelLookupRequest('chan-123');

    expect($request->resolveEndpoint())->toBe('/youtube/v3/channels')
        ->and(youtubeQuery($request))->toBe([
            'key' => 'test-youtube-key',
            'id' => 'chan-123',
            'part' => 'snippet,statistics,brandingSettings,status',
        ]);
});
