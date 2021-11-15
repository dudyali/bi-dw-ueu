<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriPajak extends Model
{
    use SoftDeletes;

    protected $table = 'kategori_pajak';

    public function jenis()
    {
        return $this->belongsTo('App\Models\JenisPajak', 'id_jenis_pajak');
    }

    public function transaksi()
    {
        return $this->hasMany('App\Models\TransaksiPajak', 'id_kategori_pajak');
    }

    public function transaksi_piutang()
    {
        return $this->hasMany('App\Models\TransaksiPiutang', 'id_kategori_pajak');
    }
}
