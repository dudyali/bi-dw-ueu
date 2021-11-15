<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiBphtbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_bphtb', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_sspd')->nullable();
            $table->string('nop')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama_wp')->nullable();
            $table->string('kelurahan_wp')->nullable();
            $table->string('kecamatan_wp')->nullable();
            $table->string('nama_notaris')->nullable();
            $table->string('no_transaksi')->nullable();
            $table->string('jenis_perolehan')->nullable();
            $table->integer('luas_tanah')->default(0);
            $table->integer('luas_bangunan')->default(0);
            $table->biginteger('njop')->default(0);
            $table->biginteger('npoptkp')->default(0);
            $table->string('no_sspd')->nullable();
            $table->string('tgl_sspd')->nullable();
            $table->string('tgl_bayar')->nullable();
            $table->biginteger('bphtb_belum_diskon')->default(0);
            $table->biginteger('bphtb_besar_pengurangan')->default(0);
            $table->biginteger('bphtb_yang_dibayar')->default(0);
            $table->string('no_sspd_awal')->nullable();
            $table->string('tgl_transaksi_bayar_sspd_awal')->nullable();
            $table->biginteger('bphtb_terhutang_sspd_awal')->default(0);
            $table->biginteger('bphtb_yang_telah_dibayar_sspd_awal')->default(0);
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
        Schema::dropIfExists('transaksi_bphtb');
    }
}
