<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NPWPD extends Model
{
    use SoftDeletes;

    protected $table = 'npwpd';

    public function nopd()
    {
        return $this->hasMany('App\Models\NOPD', 'id_npwpd');
    }

    public function transaksi()
    {
        return $this->hasMany('App\Models\TransaksiPajak', 'id_npwpd');
    }
}
