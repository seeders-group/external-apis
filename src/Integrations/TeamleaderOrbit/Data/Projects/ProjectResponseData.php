<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects;

use Spatie\LaravelData\Data;

/**
 * Response DTO for projects.get and projects.list items.
 *
 * projects.list items carry added_at / updated_at but no dealid, folderid or
 * description; projects.get carries the full detail. Absent fields stay null.
 */
class ProjectResponseData extends Data
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $extid = null,
        public ?string $dealid = null,
        public ?string $entityid = null,
        public ?string $folderid = null,
        public ?string $description = null,
        public ?string $start_dt = null,
        public ?string $end_dt = null,
        public ?string $billing_mode = null,
        public ?string $added_at = null,
        public ?string $updated_at = null,
    ) {}
}
