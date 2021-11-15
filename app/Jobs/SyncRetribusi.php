<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\LastSync;
use Illuminate\Bus\Queueable;
use App\Models\TransaksiRetribusi;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncWebRDaily implements ShouldQueue
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
        $tanggal = date('Y-m-d');
        
        $objRequest = '{
            "jsonrpc": "2.0",
            "method": "get_day",
            "params": {
                "data": {
                    "tanggal": "'.$tanggal.'"
                }
            },
            "id": 1
        }';

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $client = new Client();
        $response = $client->request('POST','xxx', [
            'headers' => $headers,
            'body' => $objRequest
        ]);

        $res = json_decode($response->getBody()->getContents(), true);

        foreach ($res['result']['data']['transaksi'] as $key => $data) {
            $tr = TransaksiRetribusi::where('kode_bayar', $data['kode_bayar'])->first();

            if (is_null($tr)) {
                $tr = new TransaksiRetribusi;
            }

            $tr->bulan = date('m');
            $tr->tahun = date('Y');
            $tr->kode_bayar = $data['kode_bayar'];
            $tr->tanggal_penerimaan = $data['tanggal_penerimaan'];
            $tr->nama_opd = $data['nama_opd'];
            $tr->jenis_retribusi = $data['jenis_retribusi'];
            $tr->jumlah = $data['jumlah'];
            $tr->save();
        }

        $ls = LastSync::where('modul', 'web-r')->where('tahun', date('Y'))->first();
        
        if (is_null($ls)) {
            $ls = new LastSync;
        }

        $ls->modul = 'web-r';
        $ls->tahun = date('Y');
        $ls->sync_at = date('Y-m-d H:i:s');
        $ls->save();
    }
}
