<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues')->cascadeOnDelete();
            $table->smallInteger('year')->unsigned();
            $table->enum('season_code', ['sum', 'fal', 'win']);
            $table->tinyInteger('week_count')->unsigned()->default(14);
            $table->tinyInteger('current_week')->unsigned()->nullable();
            $table->enum('status', ['future', 'current', 'completed'])->default('future');
            $table->enum('scoresheet_type', [
                'tuesday_6man',
                'tuesday_trips',
                'tuesday_aa',
                'wednesday',
                'wednesday_24pts',
                'wednesday_2016',
                'thursday',
                'thursday_301s',
                'thursday_aa',
            ]);
            $table->timestamps();

            // G-033/G-035: Prevent multiple "current" seasons per league+season_code+year.
            // MySQL does not support partial indexes, so we use a composite unique on all four
            // columns and enforce the single-current rule at the application layer.
            $table->unique(['league_id', 'season_code', 'year', 'status'], 'seasons_league_code_year_status_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
