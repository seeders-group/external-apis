<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\EmailData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * A Teamleader Orbit company as returned by the companies.get and
 * companies.list endpoints.
 *
 * Orbit returns a flat payload: the VAT number is `vatcode` and the primary
 * email is a single `email` string. The optional `emails` collection is kept
 * for forward-compatibility should richer payloads appear.
 *
 * Extends Data directly (not TeamleaderOrbitData) on purpose: the null-omitting
 * toArray() is request-payload behaviour and must not hide absent fields when
 * a response is serialized.
 */
#[MapInputName(SnakeCaseMapper::class)]
class CompanyResponseData extends Data
{
    /**
     * @param  array<int, EmailData>  $emails
     */
    public function __construct(
        public string $id,
        public ?string $name = null,
        #[MapInputName('vatcode')]
        public ?string $vatNumber = null,
        public ?string $website = null,
        public ?string $email = null,
        public ?string $type = null,
        public ?string $iban = null,
        public ?string $bic = null,
        #[DataCollectionOf(EmailData::class)]
        public array $emails = [],
    ) {}

    /**
     * Lower-cased, trimmed email addresses attached to the company, combining
     * the flat `email` field with any structured `emails` entries.
     *
     * @return array<int, string>
     */
    public function emailAddresses(): array
    {
        $addresses = array_map(
            fn (EmailData $email): string => mb_strtolower(trim($email->email)),
            $this->emails,
        );

        if (is_string($this->email) && trim($this->email) !== '') {
            $addresses[] = mb_strtolower(trim($this->email));
        }

        return array_values(array_unique(array_filter($addresses)));
    }
}
