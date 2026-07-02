<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Users\MailsContextRequestData;

class MailsContextRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public MailsContextRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/mails.context';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
