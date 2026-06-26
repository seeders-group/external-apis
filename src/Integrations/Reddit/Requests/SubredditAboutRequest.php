<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Reddit\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SubredditAboutRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $name) {}

    public function resolveEndpoint(): string
    {
        return '/r/'.$this->name.'/about.json';
    }
}
