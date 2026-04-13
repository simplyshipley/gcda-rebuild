<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Unified replacement for the 9 legacy game tables:
        // 501_games, doubles_501, doubles_cricket, singles_cricket,
        // doubles_301, trip_601, quad_801, etc.
        // One row per game leg in a match. PPD queries this table.
        Schema::create('game_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('league_matches')->cascadeOnDelete();
            $table->unsignedTinyInteger('game_number')->comment('Which game in the match (1-7)');
            $table->enum('game_type', [
                'singles_501',
                'doubles_501',
                'singles_cricket',
                'doubles_cricket',
                'singles_301',
                'doubles_301',
                'trip_601',
                'quad_801',
            ]);
            // Home side players (player2 nullable for singles)
            $table->foreignId('home_player1_id')->constrained('members')->restrictOnDelete();
            $table->foreignId('home_player2_id')->nullable()->constrained('members')->nullOnDelete();
            // Away side players
            $table->foreignId('away_player1_id')->constrained('members')->restrictOnDelete();
            $table->foreignId('away_player2_id')->nullable()->constrained('members')->nullOnDelete();
            // Score state at finish: 0 = won (out), positive = score remaining on board
            $table->smallInteger('home_score_remaining')->default(0);
            $table->smallInteger('away_score_remaining')->default(0);
            // Total darts thrown for PPD calculation:
            // PPD = ((501 * finished_legs) - score_remaining) / total_darts
            $table->smallInteger('home_total_darts')->default(0);
            $table->smallInteger('away_total_darts')->default(0);
            // G-044/G-045: unified tinyint for MVP counts; consistent plural naming
            $table->unsignedTinyInteger('home_mvp_count')->default(0);
            $table->unsignedTinyInteger('away_mvp_count')->default(0);
            $table->boolean('home_won');
            $table->timestamps();

            // Index for PPD queries by match
            $table->index(['match_id', 'game_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_legs');
    }
};
