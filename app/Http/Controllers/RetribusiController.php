<?php

namespace App\Http\Controllers;

use App\Models\LastSync;
use App\Models\JenisPajak;
use Illuminate\Http\Request;
use App\Models\KategoriPajak;
use App\Models\TransaksiPajak;
use App\Models\TransaksiPiutang;
use App\Http\Controllers\Controller;

class RetribusiController extends Controller
{
    public function kategori($id_jenis_pajak)
    {
        $jp = JenisPajak::findOrFail($id_jenis_pajak);
        $kp = KategoriPajak::where('id_jenis_pajak', $id_jenis_pajak)->get();

        $tahun = $jp->tahun;
        $ls = LastSync::where('tahun', $tahun)->first();

        return view('pages.retribusi.kategori')
            ->with('jp', $jp)
            ->with('ls', $ls)
            ->with('tahun', $tahun)
            ->with('kp', $kp);
    }

    public function detail($id_kategori_pajak)
    {
        $kp = KategoriPajak::findOrFail($id_kategori_pajak);
        $trx = TransaksiPajak::where('id_kategori_pajak', $id_kategori_pajak)->get();

        $ls = LastSync::where('tahun', $kp->tahun)->first();

        return view('pages.retribusi.detail')
            ->with('kp', $kp)
            ->with('ls', $ls)
            ->with('trx', $trx);
    }

    public function piutang_kategori($id_jenis_pajak)
    {
        $jp = JenisPajak::findOrFail($id_jenis_pajak);
        $kp = KategoriPajak::where('id_jenis_pajak', $id_jenis_pajak)->get();

        $tahun = $jp->tahun;
        $ls = LastSync::where('modul', 'piutang-simpad')->where('tahun', $tahun)->first();

        return view('pages.piutang.kategori')
            ->with('jp', $jp)
            ->with('ls', $ls)
            ->with('tahun', $tahun)
            ->with('kp', $kp);
    }

    public function piutang_detail($id_kategori_pajak)
    {
        $kp = KategoriPajak::findOrFail($id_kategori_pajak);
        $trx = TransaksiPiutang::where('id_kategori_pajak', $id_kategori_pajak)->get();

        $ls = LastSync::where('modul', 'piutang-simpad')->where('tahun', $kp->tahun)->first();

        return view('pages.piutang.detail')
            ->with('kp', $kp)
            ->with('ls', $ls)
            ->with('trx', $trx);
    }
}
