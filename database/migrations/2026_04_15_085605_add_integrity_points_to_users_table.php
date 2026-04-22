<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom integrity_points jika belum ada
            if (!Schema::hasColumn('users', 'integrity_points')) {
                $table->integer('integrity_points')->default(0)->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'integrity_points')) {
                $table->dropColumn('integrity_points');
            }
        });
    }
};