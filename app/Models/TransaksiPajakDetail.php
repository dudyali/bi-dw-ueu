<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiPajakDetail extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_pajak_detail';

    public function transaksi_pajak()
    {
        return $this->belongsTo('App\Models\TransaksiPajak', 'id_transaksi_pajak');
    }
}
