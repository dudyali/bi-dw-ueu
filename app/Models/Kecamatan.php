<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    public $timestamps = false;

    protected $table = 'districts';

    public function kelurahan()
    {
        return $this->hasMany('App\Models\Kelurahan', 'district_id');
    }

    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction', 'district_id');
    }
}
