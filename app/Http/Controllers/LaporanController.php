<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Channel;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

use App\Models\Transaction;
use App\Models\TransaksiPBB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function form()
    {
        $kecamatan = Kecamatan::get();

        return view('pages.laporan.form')
            ->with('kecamatan', $kecamatan);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'jenis_penerimaan' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
        ]);

        $whereClause = [];

        if ($request->jenis_penerimaan=='realisasi') {
            $whereClause[] = ['tahun_pajak', '=', date('Y')];
        } elseif ($request->jenis_penerimaan=='piutang') {
            $whereClause[] = ['tahun_pajak', '<', date('Y')];
        }

        $kecamatan_dipilih = "all";
        if ($request->kecamatan!="all") {
            $whereClause[] = ['district_id', '=', $request->kecamatan];
            $kecamatan_dipilih = Kecamatan::find($request->kecamatan)->name;
        }

        $kelurahan_dipilih = "all";
        if ($request->kelurahan!="all") {
            $whereClause[] = ['village_id', '=', $request->kelurahan];
            $kelurahan_dipilih = Kelurahan::find($request->kelurahan)->name;
        }

        $trans = TransaksiPBB::select(
                DB::RAW('MONTH(tanggal_tx) as month'), 
                DB::RAW('SUM(pokok) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->where($whereClause)
            ->where('tanggal_tx', '>=', $request->dari)
            ->where('tanggal_tx', '<=', $request->sampai)
            ->groupby('month')
            ->get();

        $kecamatan = Kecamatan::get();
        $kelurahan = Kelurahan::where('district_id', $request->kecamatan)->get();     
        
        return view('pages.laporan.form')
            ->with('kecamatan', $kecamatan)
            ->with('kecamatan_dipilih', $kecamatan_dipilih)
            ->with('kelurahan', $kelurahan)
            ->with('kelurahan_dipilih', $kelurahan_dipilih)
            ->with('request', $request)
            ->with('trans', $trans);
    }
}
