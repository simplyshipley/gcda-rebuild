<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('high_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            // G-027: FK to divisions
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            // High In: highest score thrown on a single visit to open a leg (max 170)
            $table->smallInteger('high_in_score')->nullable()->unsigned();
            $table->foreignId('high_in_member_id')->nullable()->constrained('members')->nullOnDelete();
            // High Out: highest checkout on a single visit to finish a leg (max 170)
            $table->smallInteger('high_out_score')->nullable()->unsigned();
            $table->foreignId('high_out_member_id')->nullable()->constrained('members')->nullOnDelete();
            // One record per division per season; no created_at needed per spec
            $table->timestamp('updated_at')->nullable();

            $table->unique(['season_id', 'division_id'], 'high_scores_season_division_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('high_scores');
    }
};
