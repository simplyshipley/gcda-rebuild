<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            // G-027: FK to divisions
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            // G-040: lowercase snake_case — legacy was "MVPs" uppercase
            $table->smallInteger('mvp_count')->default(0)->comment('G-040: legacy MVPs column renamed to snake_case');
            // G-001/G-031: nullable integer — NULL = never finished a 501 leg; NO 255 sentinel
            $table->smallInteger('fastest_501')->nullable()->comment('G-001: NULL = never finished; legacy used 255 as sentinel');
            $table->timestamp('stats_calculated_at')->nullable();
            $table->timestamps();

            // One stats row per player per team per season
            $table->unique(['member_id', 'team_id', 'season_id'], 'member_stats_member_team_season_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_stats');
    }
};
