<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class KeywordGroupData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $creation_date
    ) {}
}
