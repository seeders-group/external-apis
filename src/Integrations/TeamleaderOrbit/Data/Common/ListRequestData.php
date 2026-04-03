<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ListRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public ?array $filter = null,
        public ?array $order = null,
        public array $page = ['size' => 20, 'number' => 1],
    ) {}
}
