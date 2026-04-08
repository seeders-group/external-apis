<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('api_budget_config');
        Schema::dropIfExists('ai_model_pricing');
        Schema::dropIfExists('api_service_pricing');

        if (Schema::hasTable('api_usage_logs')) {
            Schema::table('api_usage_logs', function (Blueprint $table): void {
                foreach (['estimated_cost', 'actual_cost', 'reconciled_at'] as $column) {
                    if (Schema::hasColumn('api_usage_logs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Pricing and budget tables have been replaced by the Prometheus
        // metrics endpoint. There is no rollback path.
    }
};
