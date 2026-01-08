<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Contracts;

interface TeamleaderOrbitOAuthServiceInterface
{
    public function getValidAccessToken(): string;
}
