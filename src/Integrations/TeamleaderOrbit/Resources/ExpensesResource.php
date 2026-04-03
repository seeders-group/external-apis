<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Expenses\ExpenseNoteSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Expenses\ExpenseNotesContextRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Expenses\ExpenseNoteSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class ExpensesResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function context(): Response
    {
        return $this->connector->send(new ExpenseNotesContextRequest);
    }

    public function set(ExpenseNoteSetRequestData $data): Response
    {
        return $this->connector->send(new ExpenseNoteSetRequest($data));
    }
}
