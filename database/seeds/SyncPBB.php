<?php

use GuzzleHttp\Client;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiPBB;
use Illuminate\Database\Seeder;

class SyncPBB extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tahuns = [2017, 2018, 2019, 2020, 2021];
        
        $this->command->getOutput()->progressStart(count($tahuns));
        
        foreach ($tahuns as $key => $tahun) {
            $bulan = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
            
            foreach ($bulan as $key => $value) {
                $headers = [
                    'Content-Type' => 'application/json',
                ];
                
                $array = [
                    "bulan" => $value,
                    "tahun" => $tahun
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
    
                    $pbb = new TransaksiPBB;
                    $pbb->district_id = $district_id;
                    $pbb->village_id = $village_id;
                    $pbb->tanggal_tx = $value['tanggal_tx'];
                    $pbb->jam_tx = $value['jam_tx'];
                    $pbb->tahun_pajak = $value['tahun'];
                    $pbb->tahun_bayar = $tahun;
                    $pbb->nop = $value['nop'];
                    $pbb->nama_wp = $value['nama_wp'];
                    $pbb->pokok = $value['pokok_pajak'];
                    $pbb->denda = $value['denda'];
                    $pbb->potongan = $value['potongan'];
                    $pbb->admin = $value['admin'];
                    $pbb->total = $value['total'];
                    $pbb->save();
                }
    
                $this->command->getOutput()->progressAdvance();
            }
        }

        $this->command->getOutput()->progressFinish();
    }
}
