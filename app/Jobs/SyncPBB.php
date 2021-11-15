<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\LastSync;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiPBB;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncPBBDaily implements ShouldQueue
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

        $headers = [
            'Content-Type' => 'application/json',
        ];
        
        $array = [
            "tanggal" => $tanggal,
        ];

        $client = new Client();
        $response = $client->request('POST','xxx', [
            'headers' => $headers,
            'body' => json_encode($array)
        ]);

        $res = json_decode($response->getBody()->getContents(), true);

        foreach ($res['transaksi'] as $key => $value) {
            $district_code = substr($value['nop'], 4, 3);
            $village_code = substr($value['nop'], 7, 3);
    
            $district = Kecamatan::where('district_code', $district_code)->first();
            $district_id = null;
            if ($district) {
                $district_id = $district->id;
            }
    
            $village_id = null;
            if (!is_null($district_id)) {
                $village = Kelurahan::where('district_id', $district_id)->where('village_code', $village_code)->first();
                if ($village) {
                    $village_id = $village->id;
                }
            }

            $pbb = TransaksiPBB::where('tahun_pajak', $value['tahun'])
                ->where('tanggal_tx', $value['tanggal_tx'])
                ->where('nop', $value['nop'])
                ->first();

            if (is_null($pbb)) {
                $pbb = new TransaksiPBB;
                $pbb->district_id = $district_id;
                $pbb->village_id = $village_id;
                $pbb->tanggal_tx = $value['tanggal_tx'];
                $pbb->jam_tx = $value['jam_tx'];
                $pbb->tahun_pajak = $value['tahun'];
                $pbb->tahun_bayar = explode('-', $value['tanggal_tx'])[0];
                $pbb->nop = $value['nop'];
                $pbb->nama_wp = $value['nama_wp'];
                $pbb->pokok = $value['pokok_pajak'];
                $pbb->denda = $value['denda'];
                $pbb->potongan = $value['potongan'];
                $pbb->admin = $value['admin'];
                $pbb->total = $value['total'];
                $pbb->save();
            }
        }

        $ls = LastSync::where('modul', 'smartgov-pbb')->where('tahun', date('Y'))->first();
        
        if (is_null($ls)) {
            $ls = new LastSync;
        }

        $ls->modul = 'smartgov-pbb';
        $ls->tahun = date('Y');
        $ls->sync_at = date('Y-m-d H:i:s');
        $ls->save();
    }
}
