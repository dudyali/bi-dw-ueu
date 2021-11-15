<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBulanToTransaksiBphtb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaksi_bphtb', function (Blueprint $table) {
            $table->integer('bulan_trx')->after('id');
            $table->integer('tahun_trx')->after('bulan_trx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaksi_bphtb', function (Blueprint $table) {
            $table->dropColumn('bulan_trx');
            $table->dropColumn('tahun_trx');
        });
    }
}
