<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            // Legacy dart_id — unique card number (e.g. "12345", "SUB-xxx")
            $table->string('dart_card_number', 10)->unique()->comment('Legacy dart_id; substitute range 40000-79999');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('nickname', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('photo_path', 255)->nullable();
            // G-001: Legacy stored " SUB" in last_name — new schema uses explicit boolean
            $table->boolean('is_substitute')->default(false)->comment('True for dart_ids 40000-79999; legacy " SUB" in last_name');
            $table->boolean('is_active')->default(true);
            // Sentinel placeholder members (BYE/99999/99998/99997)
            $table->boolean('is_placeholder')->default(false);
            $table->enum('placeholder_type', ['bye', 'pending', 'illegal', 'forfeit'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
