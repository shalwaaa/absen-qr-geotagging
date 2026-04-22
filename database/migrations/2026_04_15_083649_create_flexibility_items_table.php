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
        Schema::create('flexibility_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // Cth: "Kompensasi Telat 15 Menit"
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-ticket'); // Ikon FontAwesome
            
            $table->enum('item_type',['late_pass', 'permission_pass', 'custom']); 
            $table->integer('value_minutes')->nullable(); // Cth: 15 (untuk telat 15 menit)
            
            $table->integer('point_cost'); // Harga item (Cth: 50 poin)
            $table->integer('stock_limit')->nullable(); // Batas beli per siswa
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flexibility_items');
    }
};
