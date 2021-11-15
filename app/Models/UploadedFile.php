<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadedFile extends Model
{
    use SoftDeletes;

    protected $table = 'uploaded_file';

    public function channel()
    {
        return $this->belongsTo('App\Models\Channel', 'id_channel');
    }

    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction', 'id_uploaded_file');
    }
}
