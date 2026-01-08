<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Contracts;

interface BudgetConfigInterface
{
    /**
     * Get budget configurations for a specific API service.
     *
     * @return iterable<self>
     */
    public static function getForService(string $service): iterable;

    /**
     * Get the budget amount.
     */
    public function getBudgetAmount(): float;

    /**
     * Get the alert threshold percentage (0-100).
     */
    public function getAlertThreshold(): int;

    /**
     * Get the notification channel/URL.
     */
    public function getNotificationChannel(): ?string;

    /**
     * Check if this budget is active.
     */
    public function isActive(): bool;
}
