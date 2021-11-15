<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPajakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_pajak', function (Blueprint $table) {
            $table->id();
            $table->integer('id_jenis_pajak');
            $table->integer('id_kategori_pajak');
            $table->integer('id_npwpd');
            $table->integer('id_nopd');
            $table->biginteger('pokok')->default(0);
            $table->biginteger('denda')->default(0);
            $table->biginteger('jumlah')->default(0);
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
        Schema::dropIfExists('transaksi_pajak');
    }
}
