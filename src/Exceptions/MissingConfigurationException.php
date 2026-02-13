<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Exceptions;

use RuntimeException;

class MissingConfigurationException extends RuntimeException
{
    public function __construct(string $configKey)
    {
        parent::__construct("Missing required configuration value [{$configKey}]. Please set the corresponding environment variable.");
    }
}
