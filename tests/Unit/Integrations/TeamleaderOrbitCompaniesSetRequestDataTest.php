<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompaniesSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Enums\CompanyTypeEnum;

beforeEach(function (): void {
    config()->set('data', require dirname(__DIR__, 3).'/vendor/spatie/laravel-data/config/data.php');
});

it('includes vat, email and bank account fields in the companies.set payload', function (): void {
    $data = new CompaniesSetRequestData(
        id: 'CP123',
        name: 'Acme BV',
        type: CompanyTypeEnum::Supplier,
        vatcode: 'NL123456789B01',
        email: 'billing@acme.test',
        iban: 'NL91ABNA0417164300',
        bic: 'ABNANL2A',
        custfields: ['seeders_studio_id' => '12345'],
    );

    expect(json_decode(json_encode($data->toArray()), true))->toBe([
        'id' => 'CP123',
        'name' => 'Acme BV',
        'type' => 'SUPPLIER',
        'vatcode' => 'NL123456789B01',
        'email' => 'billing@acme.test',
        'iban' => 'NL91ABNA0417164300',
        'bic' => 'ABNANL2A',
        'custfields' => ['seeders_studio_id' => '12345'],
    ]);
});

it('omits null fields so partial updates do not clear existing TLO values', function (): void {
    $data = new CompaniesSetRequestData(
        id: 'CP123',
        iban: 'NL91ABNA0417164300',
        bic: 'ABNANL2A',
    );

    expect($data->toArray())->toBe([
        'id' => 'CP123',
        'iban' => 'NL91ABNA0417164300',
        'bic' => 'ABNANL2A',
    ]);
});
