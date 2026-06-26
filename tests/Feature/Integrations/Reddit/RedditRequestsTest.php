<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Reddit\RedditConnector;
use Seeders\ExternalApis\Integrations\Reddit\Requests\SearchRequest;
use Seeders\ExternalApis\Integrations\Reddit\Requests\SubredditAboutRequest;
use Seeders\ExternalApis\Integrations\Reddit\Requests\UserAboutRequest;

/**
 * @return array<string, mixed>
 */
function redditQuery(Request $request): array
{
    $method = new ReflectionMethod($request, 'defaultQuery');

    return $method->invoke($request);
}

it('resolves the reddit base url and a configured user agent', function (): void {
    config()->set('external-apis.reddit.user_agent', 'TestBot/9.9');

    $connector = new RedditConnector;

    expect($connector->resolveBaseUrl())->toBe('https://www.reddit.com')
        ->and($connector->headers()->get('User-Agent'))->toBe('TestBot/9.9');
});

it('builds the search endpoint and query', function (): void {
    $request = new SearchRequest('Acme', limit: 10);

    expect($request->resolveEndpoint())->toBe('/search.json')
        ->and(redditQuery($request))->toBe([
            'q' => 'Acme',
            'limit' => 10,
            'sort' => 'relevance',
            'type' => 'link',
        ]);
});

it('builds the subreddit about endpoint', function (): void {
    expect((new SubredditAboutRequest('acme'))->resolveEndpoint())
        ->toBe('/r/acme/about.json');
});

it('builds the user about endpoint', function (): void {
    expect((new UserAboutRequest('acme'))->resolveEndpoint())
        ->toBe('/user/acme/about.json');
});
