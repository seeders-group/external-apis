<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Moz\Responses;

use Spatie\LaravelData\Data;

class LinkinRootDomainResultResponseData extends Data
{
    public function __construct(
        public string $rootDomain,
        public int $rootDomainsToRootDomain,
        public int $domainAuthority,
        public float $linkPropensity,
        public int $spamScore,
        public int $toTargetPages,
        public int $toTargetNofollowPages,
        public int $toTargetRedirectPages,
        public int $toTargetDeletedPages,
    ) {}
}
