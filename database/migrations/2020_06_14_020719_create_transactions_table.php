<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('id_channel');
            $table->integer('nomor');
            $table->date('tg_tx');
            $table->string('jm_tx');
            $table->string('no_seq');
            $table->string('no_trx_bank');
            $table->string('no_trx_pemda');
            $table->string('kd_pemda');
            $table->string('nop');
            $table->string('tahun');
            $table->string('nama_wp');
            $table->biginteger('pokok_pajak')->default(0);
            $table->biginteger('denda')->default(0);
            $table->biginteger('potongan')->default(0);
            $table->biginteger('admin')->default(0);
            $table->biginteger('total')->default(0);
            $table->string('chnl')->nullable();
            $table->string('kd_kantor')->nullable();
            $table->string('user')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
