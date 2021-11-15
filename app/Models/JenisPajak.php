<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPajak extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_pajak';

    public function kategori()
    {
        return $this->hasMany('App\Models\KategoriPajak', 'id_jenis_pajak');
    }

    public function transaksi()
    {
        return $this->hasMany('App\Models\TransaksiPajak', 'id_jenis_pajak');
    }

    public function transaksi_piutang()
    {
        return $this->hasMany('App\Models\TransaksiPiutang', 'id_jenis_pajak');
    }
}
