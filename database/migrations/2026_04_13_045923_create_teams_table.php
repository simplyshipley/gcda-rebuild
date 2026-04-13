<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            // G-027: FK to divisions table, not free-text varchar
            $table->foreignId('division_id')->constrained('divisions')->restrictOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->string('name', 50);
            $table->foreignId('captain_id')->nullable()->constrained('members')->nullOnDelete();
            $table->smallInteger('starting_points')->default(0)->comment('Bonus/handicap points at season start');
            $table->smallInteger('penalties')->default(0)->comment('Point deductions (e.g. late scoresheets)');
            $table->text('penalty_notes')->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'name'], 'teams_season_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
