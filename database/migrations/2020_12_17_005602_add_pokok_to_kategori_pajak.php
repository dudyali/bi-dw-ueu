<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPokokToKategoriPajak extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kategori_pajak', function (Blueprint $table) {
            $table->biginteger('pokok')->default(0)->after('nama');
            $table->biginteger('denda')->default(0)->after('pokok');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kategori_pajak', function (Blueprint $table) {
            $table->dropColumn('pokok');
            $table->dropColumn('denda');
        });
    }
}
