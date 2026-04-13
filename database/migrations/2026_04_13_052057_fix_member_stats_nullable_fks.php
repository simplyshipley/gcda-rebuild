<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_stats', function (Blueprint $table) {
            // Drop the existing FKs to modify the columns
            $table->dropForeign(['team_id']);
            $table->dropForeign(['division_id']);

            // Make both nullable — legacy data has gaps (sub players with no assigned team/division)
            $table->foreignId('team_id')->nullable()->change();
            $table->foreignId('division_id')->nullable()->change();

            // Re-add FKs with nullOnDelete
            $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('division_id')->references('id')->on('divisions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('member_stats', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['division_id']);
            $table->foreignId('team_id')->nullable(false)->change();
            $table->foreignId('division_id')->nullable(false)->change();
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('division_id')->references('id')->on('divisions')->restrictOnDelete();
        });
    }
};
