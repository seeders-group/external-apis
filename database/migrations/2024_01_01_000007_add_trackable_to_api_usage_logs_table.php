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
        Schema::table('api_usage_logs', function (Blueprint $table): void {
            $table->string('trackable_type')->nullable()->after('metadata');
            $table->unsignedBigInteger('trackable_id')->nullable()->after('trackable_type');

            $table->index(['trackable_type', 'trackable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_usage_logs', function (Blueprint $table): void {
            $table->dropIndex(['trackable_type', 'trackable_id']);
            $table->dropColumn(['trackable_type', 'trackable_id']);
        });
    }
};
