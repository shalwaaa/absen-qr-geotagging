<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->decimal('latitude2', 10, 8)->nullable()->after('longitude');
            $table->decimal('longitude2', 11, 8)->nullable()->after('latitude2');
        });
    }

    public function down()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn(['latitude2', 'longitude2']);
        });
    }
};