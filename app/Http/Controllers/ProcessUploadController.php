<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Imports\TransactionImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ProcessUploadController extends Controller
{
    public function proses($id_uploaded_file)
    {
        $file = UploadedFile::findOrFail($id_uploaded_file);

        if (!$file->is_processed) {
            Excel::import(new TransactionImport($id_uploaded_file, $file->id_channel), 'pbb/'.$file->path);
    
            $file->is_processed = 1;
            $file->save();
        }

        $trans = Transaction::where('id_uploaded_file', $id_uploaded_file)->get();

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

        $update = UploadedFile::find($id_uploaded_file);
        $update->tanggal_transaksi = substr($text_date, 0, strlen($text_date)-1);
        $update->total_transaksi = $total_trans;
        $update->save();

        return true;
    }

    public function success()
    {
        return redirect()->route('upload.index')->with('success', 'Berhasil memproses data upload.');
    }
}
