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
        Schema::create('ai_model_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('integration')->default('openai');
            $table->string('model');
            $table->decimal('input_per_1m_tokens', 10, 6);
            $table->decimal('output_per_1m_tokens', 10, 6);
            $table->decimal('cached_input_per_1m_tokens', 10, 6)->nullable();
            $table->timestamps();

            $table->unique(['integration', 'model']);
            $table->index('integration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_model_pricing');
    }
};
