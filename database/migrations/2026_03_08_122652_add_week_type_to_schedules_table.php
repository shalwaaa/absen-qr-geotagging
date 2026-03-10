<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('schedules', function (Blueprint $table) {
        // all = Setiap minggu ada
        // odd = Hanya minggu ganjil (1, 3, 5)
        // even = Hanya minggu genap (2, 4, 6)
        $table->enum('week_type', ['all', 'odd', 'even'])->default('all')->after('day');
    });
}

public function down()
{
    Schema::table('schedules', function (Blueprint $table) {
        $table->dropColumn('week_type');
    });
}
};
