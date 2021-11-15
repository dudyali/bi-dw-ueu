<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPBB extends Model
{
    protected $table = 'transaksi_pbb';
    
    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'district_id');
    }

    public function kelurahan()
    {
        return $this->belongsTo('App\Models\Kelurahan', 'village_id');
    }
}
