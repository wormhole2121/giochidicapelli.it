<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('available_dates', function (Blueprint $table) {
            $table->string('schedule_type')->default('normal')->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('available_dates', function (Blueprint $table) {
            $table->dropColumn('schedule_type');
        });
    }
};