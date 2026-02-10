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
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();

            // Integration identifier
            $table->string('integration', 50)->index(); // 'openai', 'gemini', 'dataforseo'

            // Provider-specific fields
            $table->string('request_id')->nullable(); // OpenAI request ID
            $table->string('model', 50)->nullable()->index(); // gpt-4o, gpt-4o-mini, etc.
            $table->string('endpoint', 100)->nullable(); // chat.completions, images.generations

            // Usage metrics (varies by integration)
            $table->integer('prompt_tokens')->nullable();
            $table->integer('completion_tokens')->nullable();
            $table->integer('total_tokens')->nullable();
            $table->integer('input_cached_tokens')->nullable(); // OpenAI cached tokens
            $table->integer('images_generated')->nullable(); // For image generation
            $table->integer('characters_processed')->nullable(); // For TTS
            $table->integer('seconds_processed')->nullable(); // For transcription

            // Cost tracking
            $table->decimal('estimated_cost', 10, 6); // Calculated from pricing table
            $table->decimal('actual_cost', 10, 6)->nullable(); // From provider's API (reconciled)

            // Context
            $table->string('feature', 100)->index();
            $table->string('sub_feature', 100)->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            // Metadata
            $table->string('status', 20)->default('success'); // 'success', 'error'
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();

            // Timestamps
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('created_at');
            $table->index(['integration', 'created_at']);
            $table->index(['feature', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};
