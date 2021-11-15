<?php

namespace App\Jobs;

use App\Models\NOPD;
use App\Models\NPWPD;
use GuzzleHttp\Client;
use App\Models\LastSync;
use App\Models\JenisPajak;
use App\Models\KategoriPajak;
use Illuminate\Bus\Queueable;
use App\Models\TransaksiPajak;
use App\Models\TransaksiPajakDetail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncSimpad implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // sync parameter
        $tahun = date('Y');
        $bulan = date('m');
        // end of sync parameter

        $jenis_pajak = JenisPajak::where('tahun', $tahun)->get();

        foreach ($jenis_pajak as $jp) {
            $headers = [
                'Content-Type' => 'application/json',
            ];

            $array = [
                "method" => "get_op",
                "params" => array([
                    "data" => array([
                        "token" => "xxx",
                        "usaha" => $jp->nama,
                        "bulan" => $bulan,
                        "tahun" => $tahun
                    ])
                ])
            ];
    
            $client = new Client();
            $response = $client->request('POST','xxx', [
                'headers' => $headers,
                'body' => json_encode($array)
            ]);
    
            $res = json_decode($response->getBody()->getContents(), true);

            $id_jenis_pajak = $jp->id;
            
            foreach ($res['result']['params']['data'] as $key => $value) {
                $id_kategori_pajak = null;
                $checkkategori = KategoriPajak::where('nama', $value['jenis_pajak'])->where('tahun', $tahun)->first();
                if (is_null($checkkategori)) {
                    $kp = new KategoriPajak;
                    $kp->tahun = $tahun;
                    $kp->id_jenis_pajak = $id_jenis_pajak;
                    $kp->nama = $value['jenis_pajak'];
                    $kp->save();

                    $id_kategori_pajak = $kp->id;
                } else {
                    $id_kategori_pajak = $checkkategori->id;
                }

                foreach ($value['transaksi'] as $trx) {
                    $id_npwpd = null;
                    $checknpwpd = NPWPD::where('npwpd', $trx['npwpd'])->first();
                    if (is_null($checknpwpd)) {
                        $npwpd = new NPWPD;
                        $npwpd->npwpd = $trx['npwpd'];
                        $npwpd->nama_wp = $trx['nama_wp'];
                        $npwpd->save();

                        $id_npwpd = $npwpd->id;
                    } else {
                        $id_npwpd = $checknpwpd->id;
                    }

                    $id_nopd = null;
                    $checknopd = NOPD::where('nopd', $trx['nopd'])->where('id_npwpd', $id_npwpd)->first();
                    if (is_null($checknopd)) {
                        $nopd = new NOPD;
                        $nopd->id_npwpd = $id_npwpd;
                        $nopd->nopd = $trx['nopd'];
                        $nopd->nama_op = $trx['nama_op'];
                        $nopd->save();

                        $id_nopd = $nopd->id;
                    } else {
                        $id_nopd = $checknopd->id;
                    }

                    $checktp = TransaksiPajak::where('id_jenis_pajak', $id_jenis_pajak)
                        ->where('id_kategori_pajak', $id_kategori_pajak)
                        ->where('id_npwpd', $id_npwpd)
                        ->where('id_nopd', $id_nopd)
                        ->where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->first();

                    if (is_null($checktp)) {
                        $tp = new TransaksiPajak;
                        $tp->id_jenis_pajak = $id_jenis_pajak;
                        $tp->id_kategori_pajak = $id_kategori_pajak;
                        $tp->id_npwpd = $id_npwpd;
                        $tp->id_nopd = $id_nopd;
                        $tp->bulan = $bulan;
                        $tp->tahun = $tahun;
                        $tp->pokok = $trx['pokok'];
                        $tp->denda = $trx['denda'];
                        $tp->jumlah = $trx['jumlah'];
                        $tp->tgl_bayar = $trx['tgl_bayar'];
                        
                        if ($jp->nama=="REKLAME") {
                            $tp->periode_awal = $trx['periode_awal'];
                            $tp->periode_akhir = $trx['periode_akhir'];
                        }

                        $tp->save();

                        foreach ($trx['masa_pajak'] as $ms) {
                            $detail = new TransaksiPajakDetail;
                            $detail->id_transaksi_pajak = $tp->id;
                            
                            $exp = explode("-", $ms['bulan']);

                            $detail->bulan = $exp[0];
                            $detail->tahun = $exp[1];
                            $detail->pokok = $ms['pokok'];
                            $detail->denda = $ms['denda'];
                            $detail->jumlah = $ms['jumlah'];
                            $detail->save();
                        }
                    }
                }
            }     
        }

        $jp = JenisPajak::where('tahun', $tahun)->get();

        foreach ($jp as $key => $value) {
            $trx_pokok = TransaksiPajak::where('id_jenis_pajak', $value->id)->where('tahun', $tahun)->get()->sum('pokok');
            $trx_denda = TransaksiPajak::where('id_jenis_pajak', $value->id)->where('tahun', $tahun)->get()->sum('denda');
            $trx_jumlah = TransaksiPajak::where('id_jenis_pajak', $value->id)->where('tahun', $tahun)->get()->sum('jumlah');

            $editjp = JenisPajak::find($value->id);
            $editjp->pokok = $trx_pokok;
            $editjp->denda = $trx_denda;
            $editjp->total = $trx_jumlah;
            $editjp->save();
        }

        $kp = KategoriPajak::where('tahun', $tahun)->get();

        foreach ($kp as $key => $value) {
            $trx_pokok = TransaksiPajak::where('id_kategori_pajak', $value->id)->where('tahun', $tahun)->get()->sum('pokok');
            $trx_denda = TransaksiPajak::where('id_kategori_pajak', $value->id)->where('tahun', $tahun)->get()->sum('denda');
            $trx_jumlah = TransaksiPajak::where('id_kategori_pajak', $value->id)->where('tahun', $tahun)->get()->sum('jumlah');

            $editkp = KategoriPajak::find($value->id);
            $editkp->pokok = $trx_pokok;
            $editkp->denda = $trx_denda;
            $editkp->total = $trx_jumlah;
            $editkp->save();
        }

        $ls = LastSync::where('modul', 'simpad')->where('tahun', $tahun)->first();
        
        if (is_null($ls)) {
            $ls = new LastSync;
        }

        $ls->modul = 'simpad';
        $ls->tahun = $tahun;
        $ls->sync_at = date('Y-m-d H:i:s');
        $ls->save();
    }
}
