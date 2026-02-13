<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\BrandTracker\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class GeneratePersonasRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly array $businessProfile,
        private readonly array $competitors,
        private readonly string $researchLocation,
        private readonly int $numPersonas = 4
    ) {}

    public function resolveEndpoint(): string
    {
        return '/personas/generate';
    }

    protected function defaultBody(): array
    {
        return [
            'project_id' => $this->businessProfile['project_id'] ?? null,
            'business_profile' => $this->businessProfile,
            'competitors' => $this->competitors,
            'num_personas' => $this->numPersonas,
        ];
    }
}
