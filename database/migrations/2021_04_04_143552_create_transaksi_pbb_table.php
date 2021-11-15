<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPbbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_pbb', function (Blueprint $table) {
            $table->id();
            $table->string('district_id');
            $table->string('village_id');
            $table->date('tanggal_tx');
            $table->time('jam_tx');
            $table->string('tahun_pajak');
            $table->string('tahun_bayar');
            $table->string('nop');
            $table->string('nama_wp');
            $table->biginteger('pokok')->default(0);
            $table->biginteger('denda')->default(0);
            $table->biginteger('potongan')->default(0);
            $table->biginteger('admin')->default(0);
            $table->biginteger('total')->default(0);
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
        Schema::dropIfExists('transaksi_pbb');
    }
}
