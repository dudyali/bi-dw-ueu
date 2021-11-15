<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NOPD extends Model
{
    use SoftDeletes;

    protected $table = 'nopd';

    public function npwpd()
    {
        return $this->belongsTo('App\Models\NPWPD', 'id_npwpd');
    }

    public function transaksi()
    {
        return $this->hasMany('App\Models\TransaksiPajak', 'id_nopd');
    }
}
