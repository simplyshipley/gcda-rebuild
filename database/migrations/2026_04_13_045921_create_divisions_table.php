<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->string('code', 10)->comment('Division code: A, B, C, AA, etc.');
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->boolean('is_twoofthree')->default(false)->comment('Best 2-of-3 legs format');
            $table->timestamps();

            // G-027: division is a real FK entity, not free-text varchar
            $table->unique(['season_id', 'code'], 'divisions_season_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
