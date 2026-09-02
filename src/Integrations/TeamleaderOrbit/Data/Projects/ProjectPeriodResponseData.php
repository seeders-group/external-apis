<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects;

use Spatie\LaravelData\Data;

/**
 * Response DTO for the undocumented projectperiod.get endpoint. Requires both
 * the project id (PR...) and the period id (ST..., from projects.get periods);
 * `costs` carries the encrypted projectcost ids (PC...) needed by pos.set.
 * The `tasks` shape is unknown so far (always empty on the test tenant).
 */
class ProjectPeriodResponseData extends Data
{
    /**
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<int, ProjectPeriodCostData>  $costs
     * @param  array<int, ProjectPeriodChapterData>  $chapters
     */
    public function __construct(
        public string $projectid,
        public string $projectperiodid,
        public array $tasks = [],
        public array $costs = [],
        public array $chapters = [],
    ) {}
}
