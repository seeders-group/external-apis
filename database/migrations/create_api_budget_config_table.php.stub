<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_budget_config', function (Blueprint $table) {
            $table->id();

            // Integration identifier
            $table->string('integration', 50)->unique(); // 'openai', 'gemini', 'dataforseo'

            // Budget limits
            $table->decimal('monthly_budget', 10, 2);
            $table->decimal('daily_budget', 10, 2);

            // Alert thresholds (percentages)
            $table->integer('warning_threshold')->default(80);
            $table->integer('critical_threshold')->default(90);

            // Alert settings
            $table->boolean('alert_enabled')->default(true);
            $table->text('google_chat_webhook_url')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_budget_config');
    }
};
