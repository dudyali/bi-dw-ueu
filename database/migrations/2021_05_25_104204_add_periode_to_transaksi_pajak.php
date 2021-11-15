<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPeriodeToTransaksiPajak extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_pajak', function (Blueprint $table) {
            $table->datetime('periode_awal')->after('jumlah')->nullable();
            $table->datetime('periode_akhir')->after('periode_awal')->nullable();
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
            $table->dropColumn('periode_awal');
            $table->dropColumn('periode_akhir');
        });
    }
}
