<?php

declare(strict_types=1);

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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('trackable');
            $table->string('scope')->nullable();
            $table->string('integration');
            $table->string('endpoint');
            $table->integer('status')->default(200);
            $table->decimal('consumption', 12, 6)->default(0);
            $table->string('consumption_type')->nullable();
            $table->integer('latency_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['integration', 'created_at']);
            $table->index('scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
