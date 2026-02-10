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
        Schema::create('api_service_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('integration');
            $table->string('endpoint')->nullable();
            $table->decimal('cost_per_unit', 10, 6)->default(0);
            $table->string('unit_type')->default('api_units');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['integration', 'endpoint']);
            $table->index('integration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_service_pricing');
    }
};
