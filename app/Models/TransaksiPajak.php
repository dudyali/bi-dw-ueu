<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiPajak extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_pajak';

    public function npwpd()
    {
        return $this->belongsTo('App\Models\NPWPD', 'id_npwpd');
    }

    public function nopd()
    {
        return $this->belongsTo('App\Models\NOPD', 'id_nopd');
    }

    public function jenis()
    {
        return $this->belongsTo('App\Models\JenisPajak', 'id_jenis_pajak');
    }

    public function kategori()
    {
        return $this->belongsTo('App\Models\KategoriPajak', 'id_kategori_pajak');
    }
    
    public function transaksi_pajak_detail()
    {
        return $this->hasMany('App\Models\TransaksiPajakDetail', 'id_transaksi_pajak');
    }
}
