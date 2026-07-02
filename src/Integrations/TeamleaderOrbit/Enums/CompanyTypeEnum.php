<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Enums;

enum CompanyTypeEnum: string
{
    case Prospect = 'PROSPECT';
    case Client = 'CLIENT';
    case Supplier = 'SUPPLIER';
    case Both = 'BOTH';
}
