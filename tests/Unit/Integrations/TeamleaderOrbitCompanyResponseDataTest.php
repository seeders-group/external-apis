<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompanyResponseData;

beforeEach(function (): void {
    config()->set('data', require dirname(__DIR__, 3).'/vendor/spatie/laravel-data/config/data.php');
});

it('maps the flat Teamleader Orbit company payload into typed properties', function (): void {
    $company = CompanyResponseData::from([
        'id' => 'CP93E1SD5FPSBYK',
        'name' => 'We Talk SEO B.V.',
        'vatcode' => 'NL860156965B01',
        'website' => 'wetalkseo.nl',
        'email' => 'Koen@WeTalkSEO.nl',
        'iban' => 'NL91ABNA0417164300',
        'bic' => 'ABNANL2A',
        'street' => 'Hoofdstraat',
        'housenumber' => '12a',
        'zipcode' => '1234 AB',
        'city' => 'Amsterdam',
        'country' => 'The Netherlands',
        'phone' => '+31612345678',
        'custfields' => ['SEEDERS_STUDIO_SUPPLIER_ID' => '42'],
    ]);

    expect($company->id)->toBe('CP93E1SD5FPSBYK')
        ->and($company->vatNumber)->toBe('NL860156965B01')
        ->and($company->website)->toBe('wetalkseo.nl')
        ->and($company->iban)->toBe('NL91ABNA0417164300')
        ->and($company->bic)->toBe('ABNANL2A')
        ->and($company->street)->toBe('Hoofdstraat')
        ->and($company->housenumber)->toBe('12a')
        ->and($company->zipcode)->toBe('1234 AB')
        ->and($company->city)->toBe('Amsterdam')
        ->and($company->country)->toBe('The Netherlands')
        ->and($company->phone)->toBe('+31612345678')
        ->and($company->custfields)->toBe(['SEEDERS_STUDIO_SUPPLIER_ID' => '42'])
        ->and($company->emailAddresses())->toBe(['koen@wetalkseo.nl']);
});

it('merges a structured emails collection with the flat email', function (): void {
    $company = CompanyResponseData::from([
        'id' => 'uuid-1',
        'email' => 'primary@acme.test',
        'emails' => [
            ['type' => 'invoicing', 'email' => 'Billing@Acme.test'],
        ],
    ]);

    expect($company->emailAddresses())->toBe(['billing@acme.test', 'primary@acme.test']);
});

it('tolerates a sparse payload', function (): void {
    $company = CompanyResponseData::from(['id' => 'uuid-2']);

    expect($company->name)->toBeNull()
        ->and($company->vatNumber)->toBeNull()
        ->and($company->iban)->toBeNull()
        ->and($company->emailAddresses())->toBe([]);
});
