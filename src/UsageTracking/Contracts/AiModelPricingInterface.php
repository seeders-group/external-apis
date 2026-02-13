<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Contracts;

interface AiModelPricingInterface
{
    /**
     * Get pricing for a model by its ID.
     */
    public static function getByModelId(string $modelId): ?self;

    /**
     * Get the input token price (per million tokens).
     */
    public function getInputPrice(): float;

    /**
     * Get the output token price (per million tokens).
     */
    public function getOutputPrice(): float;

    /**
     * Calculate total cost based on token usage.
     */
    public function calculateCost(int $inputTokens, int $outputTokens): float;
}
