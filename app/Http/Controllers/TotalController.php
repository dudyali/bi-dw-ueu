<?php

namespace App\Http\Controllers;

use App\Models\TargetPBB;
use App\Models\JenisPajak;
use App\Models\TargetBPHTB;
use App\Models\Transaction;
use App\Models\TransaksiPBB;
use Illuminate\Http\Request;
use App\Models\TransaksiBPHTB;
use App\Models\TransaksiPajak;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TotalController extends Controller
{
    public function index($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $all_year = JenisPajak::select(DB::RAW('distinct(tahun) as year'))->get();

        $jenis_pajak = JenisPajak::where('tahun', $tahun)->get();
        $trans_pajak = TransaksiPajak::where('tahun', $tahun)->orderby('bulan')->get();
        
        $non_pbb = [];
        $bulan_ini = date('m');

        $non_pbb_total_bulan_lalu = 0;
        $non_pbb_total_bulan_ini = 0;
        $total_target = 0;

        foreach ($jenis_pajak as $jp) {
            $row = [];
            $row['jenis_pajak'] = $jp->nama;
            $row['target'] = $jp->target;
            
            $total_bulan_ini = 0;
            $total_bulan_lalu = 0;

            foreach ($trans_pajak as $tp) {
                if ($jp->id == $tp->id_jenis_pajak) {
                    if ($tp->bulan < $bulan_ini) {
                        $total_bulan_lalu += $tp->jumlah;    
                    } elseif ($tp->bulan == $bulan_ini) {
                        $total_bulan_ini += $tp->jumlah;    
                    }
                }
            }

            $row['realisasi']['bulan_lalu'] = $total_bulan_lalu;
            $row['realisasi']['bulan_ini'] = $total_bulan_ini;

            $non_pbb_total_bulan_lalu += $total_bulan_lalu;
            $non_pbb_total_bulan_ini += $total_bulan_ini;
            $total_target += $jp->target;

            $tots = $total_bulan_lalu + $total_bulan_ini;
            $row['lebih_kurang'] = $tots - $jp->target;
            $row['persentase'] = $tots * 100 / $jp->target;

            $non_pbb[] = $row;
        }

        $total_non_pbb['bulan_lalu'] = $non_pbb_total_bulan_lalu;
        $total_non_pbb['bulan_ini'] = $non_pbb_total_bulan_ini;
        $total_non_pbb['total_target'] = $total_target;
        $total_non_pbb['lebih_kurang'] = ($non_pbb_total_bulan_lalu + $non_pbb_total_bulan_ini) - $total_target;
        $total_non_pbb['persentase'] = ($non_pbb_total_bulan_lalu + $non_pbb_total_bulan_ini) * 100 / $total_target;

        $trans_pbb = TransaksiPBB::select(
            DB::RAW('MONTH(tanggal_tx) as month'),
            DB::RAW('SUM(total) as total')
        )
        ->groupby('month')
        ->where('tahun_bayar', $tahun)
        ->get();

        $targetPBB = TargetPBB::where('tahun', $tahun)->first();
        $pbb_lebih_kurang = $trans_pbb->sum('total') - $targetPBB->target;
        $pbb_persentase = 0;
        if (!is_null($targetPBB)) {
            if ($targetPBB->target!=0) {
                $pbb_persentase = $trans_pbb->sum('total') * 100 / $targetPBB->target;
            }
        }

        $pbb = [];
        $pbb_total_bulan_ini = 0;
        $pbb_total_bulan_lalu = 0;

        foreach ($trans_pbb as $key => $value) {
            if ($value->month < $bulan_ini) {
                $pbb_total_bulan_lalu += $value->total;
            } elseif ($value->month == $bulan_ini) {
                $pbb_total_bulan_ini += $value->total;
            }
        }

        $pbb['bulan_lalu'] = $pbb_total_bulan_lalu;
        $pbb['bulan_ini'] = $pbb_total_bulan_ini;

        $trx_perbulan = TransaksiBPHTB::select('bulan_trx', DB::RAW('SUM(bphtb_yang_dibayar) as bphtb_yang_dibayar'))
            ->groupby('bulan_trx')
            ->get();

        $targetBPHTB = TargetBPHTB::where('tahun', $tahun)->first();
        $bphtb_lebih_kurang = $trx_perbulan->sum('bphtb_yang_dibayar') - $targetBPHTB->target;
        $bphtb_persentase = 0;
        if (!is_null($targetBPHTB)) {
            if ($targetBPHTB->target!=0) {
                $bphtb_persentase = $trx_perbulan->sum('bphtb_yang_dibayar') * 100 / $targetBPHTB->target;
            }
        }

        $bphtb_bulan_lalu = 0;
        $bphtb_bulan_ini = 0;
        foreach ($trx_perbulan as $key => $value) {
            if ($value->bulan_trx < $bulan_ini) {
                $bphtb_bulan_lalu += $value->bphtb_yang_dibayar;
            } elseif ($value->bulan_trx == $bulan_ini) {
                $bphtb_bulan_ini += $value->bphtb_yang_dibayar;
            }
        }

        $bphtb['bulan_lalu'] = $bphtb_bulan_lalu;
        $bphtb['bulan_ini'] = $bphtb_bulan_ini;

        return view('pages.dashboard.total')
            ->with('targetBPHTB', $targetBPHTB)
            ->with('targetPBB', $targetPBB)
            ->with('pbb', $pbb)
            ->with('pbb_lebih_kurang', $pbb_lebih_kurang)
            ->with('pbb_persentase', $pbb_persentase)
            ->with('bphtb', $bphtb)
            ->with('bphtb_lebih_kurang', $bphtb_lebih_kurang)
            ->with('bphtb_persentase', $bphtb_persentase)
            ->with('non_pbb', $non_pbb)
            ->with('total_non_pbb', $total_non_pbb)
            ->with('all_year', $all_year)
            ->with('tahun', $tahun);
    }

    public function hasilIntegrasi($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $all_year = JenisPajak::select(DB::RAW('distinct(tahun) as year'))->get();

        $jenis_pajak = JenisPajak::where('tahun', $tahun)->get();
        $trans_pajak = TransaksiPajak::where('tahun', $tahun)->orderby('bulan')->get();
        
        $non_pbb = [];
        $bulan_ini = date('m');

        $non_pbb_total_bulan_lalu = 0;
        $non_pbb_total_bulan_ini = 0;
        $total_target = 0;
        $total_seluruhnya = 0;

        foreach ($jenis_pajak as $jp) {
            $row = [];
            $row['jenis_pajak'] = $jp->nama;
            $row['target'] = $jp->target;
            
            $total_bulan_ini = 0;
            $total_bulan_lalu = 0;

            foreach ($trans_pajak as $tp) {
                if ($jp->id == $tp->id_jenis_pajak) {
                    if ($tp->bulan < $bulan_ini) {
                        $total_bulan_lalu += $tp->jumlah;    
                    } elseif ($tp->bulan == $bulan_ini) {
                        $total_bulan_ini += $tp->jumlah;    
                    }
                }
            }
            
            // ambil 20% aja dari angka total sebelum integrasi --
            $row['realisasi']['bulan_lalu'] = $total_bulan_lalu * 20 / 100;
            $row['realisasi']['bulan_ini'] = $total_bulan_ini;

            $non_pbb_total_bulan_lalu += $total_bulan_lalu;
            $non_pbb_total_bulan_ini += $total_bulan_ini;
            $total_target += $jp->target;

            $tots = $total_bulan_lalu + $total_bulan_ini;
            $row['lebih_kurang'] = $tots - $jp->target;
            $row['persentase'] = $tots * 100 / $jp->target;

            $non_pbb[] = $row;
        }

        // ambil 20% aja dari angka total sebelum integrasi --
        $total_non_pbb['bulan_lalu'] = $non_pbb_total_bulan_lalu * 20 / 100;
        $total_non_pbb['bulan_ini'] = $non_pbb_total_bulan_ini;
        $total_non_pbb['total_target'] = $total_target;
        $total_non_pbb['lebih_kurang'] = ($non_pbb_total_bulan_lalu + $non_pbb_total_bulan_ini) - $total_target;
        $total_non_pbb['persentase'] = ($non_pbb_total_bulan_lalu + $non_pbb_total_bulan_ini) * 100 / $total_target;
        $total_seluruhnya = $total_seluruhnya + ($total_non_pbb['bulan_lalu'] + $total_non_pbb['bulan_ini']);

        $trans_pbb = TransaksiPBB::select(
            DB::RAW('MONTH(tanggal_tx) as month'),
            DB::RAW('SUM(total) as total')
        )
        ->groupby('month')
        ->where('tahun_bayar', $tahun)
        ->get();

        $targetPBB = TargetPBB::where('tahun', $tahun)->first();
        $pbb_lebih_kurang = $trans_pbb->sum('total') - $targetPBB->target;
        $pbb_persentase = 0;
        if (!is_null($targetPBB)) {
            if ($targetPBB->target!=0) {
                $pbb_persentase = $trans_pbb->sum('total') * 100 / $targetPBB->target;
            }
        }

        $pbb = [];
        $pbb_total_bulan_ini = 0;
        $pbb_total_bulan_lalu = 0;

        foreach ($trans_pbb as $key => $value) {
            if ($value->month < $bulan_ini) {
                $pbb_total_bulan_lalu += $value->total;
            } elseif ($value->month == $bulan_ini) {
                $pbb_total_bulan_ini += $value->total;
            }
        }

        // ambil 20% aja dari angka total sebelum integrasi --
        $pbb['bulan_lalu'] = $pbb_total_bulan_lalu * 20 / 100;
        $pbb['bulan_ini'] = $pbb_total_bulan_ini;
        $total_seluruhnya = $total_seluruhnya + ($pbb['bulan_lalu'] + $pbb['bulan_ini']);

        $trx_perbulan = TransaksiBPHTB::select('bulan_trx', DB::RAW('SUM(bphtb_yang_dibayar) as bphtb_yang_dibayar'))
            ->groupby('bulan_trx')
            ->get();

        $targetBPHTB = TargetBPHTB::where('tahun', $tahun)->first();
        $bphtb_lebih_kurang = $trx_perbulan->sum('bphtb_yang_dibayar') - $targetBPHTB->target;
        $bphtb_persentase = 0;
        if (!is_null($targetBPHTB)) {
            if ($targetBPHTB->target!=0) {
                $bphtb_persentase = $trx_perbulan->sum('bphtb_yang_dibayar') * 100 / $targetBPHTB->target;
            }
        }

        $bphtb_bulan_lalu = 0;
        $bphtb_bulan_ini = 0;
        foreach ($trx_perbulan as $key => $value) {
            if ($value->bulan_trx < $bulan_ini) {
                $bphtb_bulan_lalu += $value->bphtb_yang_dibayar;
            } elseif ($value->bulan_trx == $bulan_ini) {
                $bphtb_bulan_ini += $value->bphtb_yang_dibayar;
            }
        }

        // ambil 20% aja dari angka total sebelum integrasi --
        $bphtb['bulan_lalu'] = $bphtb_bulan_lalu * 20 / 100;
        $bphtb['bulan_ini'] = $bphtb_bulan_ini;
        $total_seluruhnya = $total_seluruhnya + ($bphtb['bulan_lalu'] + $bphtb['bulan_ini']);

        return view('pages.dashboard.hasil-integrasi')
            ->with('targetBPHTB', $targetBPHTB)
            ->with('targetPBB', $targetPBB)
            ->with('pbb', $pbb)
            ->with('pbb_lebih_kurang', $pbb_lebih_kurang)
            ->with('pbb_persentase', $pbb_persentase)
            ->with('bphtb', $bphtb)
            ->with('bphtb_lebih_kurang', $bphtb_lebih_kurang)
            ->with('bphtb_persentase', $bphtb_persentase)
            ->with('non_pbb', $non_pbb)
            ->with('total_non_pbb', $total_non_pbb)
            ->with('all_year', $all_year)
            ->with('total_seluruhnya', $total_seluruhnya)
            ->with('tahun', $tahun);
    }
}
