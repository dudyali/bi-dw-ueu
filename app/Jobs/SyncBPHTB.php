<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\LastSync;
use Illuminate\Bus\Queueable;
use App\Models\TransaksiBPHTB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncBPHTB implements ShouldQueue
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
        $array = [
            "jsonrpc" => "2.0",
            "method" => "get_penerimaan_pertanggal",
            "params" => array([
                "data" => array([
                    "token" => "xxx",
                    "tanggal" => date('Y-m-d')
                ])
            ])
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $client = new Client();
        $response = $client->request('POST','xxx', [
            'headers' => $headers,
            'body' => json_encode($array)
        ]);

        $res = json_decode($response->getBody()->getContents(), true);

        foreach ($res['result']['params']['data'] as $key => $data) {
            foreach ($data['transaksi'] as $key => $value) {
                $check = TransaksiBPHTB::where('no_transaksi', $value['no_transaksi'])->first();
                if (is_null($check)) {
                    $tb = new TransaksiBPHTB;
                    $tb->bulan_trx = date('m');
                    $tb->tahun_trx = date('Y');
                    $tb->jenis_sspd = $value['jenis_sspd'];
                    $tb->nop = $value['nop'];
                    $tb->nik = $value['nik'];
                    $tb->nama_wp = $value['nama_wp'];
                    $tb->kelurahan_wp = $value['kelurahan'];
                    $tb->kecamatan_wp = $value['kelurahan'];
                    $tb->nama_notaris = $value['nama_notaris'];
                    $tb->no_transaksi = $value['no_transaksi'];
                    $tb->jenis_perolehan = $value['jenis_perolehan'];
                    $tb->luas_tanah = $value['luas_tanah'];
                    $tb->luas_bangunan = $value['luas_bangunan'];
                    $tb->njop = $value['njop'];
                    $tb->npoptkp = $value['npoptkp'];
                    $tb->no_sspd = $value['no_sspd'];
                    $tb->tgl_sspd = $value['tgl_sspd'];
                    $tb->tgl_bayar = $value['tgl_bayar'];
                    $tb->bphtb_belum_diskon = $value['bphtb_belum_diskon'];
                    $tb->bphtb_besar_pengurangan = $value['bphtb_besar_pengurangan'];
                    $tb->bphtb_yang_dibayar = $value['bphtb_yang_dibayar'];
                    $tb->no_sspd_awal = $value['no_sspd_awal'];
                    $tb->tgl_transaksi_bayar_sspd_awal = $value['tgl_transaksi_bayar_sspd_awal'];
                    $tb->bphtb_terhutang_sspd_awal = $value['bphtb_terhutang_sspd_awal'];
                    $tb->bphtb_yang_telah_dibayar_sspd_awal = $value['bphtb_yang_telah_dibayar_sspd_awal'];
                    $tb->save();   
                }
            }
        }

        $ls = LastSync::where('modul', 'e-bphtb')->where('tahun', date('Y'))->first();
        
        if (is_null($ls)) {
            $ls = new LastSync;
        }

        $ls->modul = 'e-bphtb';
        $ls->tahun = date('Y');
        $ls->sync_at = date('Y-m-d H:i:s');
        $ls->save();
    }
}
