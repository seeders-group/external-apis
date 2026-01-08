<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Contracts;

/**
 * Interface for Planning models used in domain planning operations.
 *
 * This interface defines the contract for Planning entities that
 * can be passed to the DomainPlanningClient for AI-assisted planning.
 */
interface PlanningInterface
{
    /**
     * Get the planning data as an array for prompt generation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
