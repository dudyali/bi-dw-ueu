<?php

use GuzzleHttp\Client;
use App\Models\LastSync;
use Illuminate\Database\Seeder;
use App\Models\TransaksiRetribusi;

class SyncRetribusi extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
