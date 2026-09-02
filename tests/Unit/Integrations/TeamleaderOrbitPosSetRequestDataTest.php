<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos\PosLineData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos\PosSetRequestData;

beforeEach(function (): void {
    config()->set('data', require dirname(__DIR__, 3).'/vendor/spatie/laravel-data/config/data.php');
});

it('builds a pos.set payload with period-bound lines', function (): void {
    $data = new PosSetRequestData(
        legalentityid: 'LE1WT1GYEBG39NU',
        folderid: 'MOBV3MS7TV9PKHS',
        currencyid: 'CUTRVGEP9ZVDPDE',
        close_dt: '2026-09-08',
        supplierid: 'SPQVJMQCXGRSKHQ',
        state: 'DRAFT',
        ownerid: 'USS288WHNZAWJAV',
        lines: [
            new PosLineData(
                finaccountid: 'FAE6G2KWNCJPSCJ',
                projectcostid: 'PCKGAT1D1UVZ22P',
                periodid: 'PP1234567890ABC',
                value: 150.0,
            ),
        ],
    );

    expect(json_decode(json_encode($data->toArray()), true))->toBe([
        'legalentityid' => 'LE1WT1GYEBG39NU',
        'folderid' => 'MOBV3MS7TV9PKHS',
        'currencyid' => 'CUTRVGEP9ZVDPDE',
        'close_dt' => '2026-09-08',
        'supplierid' => 'SPQVJMQCXGRSKHQ',
        'state' => 'DRAFT',
        'ownerid' => 'USS288WHNZAWJAV',
        'lines' => [
            [
                'finaccountid' => 'FAE6G2KWNCJPSCJ',
                'projectcostid' => 'PCKGAT1D1UVZ22P',
                'periodid' => 'PP1234567890ABC',
                'value' => 150,
            ],
        ],
    ]);
});

it('omits null fields from pos.set payloads and lines', function (): void {
    $data = new PosSetRequestData(
        id: 'PONUMVA5BEPCP4E',
        lines: [
            new PosLineData(
                finaccountid: 'FAE6G2KWNCJPSCJ',
                projectcostid: 'PCKGAT1D1UVZ22P',
                price: 150.0,
            ),
        ],
    );

    expect(json_decode(json_encode($data->toArray()), true))->toBe([
        'id' => 'PONUMVA5BEPCP4E',
        'lines' => [
            [
                'finaccountid' => 'FAE6G2KWNCJPSCJ',
                'projectcostid' => 'PCKGAT1D1UVZ22P',
                'price' => 150,
            ],
        ],
    ]);
});
