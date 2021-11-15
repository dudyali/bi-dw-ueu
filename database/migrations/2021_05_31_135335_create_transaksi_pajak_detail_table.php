<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPajakDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_pajak', function (Blueprint $table) {
            $table->dropColumn('masa_pajak');    
        });

        Schema::create('transaksi_pajak_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('id_transaksi_pajak');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->biginteger('pokok');
            $table->biginteger('denda');
            $table->biginteger('jumlah');
            $table->timestamps();
            $table->softdeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_pajak_detail');
    }
}
