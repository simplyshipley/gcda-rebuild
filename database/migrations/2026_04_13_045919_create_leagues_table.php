<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('e.g. Tuesday, Wednesday, Thursday');
            $table->string('slug', 20)->unique()->comment('e.g. tuesday, wednesday, thursday');
            $table->string('day_of_week', 10)->nullable()->comment('Day the league plays');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
