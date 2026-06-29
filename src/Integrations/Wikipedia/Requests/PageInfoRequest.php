<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Wikipedia\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class PageInfoRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $title,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultQuery(): array
    {
        return [
            'action' => 'query',
            'titles' => $this->title,
            'format' => 'json',
            'prop' => 'info|extracts',
            'exintro' => true,
            'explaintext' => true,
        ];
    }
}
