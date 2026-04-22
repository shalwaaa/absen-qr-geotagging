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
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Jenis Mutasi: EARN (Dapat Poin), SPEND (Beli Item), PENALTY (Denda)
            $table->enum('transaction_type', ['EARN', 'SPEND', 'PENALTY']);
            $table->integer('amount'); // Jumlah masuk/keluar (selalu positif di sini, tipe yg nentuin sifatnya)
            $table->integer('current_balance'); // Saldo AKHIR setelah transaksi ini
            $table->string('description'); // Cth: "Reward Datang Pagi Tanggal X"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
    }
};
