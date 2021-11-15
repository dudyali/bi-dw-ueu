<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetBPHTB extends Model
{
    use SoftDeletes;

    protected $table = 'target_bphtb';
}
