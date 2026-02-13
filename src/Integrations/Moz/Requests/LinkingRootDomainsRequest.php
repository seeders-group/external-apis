<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Moz\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\Moz\Data\Requests\LinkingRootDomainsRequestData;

class LinkingRootDomainsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public LinkingRootDomainsRequestData $data
    ) {}

    public function resolveEndpoint(): string
    {
        return '/linking_root_domains';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
