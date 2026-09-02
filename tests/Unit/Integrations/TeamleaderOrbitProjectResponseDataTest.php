<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectResponseData;

beforeEach(function (): void {
    config()->set('data', require dirname(__DIR__, 3).'/vendor/spatie/laravel-data/config/data.php');
});

it('maps a projects.list item', function (): void {
    $project = ProjectResponseData::from([
        'id' => 'PR3DC1H8ZYVXZNX',
        'name' => 'Campaign NL',
        'extid' => '6418',
        'start_dt' => '2026-08-28T00:00:00',
        'end_dt' => '2026-09-09T00:00:00',
        'entityid' => 'ENQ3PNCH5WC448X',
        'billing_mode' => 'OFFER',
        'added_at' => '2026-08-28T09:37:45',
        'updated_at' => '2026-08-28T09:38:32',
    ]);

    expect($project->id)->toBe('PR3DC1H8ZYVXZNX')
        ->and($project->name)->toBe('Campaign NL')
        ->and($project->extid)->toBe('6418')
        ->and($project->billing_mode)->toBe('OFFER')
        ->and($project->dealid)->toBeNull()
        ->and($project->folderid)->toBeNull();
});

it('maps a projects.get detail with folder and deal', function (): void {
    $project = ProjectResponseData::from([
        'id' => 'PRYW3DG2AW7XPFU',
        'dealid' => null,
        'entityid' => 'ENQ3PNCH5WC448X',
        'folderid' => 'PF1GWR1JQVZT5NP',
        'extid' => '6412',
        'name' => 'My test Project',
        'description' => '',
        'start_dt' => '2026-08-26T00:00:00',
        'end_dt' => '2026-11-04T00:00:00',
        'billing_mode' => 'ESTIMATE',
    ]);

    expect($project->folderid)->toBe('PF1GWR1JQVZT5NP')
        ->and($project->description)->toBe('')
        ->and($project->added_at)->toBeNull();
});
