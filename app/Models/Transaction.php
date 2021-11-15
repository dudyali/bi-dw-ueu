<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'district_id', 'village_id', 'id_uploaded_file', 'id_channel', 'nomor', 'tg_tx', 'jm_tx', 'no_seq', 'no_trx_bank', 'no_trx_pemda', 'kd_pemda', 
        'nop', 'tahun', 'nama_wp', 'pokok_pajak', 'denda', 'potongan', 'admin', 'total', 'chnl',
        'kd_kantor', 'user'
    ];

    public function channel()
    {
        return $this->belongsTo('App\Models\Channel', 'id_channel');
    }

    public function kecamatan()
    {
        return $this->belongsTo('App\Models\Kecamatan', 'district_id');
    }

    public function kelurahan()
    {
        return $this->belongsTo('App\Models\Kelurahan', 'village_id');
    }

    public function uploaded_file()
    {
        return $this->belongsTo('App\Models\UploadedFile', 'id_uploaded_file');
    }
}
