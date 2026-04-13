<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table is named "league_matches" to avoid collision with MySQL reserved word "matches"
        Schema::create('league_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            // G-027: FK to divisions
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            $table->unsignedTinyInteger('week_number')->comment('Regular season week; use is_playoff + playoff_round for playoffs');
            $table->boolean('is_playoff')->default(false);
            $table->string('playoff_round', 10)->nullable()->comment('Q, S, F, etc. for playoffs');
            $table->date('match_date')->nullable();
            $table->foreignId('home_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->restrictOnDelete();
            $table->smallInteger('home_score')->default(0);
            $table->smallInteger('away_score')->default(0);
            $table->enum('received_status', ['pending', 'on_time', 'late'])->default('pending');
            $table->timestamp('received_at')->nullable();
            // G-043: users.password is VARCHAR(255); scoresheet submitter is a user account
            $table->foreignId('scoresheet_submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // G-033: Prevent duplicate match scheduling
            $table->unique(
                ['season_id', 'division_id', 'week_number', 'home_team_id', 'away_team_id'],
                'league_matches_schedule_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_matches');
    }
};
