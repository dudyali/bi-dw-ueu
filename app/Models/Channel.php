<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use SoftDeletes;
    
    protected $table = 'channel';

    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction', 'id_channel');
    }
}
