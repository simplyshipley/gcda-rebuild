<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('role', ['rostered', 'substitute'])->default('rostered');
            $table->date('joined_at')->nullable();
            $table->timestamps();

            // One roster slot per player per team
            $table->unique(['team_id', 'member_id'], 'team_members_team_member_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
