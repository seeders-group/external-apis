<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

class BudgetAlertService
{
    /**
     * Check budget thresholds and send alerts if exceeded.
     */
    public function checkAndAlert(string $integration = 'openai'): void
    {
        $budget = $this->getBudgetConfig($integration);

        if (! $budget || ! $budget->shouldAlert()) {
            Log::info('BudgetAlertService: Budget alerts disabled', [
                'integration' => $integration,
            ]);

            return;
        }

        $monthToDateSpend = $this->getMonthToDateSpend($integration);

        $warningThresholdAmount = $this->calculateThresholdAmount(
            (float) $budget->monthly_budget,
            $budget->warning_threshold
        );

        $criticalThresholdAmount = $this->calculateThresholdAmount(
            (float) $budget->monthly_budget,
            $budget->critical_threshold
        );

        $currentPercentage = ($monthToDateSpend / $budget->monthly_budget) * 100;

        Log::info('BudgetAlertService: Checking thresholds', [
            'integration' => $integration,
            'current_spend' => $monthToDateSpend,
            'monthly_budget' => $budget->monthly_budget,
            'current_percentage' => round($currentPercentage, 2),
            'warning_threshold_amount' => $warningThresholdAmount,
            'critical_threshold_amount' => $criticalThresholdAmount,
        ]);

        if ($monthToDateSpend >= $criticalThresholdAmount) {
            $this->handleCriticalThreshold(
                integration: $integration,
                currentSpend: $monthToDateSpend,
                budget: $budget->monthly_budget,
                thresholdAmount: $criticalThresholdAmount,
                thresholdPercentage: $budget->critical_threshold,
                webhookUrl: $budget->google_chat_webhook_url
            );

            return;
        }

        if ($monthToDateSpend >= $warningThresholdAmount) {
            $this->handleWarningThreshold(
                integration: $integration,
                currentSpend: $monthToDateSpend,
                budget: $budget->monthly_budget,
                thresholdAmount: $warningThresholdAmount,
                thresholdPercentage: $budget->warning_threshold,
                webhookUrl: $budget->google_chat_webhook_url
            );
        }
    }

    /**
     * Calculate threshold amount in dollars based on percentage.
     */
    public function calculateThresholdAmount(float $monthlyBudget, int $thresholdPercentage): float
    {
        return round(($monthlyBudget * $thresholdPercentage) / 100, 2);
    }

    /**
     * Get month-to-date spend (prefer actual costs).
     * For Ahrefs, returns units consumed instead of cost.
     */
    private function getMonthToDateSpend(string $integration): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        if ($integration === 'ahrefs') {
            return (float) $logModel::byIntegration($integration)
                ->thisMonth()
                ->sum('total_tokens');
        }

        $actualSpend = $logModel::byIntegration($integration)
            ->thisMonth()
            ->whereNotNull('actual_cost')
            ->sum('actual_cost');

        if ($actualSpend > 0) {
            return round($actualSpend, 2);
        }

        return round(
            $logModel::byIntegration($integration)
                ->thisMonth()
                ->sum('estimated_cost'),
            2
        );
    }

    /**
     * Get budget configuration.
     */
    private function getBudgetConfig(string $integration)
    {
        $budgetModel = UsageTracking::$apiBudgetConfigModel;

        if ($integration === 'openai') {
            return $budgetModel::getOpenAIBudget();
        }

        return $budgetModel::where('integration', $integration)->first();
    }

    /**
     * Handle critical threshold breach.
     */
    private function handleCriticalThreshold(
        string $integration,
        float $currentSpend,
        float $budget,
        float $thresholdAmount,
        int $thresholdPercentage,
        ?string $webhookUrl
    ): void {
        $cacheKey = "budget_alert_critical_{$integration}_".now()->format('Y-m');

        if (Cache::has($cacheKey)) {
            Log::info('BudgetAlertService: Critical alert already sent this month', [
                'integration' => $integration,
            ]);

            return;
        }

        $remaining = $budget - $currentSpend;
        $overBudget = $currentSpend > $budget;

        $message = $this->buildCriticalMessage(
            integration: $integration,
            currentSpend: $currentSpend,
            budget: $budget,
            thresholdPercentage: $thresholdPercentage,
            remaining: $remaining,
            overBudget: $overBudget
        );

        $this->sendGoogleChatAlert($message, $webhookUrl, 'CRITICAL');

        Cache::put($cacheKey, true, now()->endOfMonth());

        Log::warning('BudgetAlertService: Critical threshold breached', [
            'integration' => $integration,
            'current_spend' => $currentSpend,
            'budget' => $budget,
            'threshold_percentage' => $thresholdPercentage,
        ]);
    }

    /**
     * Handle warning threshold breach.
     */
    private function handleWarningThreshold(
        string $integration,
        float $currentSpend,
        float $budget,
        float $thresholdAmount,
        int $thresholdPercentage,
        ?string $webhookUrl
    ): void {
        $cacheKey = "budget_alert_warning_{$integration}_".now()->format('Y-m');

        if (Cache::has($cacheKey)) {
            Log::info('BudgetAlertService: Warning alert already sent this month', [
                'integration' => $integration,
            ]);

            return;
        }

        $remaining = $budget - $currentSpend;
        $percentageUsed = round(($currentSpend / $budget) * 100, 1);

        $message = $this->buildWarningMessage(
            integration: $integration,
            currentSpend: $currentSpend,
            budget: $budget,
            thresholdPercentage: $thresholdPercentage,
            percentageUsed: $percentageUsed,
            remaining: $remaining
        );

        $this->sendGoogleChatAlert($message, $webhookUrl, 'WARNING');

        Cache::put($cacheKey, true, now()->endOfMonth());

        Log::warning('BudgetAlertService: Warning threshold breached', [
            'integration' => $integration,
            'current_spend' => $currentSpend,
            'budget' => $budget,
            'threshold_percentage' => $thresholdPercentage,
        ]);
    }

    /**
     * Build critical alert message.
     */
    private function buildCriticalMessage(
        string $integration,
        float $currentSpend,
        float $budget,
        int $thresholdPercentage,
        float $remaining,
        bool $overBudget
    ): string {
        $integrationName = strtoupper($integration);
        $month = now()->format('F Y');
        $percentageUsed = round(($currentSpend / $budget) * 100, 1);

        $status = $overBudget ? 'OVER BUDGET' : 'CRITICAL';

        if ($integration === 'ahrefs') {
            $currentDisplay = number_format($currentSpend).' units';
            $budgetDisplay = number_format($budget).' units';
            $remainingText = $overBudget
                ? 'Over limit by: '.number_format(abs($remaining)).' units'
                : 'Remaining: '.number_format($remaining).' units';
        } else {
            $currentDisplay = '$'.number_format($currentSpend, 2);
            $budgetDisplay = '$'.number_format($budget, 2);
            $remainingText = $overBudget
                ? 'Over budget by: $'.number_format(abs($remaining), 2)
                : 'Remaining: $'.number_format($remaining, 2);
        }

        return <<<MSG
        {$status}: {$integrationName} API Budget Alert

        Budget Status for {$month}
        Current Usage: {$currentDisplay}
        Monthly Limit: {$budgetDisplay}
        Usage: {$percentageUsed}%
        Threshold: {$thresholdPercentage}%
        {$remainingText}

        CRITICAL ALERT: The {$integrationName} API budget has exceeded {$thresholdPercentage}% of the monthly limit!

        Action Required:
        - Review API usage patterns immediately
        - Check for unexpected high-cost operations
        - Consider optimizing API calls or increasing budget
        - Monitor dashboard: /admin/api-usage

        MSG;
    }

    /**
     * Build warning alert message.
     */
    private function buildWarningMessage(
        string $integration,
        float $currentSpend,
        float $budget,
        int $thresholdPercentage,
        float $percentageUsed,
        float $remaining
    ): string {
        $integrationName = strtoupper($integration);
        $month = now()->format('F Y');

        if ($integration === 'ahrefs') {
            $currentDisplay = number_format($currentSpend).' units';
            $budgetDisplay = number_format($budget).' units';
            $remainingDisplay = number_format($remaining).' units';
        } else {
            $currentDisplay = '$'.number_format($currentSpend, 2);
            $budgetDisplay = '$'.number_format($budget, 2);
            $remainingDisplay = '$'.number_format($remaining, 2);
        }

        return <<<MSG
        WARNING: {$integrationName} API Budget Alert

        Budget Status for {$month}
        Current Usage: {$currentDisplay}
        Monthly Limit: {$budgetDisplay}
        Usage: {$percentageUsed}%
        Threshold: {$thresholdPercentage}%
        Remaining: {$remainingDisplay}

        The {$integrationName} API budget has reached {$thresholdPercentage}% of the monthly limit.

        Recommended Actions:
        - Monitor usage closely for the rest of the month
        - Review cost optimization opportunities
        - Dashboard: /admin/api-usage

        MSG;
    }

    /**
     * Send alert to Google Chat webhook.
     */
    private function sendGoogleChatAlert(string $message, ?string $webhookUrl, string $severity = 'INFO'): void
    {
        if (! $webhookUrl) {
            Log::warning('BudgetAlertService: No webhook URL configured');

            return;
        }

        try {
            $response = Http::timeout(10)->post($webhookUrl, [
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('BudgetAlertService: Alert sent successfully', [
                    'severity' => $severity,
                    'response_status' => $response->status(),
                ]);
            } else {
                Log::error('BudgetAlertService: Failed to send alert', [
                    'severity' => $severity,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('BudgetAlertService: Exception sending alert', [
                'severity' => $severity,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Manually trigger budget check (useful for testing).
     */
    public function checkBudgetStatus(string $integration = 'openai'): array
    {
        $budget = $this->getBudgetConfig($integration);

        if (! $budget) {
            return [
                'status' => 'no_budget',
                'message' => 'No budget configured',
            ];
        }

        $monthToDateSpend = $this->getMonthToDateSpend($integration);
        $percentageUsed = ($monthToDateSpend / $budget->monthly_budget) * 100;

        $warningThresholdAmount = $this->calculateThresholdAmount(
            (float) $budget->monthly_budget,
            $budget->warning_threshold
        );

        $criticalThresholdAmount = $this->calculateThresholdAmount(
            (float) $budget->monthly_budget,
            $budget->critical_threshold
        );

        $status = 'ok';
        if ($monthToDateSpend >= $criticalThresholdAmount) {
            $status = 'critical';
        } elseif ($monthToDateSpend >= $warningThresholdAmount) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'integration' => $integration,
            'current_spend' => round($monthToDateSpend, 2),
            'monthly_budget' => (float) $budget->monthly_budget,
            'percentage_used' => round($percentageUsed, 2),
            'remaining' => round((float) $budget->monthly_budget - $monthToDateSpend, 2),
            'thresholds' => [
                'warning' => [
                    'percentage' => $budget->warning_threshold,
                    'amount' => $warningThresholdAmount,
                    'exceeded' => $monthToDateSpend >= $warningThresholdAmount,
                ],
                'critical' => [
                    'percentage' => $budget->critical_threshold,
                    'amount' => $criticalThresholdAmount,
                    'exceeded' => $monthToDateSpend >= $criticalThresholdAmount,
                ],
            ],
        ];
    }
}
