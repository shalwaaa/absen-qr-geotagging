<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('title'); 
            $table->date('date')->unique(); // Tanggal unik (tidak boleh double)
            $table->text('description')->nullable();
            
            // Pembeda: 'national' (dari API) atau 'manual' (input Admin)
            $table->enum('type', ['national', 'manual'])->default('manual');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holidays');
    }
};