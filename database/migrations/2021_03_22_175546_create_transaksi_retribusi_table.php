<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiRetribusiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_retribusi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bayar');
            $table->date('tanggal_penerimaan');
            $table->string('nama_opd');
            $table->string('jenis_retribusi');
            $table->biginteger('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_retribusi');
    }
}
