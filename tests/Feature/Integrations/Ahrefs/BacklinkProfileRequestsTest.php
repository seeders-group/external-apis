<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer\BacklinksStatsRequest;
use Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer\DomainRatingRequest;

/**
 * @return array<string, mixed>
 */
function ahrefsQuery(Request $request): array
{
    $method = new ReflectionMethod($request, 'defaultQuery');

    return $method->invoke($request);
}

it('includes the select clause in the domain-rating query when provided', function (): void {
    $request = new DomainRatingRequest(
        domain: 'example.com',
        date: Date::parse('2026-06-25'),
        select: 'domain_rating,ahrefs_rank',
    );

    expect($request->resolveEndpoint())->toBe('/site-explorer/domain-rating')
        ->and(ahrefsQuery($request))->toBe([
            'target' => 'example.com',
            'date' => '2026-06-25',
            'select' => 'domain_rating,ahrefs_rank',
        ]);
});

it('omits the select clause from the domain-rating query when not provided', function (): void {
    $request = new DomainRatingRequest(domain: 'example.com', date: Date::parse('2026-06-25'));

    expect(ahrefsQuery($request))->toBe([
        'target' => 'example.com',
        'date' => '2026-06-25',
    ]);
});

it('builds the backlinks-stats endpoint and query', function (): void {
    $request = new BacklinksStatsRequest(domain: 'example.com', date: Date::parse('2026-06-25'));

    expect($request->resolveEndpoint())->toBe('/site-explorer/backlinks-stats')
        ->and(ahrefsQuery($request))->toBe([
            'target' => 'example.com',
            'date' => '2026-06-25',
        ]);
});

it('defaults backlinks-stats date to yesterday when omitted', function (): void {
    Carbon::setTestNow(Date::parse('2026-06-26 12:00:00'));

    $request = new BacklinksStatsRequest(domain: 'example.com');

    expect(ahrefsQuery($request)['date'])->toBe('2026-06-25');

    Carbon::setTestNow();
});
