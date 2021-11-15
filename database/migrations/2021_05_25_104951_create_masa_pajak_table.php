<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasaPajakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masa_pajak', function (Blueprint $table) {
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
        Schema::dropIfExists('masa_pajak');
    }
}
