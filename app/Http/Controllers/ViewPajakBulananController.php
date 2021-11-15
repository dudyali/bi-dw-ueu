<?php

namespace App\Http\Controllers;

use App\Models\LastSync;
use App\Models\JenisPajak;
use Illuminate\Http\Request;
use App\Models\KategoriPajak;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ViewPajakBulananController extends Controller
{
    public function jenis($tahun)
    {
        $get = JenisPajak::where('tahun', $tahun)->with('transaksi')->get();

        $ls = LastSync::where('modul', 'simpad')->where('tahun', $tahun)->first();
        $all_year = JenisPajak::select(DB::RAW('distinct(tahun) as year'))->get();

        $data = [];
        foreach ($get as $key => $value) {
            $row = [];
            $row['id'] = $value->id;
            $row['jenis_pajak'] = $value->nama;
            $jan = $feb = $mar = $apr = $mei = $jun = $jul = $agu = $sep = $okt = $nov = $des = 0;

            foreach ($value->transaksi as $trx) {
                if ($trx->bulan == 1) {
                    $jan += $trx->jumlah;
                }
                if ($trx->bulan == 2) {
                    $feb += $trx->jumlah;
                }
                if ($trx->bulan == 3) {
                    $mar += $trx->jumlah;
                }
                if ($trx->bulan == 4) {
                    $apr += $trx->jumlah;
                }
                if ($trx->bulan == 5) {
                    $mei += $trx->jumlah;
                }
                if ($trx->bulan == 6) {
                    $jun += $trx->jumlah;
                }
                if ($trx->bulan == 7) {
                    $jul += $trx->jumlah;
                }
                if ($trx->bulan == 8) {
                    $agu += $trx->jumlah;
                }
                if ($trx->bulan == 9) {
                    $sep += $trx->jumlah;
                }
                if ($trx->bulan == 10) {
                    $okt += $trx->jumlah;
                }
                if ($trx->bulan == 11) {
                    $nov += $trx->jumlah;
                }
                if ($trx->bulan == 12) {
                    $des += $trx->jumlah;
                }
            }

            $row['jan'] = $jan;
            $row['feb'] = $feb;
            $row['mar'] = $mar;
            $row['apr'] = $apr;
            $row['mei'] = $mei;
            $row['jun'] = $jun;
            $row['jul'] = $jul;
            $row['agu'] = $agu;
            $row['sep'] = $sep;
            $row['okt'] = $okt;
            $row['nov'] = $nov;
            $row['des'] = $des;
            $row['total'] = $jan + $feb + $mar + $apr + $mei + $jun + $jul + $agu + $sep + $okt + $nov + $des;

            $data[] = $row;
        }

        return view('pages.pajak-bulanan.jenis')
            ->with('data', $data)
            ->with('ls', $ls)
            ->with('all_year', $all_year)
            ->with('tahun', $tahun);
    }
    
    public function kategori($id_jenis_pajak)
    {
        $jenis = JenisPajak::findOrFail($id_jenis_pajak);
        $get = KategoriPajak::where('id_jenis_pajak', $id_jenis_pajak)->with('transaksi')->get();

        $data = [];
        foreach ($get as $key => $value) {
            $row = [];
            $row['id'] = $value->id;
            $row['kategori_pajak'] = $value->nama;
            $jan = $feb = $mar = $apr = $mei = $jun = $jul = $agu = $sep = $okt = $nov = $des = 0;

            foreach ($value->transaksi as $trx) {
                if ($trx->bulan == 1) {
                    $jan += $trx->jumlah;
                }
                if ($trx->bulan == 2) {
                    $feb += $trx->jumlah;
                }
                if ($trx->bulan == 3) {
                    $mar += $trx->jumlah;
                }
                if ($trx->bulan == 4) {
                    $apr += $trx->jumlah;
                }
                if ($trx->bulan == 5) {
                    $mei += $trx->jumlah;
                }
                if ($trx->bulan == 6) {
                    $jun += $trx->jumlah;
                }
                if ($trx->bulan == 7) {
                    $jul += $trx->jumlah;
                }
                if ($trx->bulan == 8) {
                    $agu += $trx->jumlah;
                }
                if ($trx->bulan == 9) {
                    $sep += $trx->jumlah;
                }
                if ($trx->bulan == 10) {
                    $okt += $trx->jumlah;
                }
                if ($trx->bulan == 11) {
                    $nov += $trx->jumlah;
                }
                if ($trx->bulan == 12) {
                    $des += $trx->jumlah;
                }
            }

            $row['jan'] = $jan;
            $row['feb'] = $feb;
            $row['mar'] = $mar;
            $row['apr'] = $apr;
            $row['mei'] = $mei;
            $row['jun'] = $jun;
            $row['jul'] = $jul;
            $row['agu'] = $agu;
            $row['sep'] = $sep;
            $row['okt'] = $okt;
            $row['nov'] = $nov;
            $row['des'] = $des;
            $row['total'] = $jan + $feb + $mar + $apr + $mei + $jun + $jul + $agu + $sep + $okt + $nov + $des;

            $data[] = $row;
        }

        return view('pages.pajak-bulanan.kategori')
            ->with('jenis', $jenis)
            ->with('data', $data);
    }
}
