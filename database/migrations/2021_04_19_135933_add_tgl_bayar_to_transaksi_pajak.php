<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTglBayarToTransaksiPajak extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_pajak', function (Blueprint $table) {
            $table->datetime('tgl_bayar')->after('tahun')->nullable();
            $table->text('masa_pajak')->after('tgl_bayar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi_pajak', function (Blueprint $table) {
            $table->dropColumn('tgl_bayar');
            $table->dropColumn('masa_pajak');
        });
    }
}
