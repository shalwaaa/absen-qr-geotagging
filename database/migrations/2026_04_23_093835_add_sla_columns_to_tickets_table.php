<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Tambahkan kolom untuk SLA (Response & Resolution Time)
            $table->timestamp('first_response_at')->nullable()->after('status');
            $table->timestamp('closed_at')->nullable()->after('first_response_at');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['first_response_at', 'closed_at']);
        });
    }
};
