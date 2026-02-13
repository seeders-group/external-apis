<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\BrandTracker\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class GenerateCompetitorsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $domain,
        private readonly string $businessDescription
    ) {}

    public function resolveEndpoint(): string
    {
        return '/generate-competitors';
    }

    protected function defaultBody(): array
    {
        return [
            'domain' => $this->domain,
            'business_description' => $this->businessDescription,
        ];
    }
}
