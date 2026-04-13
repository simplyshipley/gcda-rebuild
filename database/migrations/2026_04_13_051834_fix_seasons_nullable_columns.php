<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            // week_count: nullable to accommodate historical seasons with unknown week count
            $table->tinyInteger('week_count')->unsigned()->nullable()->default(null)->change();

            // scoresheet_type: nullable — not all legacy seasons have a known type
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
            ])->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->tinyInteger('week_count')->unsigned()->default(14)->change();
            $table->enum('scoresheet_type', [
                'tuesday_6man', 'tuesday_trips', 'tuesday_aa',
                'wednesday', 'wednesday_24pts', 'wednesday_2016',
                'thursday', 'thursday_301s', 'thursday_aa',
            ])->nullable(false)->change();
        });
    }
};
