<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('match_id')->nullable()->constrained('league_matches')->nullOnDelete();
            $table->unsignedTinyInteger('week_number')->nullable();
            $table->string('week_label', 20)->nullable()->comment('Playoff week label e.g. "Quarters", "Finals"');
            // Big Shots patch types
            $table->enum('patch_type', ['ton80', 'ton70', 'cricket', 'five_bulls', 'six_bulls'])
                ->comment('Ton80=180, Ton70=ton+70, Cricket=all bulls/triples in one visit, 5/6 Bulls');
            $table->date('earned_at')->nullable();
            $table->timestamps();

            // G-033: Prevent duplicate patch entry for same player+match+type
            $table->unique(['member_id', 'match_id', 'patch_type'], 'patches_member_match_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patches');
    }
};
