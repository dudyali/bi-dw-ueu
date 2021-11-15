<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Channel;
use App\Models\LastSync;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

use App\Models\JenisPajak;
use App\Models\Transaction;
use App\Helpers\BulanHelper;
use App\Models\TransaksiPBB;
use Illuminate\Http\Request;
use App\Models\TransaksiBPHTB;
use App\Models\TransaksiRetribusi;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function summary($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $ls = LastSync::where('tahun', $tahun)->first();

        $jp = JenisPajak::where('tahun', $tahun)->get();
        
        $all_year = JenisPajak::select(DB::RAW('distinct(tahun) as year'))->get();

        return view('pages.dashboard.summary')
            ->with('jp', $jp)
            ->with('tahun', $tahun)
            ->with('ls', $ls)
            ->with('all_year', $all_year);
    }

    public function perchannel($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $all_year = Transaction::select(DB::RAW('YEAR(tg_tx) as year'))->distinct()->get();

        $transaction_tahun_berjalan = Transaction::select(
                DB::RAW('MONTH(tg_tx) as month'), 
                DB::RAW('SUM(pokok_pajak) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->whereHas('uploaded_file', function($query) {
                $query->wherenull('deleted_at');
            })
            ->groupby('month')
            ->where('tahun', $tahun)
            ->whereYear('tg_tx', $tahun)
            ->get();

        $transaction_piutang = Transaction::select(
                DB::RAW('MONTH(tg_tx) as month'), 
                DB::RAW('SUM(pokok_pajak) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->whereHas('uploaded_file', function($query) {
                $query->wherenull('deleted_at');
            })
            ->groupby('month')
            ->where('tahun', '<', $tahun)
            ->whereYear('tg_tx', $tahun)
            ->get();

        $realisasi_channel = Transaction::select(
                DB::RAW('MONTH(tg_tx) as month'), 
                DB::RAW('id_channel'), 
                DB::RAW('SUM(pokok_pajak) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->whereHas('uploaded_file', function($query) {
                $query->wherenull('deleted_at');
            })
            ->where('tahun', $tahun)
            ->whereYear('tg_tx', $tahun)
            ->groupby('month')
            ->groupby('id_channel')
            ->get();

        $piutang_channel = Transaction::select(
                DB::RAW('MONTH(tg_tx) as month'), 
                DB::RAW('id_channel'), 
                DB::RAW('SUM(pokok_pajak) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->whereHas('uploaded_file', function($query) {
                $query->wherenull('deleted_at');
            })
            ->where('tahun', '<', $tahun)
            ->whereYear('tg_tx', $tahun)
            ->groupby('month')
            ->groupby('id_channel')
            ->get();

        $sppt_tahun_berjalan = Transaction::where('tahun', $tahun)->whereYear('tg_tx', $tahun)
            ->whereHas('uploaded_file', function($query) {
                $query->wherenull('deleted_at');
            })    
            ->count();

        $sppt_piutang = Transaction::where('tahun', '<', $tahun)->whereYear('tg_tx', $tahun)
            ->whereHas('uploaded_file', function($query) {
                $query->wherenull('deleted_at');
            })    
            ->count();

        $channel = Channel::get();

        return view('pages.dashboard.perchannel')
            ->with('channel', $channel)
            ->with('transaction_tahun_berjalan', $transaction_tahun_berjalan)
            ->with('transaction_piutang', $transaction_piutang)
            ->with('all_year', $all_year)
            ->with('realisasi_channel', $realisasi_channel)
            ->with('piutang_channel', $piutang_channel)
            ->with('sppt_tahun_berjalan', $sppt_tahun_berjalan)
            ->with('sppt_piutang', $sppt_piutang)
            ->with('tahun', $tahun);
    }

    public function perbulan($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $all_year = TransaksiPBB::select('tahun_bayar as year')->distinct()->get();

        $transaction_tahun_berjalan = TransaksiPBB::select(
                DB::RAW('MONTH(tanggal_tx) as month'), 
                DB::RAW('SUM(pokok) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->groupby('month')
            ->where('tahun_pajak', $tahun)
            ->where('tahun_bayar', $tahun)
            ->get();
            
        $transaction_piutang = TransaksiPBB::select(
                DB::RAW('MONTH(tanggal_tx) as month'), 
                DB::RAW('SUM(pokok) as pokok'), 
                DB::RAW('SUM(denda) as denda'), 
                DB::RAW('SUM(total) as total'),
                DB::RAW('COUNT(*) as sppt')
            )
            ->groupby('month')
            ->where('tahun_pajak', '<', $tahun)
            ->where('tahun_bayar', $tahun)
            ->get();

        $sppt_tahun_berjalan = TransaksiPBB::where('tahun_pajak', $tahun)->where('tahun_bayar', $tahun)->count();
        $sppt_piutang = TransaksiPBB::where('tahun_pajak', '<', $tahun)->where('tahun_bayar', $tahun)->count();

        $lastSync = LastSync::where('modul', 'smartgov-pbb')->where('tahun', $tahun)->first();

        return view('pages.dashboard.perbulan')
            ->with('lastSync', $lastSync)
            ->with('all_year', $all_year)
            ->with('transaction_tahun_berjalan', $transaction_tahun_berjalan)
            ->with('transaction_piutang', $transaction_piutang)
            ->with('sppt_tahun_berjalan', $sppt_tahun_berjalan)
            ->with('sppt_piutang', $sppt_piutang)
            ->with('tahun', $tahun);
    }

    public function perkecamatan($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $all_year = TransaksiPBB::select('tahun_bayar as year')->distinct()->get();

        $realisasi_tahun_berjalan = TransaksiPBB::select(DB::RAW('SUM(total) as total'))->where('tahun_pajak', $tahun)->where('tahun_bayar', $tahun)->get();
        $realisasi_piutang = TransaksiPBB::select(DB::RAW('SUM(total) as total'))->where('tahun_pajak', '<', $tahun)->where('tahun_bayar', $tahun)->get();

        $kecamatan_tahun_berjalan = TransaksiPBB::select('district_id', DB::RAW('SUM(total) as total'))
            ->groupby('district_id')
            ->where('tahun_pajak', $tahun)
            ->where('tahun_bayar', $tahun)
            ->get();

        $kecamatan_piutang = TransaksiPBB::select('district_id', DB::RAW('SUM(total) as total'))
            ->groupby('district_id')
            ->where('tahun_pajak', '<', $tahun)
            ->where('tahun_bayar', $tahun)
            ->get();

        $kecamatan = Kecamatan::get();

        $data_per_kecamatan = [];
        foreach ($kecamatan as $key => $value) {
            $row['id_kecamatan'] = $value->id; 
            $row['kecamatan'] = $value->name;

            $row['realisasi_tahun_berjalan'] = 0;
            foreach ($kecamatan_tahun_berjalan as $tb) {
                if ($tb->district_id==$value->id) {
                    $row['realisasi_tahun_berjalan'] = $tb->total;
                    break;
                }
            }

            $row['realisasi_piutang'] = 0;
            foreach ($kecamatan_piutang as $piu) {
                if ($piu->district_id==$value->id) {
                    $row['realisasi_piutang'] = $piu->total;
                    break;
                }
            }

            $row['total_realisasi'] = $row['realisasi_tahun_berjalan'] + $row['realisasi_piutang'];

            $data_per_kecamatan[] = $row;
        }

        return view('pages.dashboard.perkecamatan')
            ->with('all_year', $all_year)
            ->with('realisasi_tahun_berjalan', $realisasi_tahun_berjalan)
            ->with('realisasi_piutang', $realisasi_piutang)
            ->with('kecamatan_tahun_berjalan', $kecamatan_tahun_berjalan)
            ->with('kecamatan_piutang', $kecamatan_piutang)
            ->with('data_per_kecamatan', $data_per_kecamatan)
            ->with('tahun', $tahun);
    }

    public function perkelurahan($id_kecamatan, $tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $all_year = TransaksiPBB::select('tahun_bayar as year')->distinct()->get();

        $kecamatan = Kecamatan::findOrFail($id_kecamatan);

        $realisasi_tahun_berjalan = TransaksiPBB::where('tahun_pajak', $tahun)->where('tahun_bayar', $tahun)->where('district_id', $id_kecamatan)->get();
        $realisasi_piutang = TransaksiPBB::where('tahun_pajak', '<', $tahun)->where('tahun_bayar', $tahun)->where('district_id', $id_kecamatan)->get();

        $kelurahan_tahun_berjalan = Kelurahan::with(['transaction' => function($query) use($tahun) {
            $query->where('tahun_pajak', $tahun)->where('tahun_bayar', $tahun);
        }])->where('district_id', $id_kecamatan)->get();

        $kelurahan_piutang = Kelurahan::with(['transaction' => function($query) use($tahun) {
            $query->where('tahun_pajak', '<', $tahun)->where('tahun_bayar', $tahun);
        }])->where('district_id', $id_kecamatan)->get();

        $kelurahan = Kelurahan::where('district_id', $id_kecamatan)->get();

        $data_per_kelurahan = [];
        foreach ($kelurahan as $key => $value) {
            $row['id_kelurahan'] = $value->id; 
            $row['kelurahan'] = $value->name;

            foreach ($kelurahan_tahun_berjalan as $tb) {
                if ($tb->name==$value->name) {
                    $row['realisasi_tahun_berjalan'] = $tb->transaction->sum('total');
                    break;
                }
            }

            foreach ($kelurahan_piutang as $piu) {
                if ($piu->name==$value->name) {
                    $row['realisasi_piutang'] = $piu->transaction->sum('total');
                    break;
                }
            }

            $row['total_realisasi'] = $row['realisasi_tahun_berjalan'] + $row['realisasi_piutang'];

            $data_per_kelurahan[] = $row;
        }

        return view('pages.dashboard.perkelurahan')
            ->with('all_year', $all_year)
            ->with('kecamatan', $kecamatan)
            ->with('data_per_kelurahan', $data_per_kelurahan)
            ->with('realisasi_tahun_berjalan', $realisasi_tahun_berjalan)
            ->with('realisasi_piutang', $realisasi_piutang)
            ->with('kelurahan_tahun_berjalan', $kelurahan_tahun_berjalan)
            ->with('kelurahan_piutang', $kelurahan_piutang)
            ->with('tahun', $tahun);
    }

    public function dashboard_bphtb($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $ls = LastSync::where('tahun', $tahun)->first();
        
        $all_year = TransaksiBPHTB::select(DB::RAW('distinct(tahun_trx) as year'))->get();

        $trx_jenis_perolehan = TransaksiBPHTB::select('jenis_perolehan', DB::RAW('SUM(bphtb_yang_dibayar) as bphtb_yang_dibayar'))
            ->where('tahun_trx', $tahun)
            ->groupby('jenis_perolehan')
            ->orderby('bphtb_yang_dibayar', 'desc')
            ->get();

        $trx_perbulan = TransaksiBPHTB::select('bulan_trx', DB::RAW('SUM(bphtb_yang_dibayar) as bphtb_yang_dibayar'))
            ->where('tahun_trx', $tahun)
            ->groupby('bulan_trx')
            ->get();

        $trx_bulanan = [];
        $total = 0;
        for ($i=1; $i <= 12; $i++) { 
            $row = [];
            $row['bulan_digit'] = $i;
            $row['bulan'] = BulanHelper::getMonthLetter($i);
            $flag = 0;
            foreach ($trx_perbulan as $key => $value) {
                if ($value->bulan_trx==$i) {
                    $row['nilai'] = $value->bphtb_yang_dibayar;
                    $flag = 1;
                    break;
                }
            }

            if ($flag==0) {
                $row['nilai'] = 0;
            }

            $total += $row['nilai'];

            $trx_bulanan[] = $row;
        }

        $bulan_ini = $bulan_lalu = 0;
        foreach ($trx_bulanan as $key => $value) {
            if ($value['bulan_digit']==date('m')) {
                $bulan_ini = $value['nilai'];
            }

            if ($value['bulan_digit'] < date('m')) {
                $bulan_lalu += $value['nilai'];
            }
        }

        return view('pages.bphtb.dashboard')
            ->with('tahun', $tahun)
            ->with('ls', $ls)
            ->with('total', $total)
            ->with('trx_jenis_perolehan', $trx_jenis_perolehan)
            ->with('trx_bulanan', $trx_bulanan)
            ->with('all_year', $all_year)
            ->with('bulan_ini', $bulan_ini)
            ->with('bulan_lalu', $bulan_lalu);
    }

    public function dashboard_retribusi($tahun = null)
    {
        if (is_null($tahun)) {
            $tahun = date('Y');
        }

        $ls = LastSync::where('modul', 'web-r')->where('tahun', $tahun)->first();
        
        $all_year = TransaksiRetribusi::select(DB::RAW('distinct(tahun) as year'))->get();

        $retribusi_bulan = TransaksiRetribusi::select('bulan', 'tahun', DB::RAW('SUM(jumlah) as total'))
            ->where('tahun', $tahun)
            ->groupby('bulan', 'tahun')
            ->get();

        $retribusi_opd = TransaksiRetribusi::select('nama_opd', DB::RAW('SUM(jumlah) as total'))
            ->where('tahun', $tahun)
            ->groupby('nama_opd')
            ->orderby('total', 'desc')
            ->get();

        $retribusi_jenis = TransaksiRetribusi::select('jenis_retribusi', DB::RAW('SUM(jumlah) as total'))
            ->where('tahun', $tahun)
            ->groupby('jenis_retribusi')
            ->orderby('total', 'desc')
            ->get();

        $bulan_lalu = 0;
        $bulan_ini = 0;
        foreach ($retribusi_bulan as $key => $value) {
            if ($value->bulan==date('m')) {
                $bulan_ini = $value->total;
            }

            if ($value->bulan < date('m')) {
                $bulan_lalu += $value->total;
            }
        }

        return view('pages.webr.dashboard')
            ->with('tahun', $tahun)
            ->with('ls', $ls)
            ->with('retribusi_bulan', $retribusi_bulan)
            ->with('retribusi_opd', $retribusi_opd)
            ->with('retribusi_jenis', $retribusi_jenis)
            ->with('bulan_ini', $bulan_ini)
            ->with('bulan_lalu', $bulan_lalu)
            ->with('all_year', $all_year);
    }
}
