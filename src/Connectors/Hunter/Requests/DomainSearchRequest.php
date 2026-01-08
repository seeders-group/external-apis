<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Hunter\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DomainSearchRequest extends Request
{
    public function __construct(
        public string $domain,
        public ?int $limit = null,
        public ?int $offset = null,
        public ?string $type = null,
        public bool $sentry = true
    ) {}

    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/domain-search';
    }

    /**
     * Default query parameters for this request
     */
    protected function defaultQuery(): array
    {
        $query = [
            'domain' => $this->domain,
            'sentry' => $this->sentry ? 'true' : 'false',
        ];

        if ($this->limit) {
            $query['limit'] = $this->limit;
        }

        if ($this->offset) {
            $query['offset'] = $this->offset;
        }

        if ($this->type) {
            $query['type'] = $this->type;
        }

        return $query;
    }
}
