<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Expenses;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Expenses\ExpenseNoteSetRequestData;

class ExpenseNoteSetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ExpenseNoteSetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/expensenote.set';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
