<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\BrandTracker\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class RunBrandTrackingRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $websiteUrl,
        private readonly array $businessProfile,
        private readonly array $personas,
        private readonly array $questions,
        private readonly array $competitors,
        private readonly string $researchLocation,
        private readonly bool $enableWebSearch = true,
        private readonly string $llmModel = 'gpt-4o-mini'
    ) {}

    public function resolveEndpoint(): string
    {
        return '/brand-tracking/execute';
    }

    protected function defaultBody(): array
    {
        return [
            'project_id' => $this->businessProfile['project_id'] ?? null,
            'business_name' => $this->businessProfile['company_name'] ?? '',
            'question_sets' => $this->questions,
            'enable_web_search' => $this->enableWebSearch,
        ];
    }
}
