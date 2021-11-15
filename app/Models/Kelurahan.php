<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    public $timestamps = false;

    protected $table = 'villages';

    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'district_id');
    }

    public function transaction()
    {
        return $this->hasMany('App\Models\TransaksiPBB', 'village_id');
    }
}
