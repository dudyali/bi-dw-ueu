<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GenerateController extends Controller
{
    public function generate() 
    {
        $get = UploadedFile::where('is_processed', 1)->get();

        foreach ($get as $key => $g) {
            $trans = Transaction::where('id_uploaded_file', $g->id)->get();

            $tgl_trans = [];
            $total_trans = 0;
            foreach ($trans as $key => $t) {
                $tgl_trans[] = $t->tg_tx;
                $total_trans += $t->total;
            }

            $unique_date = array_unique($tgl_trans);
            $text_date = null;
            foreach ($unique_date as $key => $ud) {
                $text_date .= $ud.',';
            }

            $update = UploadedFile::find($g->id);
            $update->tanggal_transaksi = substr($text_date, 0, strlen($text_date)-1);
            $update->total_transaksi = $total_trans;
            $update->save();
        }
    }
}
