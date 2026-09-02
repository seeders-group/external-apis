<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data;

use Spatie\LaravelData\Data;

class TeamleaderOrbitData extends Data
{
    /**
     * The TLO API treats set-calls as partial updates: omitted fields keep
     * their current value, while null would clear them. Strip nulls
     * recursively so nested payloads (e.g. pos.set lines) stay partial too.
     */
    public function toArray(): array
    {
        return $this->withoutNullValues(parent::toArray());
    }

    /**
     * @param  array<array-key, mixed>  $values
     * @return array<array-key, mixed>
     */
    private function withoutNullValues(array $values): array
    {
        return array_filter(
            array_map(
                fn (mixed $value): mixed => is_array($value) ? $this->withoutNullValues($value) : $value,
                $values,
            ),
            fn (mixed $value): bool => $value !== null,
        );
    }
}
