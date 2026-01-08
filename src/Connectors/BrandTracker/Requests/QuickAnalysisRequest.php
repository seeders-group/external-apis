<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\BrandTracker\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class QuickAnalysisRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $websiteUrl,
        private readonly string $researchLocation
    ) {}

    public function resolveEndpoint(): string
    {
        return '/quick-analysis';
    }

    protected function defaultBody(): array
    {
        return [
            'website_url' => $this->websiteUrl,
            'company_name' => null,
            'research_location' => $this->researchLocation,
        ];
    }
}
