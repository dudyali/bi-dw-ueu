<?php

use GuzzleHttp\Client;
use App\Models\TransaksiBPHTB;
use Illuminate\Database\Seeder;

class SyncBPHTB extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tahuns = [2019, 2020, 2021];

        $this->command->getOutput()->progressStart(count($tahuns));

        foreach ($tahuns as $key => $tahun) {
            $bulan = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    
            foreach ($bulan as $key => $month) {
                $array = [
                    "jsonrpc" => "2.0",
                    "method" => "get_penerimaan",
                    "params" => array([
                        "data" => array([
                            "token" => "xxx",
                            "bulan" => $month,
                            "tahun" => $tahun
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
                            $tb->bulan_trx = (int) $month;
                            $tb->tahun_trx = $tahun;
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
    
                $this->command->getOutput()->progressAdvance();
            }
        }

        $this->command->getOutput()->progressFinish();
    }
}
