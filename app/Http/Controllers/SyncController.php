<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\JenisPajak;
use Illuminate\Http\Request;
use App\Models\KategoriPajak;
use App\Models\TransaksiBPHTB;
use App\Models\TransaksiPajak;
use App\Models\TransaksiRetribusi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SyncController extends Controller
{
    public function get_anggaran_simpad()
    {
        $tahun = '2021';

        $array = [
            "jsonrpc" => "2.0",
            "method" => "get_target_anggaran",
            "params" => array([
                "data" => array([
                    "token" => "89ec2deaf766818bac83c4ed818d10bb",
                    "tahun" => $tahun
                ])
            ])
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $client = new Client();
        $response = $client->request('POST','https://pajak.tangerangkab.go.id/wspdl-tangerang-kab/api/server', [
            'headers' => $headers,
            'body' => json_encode($array)
        ]);

        $res = json_decode($response->getBody()->getContents(), true);

        foreach ($res['result']['params']['data'] as $key => $value) {
            $jp = JenisPajak::where('nama', $value['jenis_pajak'])->where('tahun', $value['tahun'])->firstOrFail();
            $jp->target = $value['target_anggaran'];
            $jp->save();

            foreach ($value['kategori_pajak'] as $key => $kat) {
                $kp = KategoriPajak::where('tahun', $value['tahun'])->where('id_jenis_pajak', $jp->id)->where('nama', $kat['nama_kategori'])->first();
                if (is_null($kp)) {
                    $kp = new KategoriPajak;
                }
                $kp->tahun = $tahun;
                $kp->id_jenis_pajak = $jp->id;
                $kp->nama = $kat['nama_kategori'];
                $kp->target = $kat['target_anggaran'];
                $kp->save();
            }
        }

        return "done!";
    }

    public function get_penerimaan_bphtb()
    {
        $bulan = '01';
        $tahun = '2021';

        $array = [
            "jsonrpc" => "2.0",
            "method" => "get_penerimaan",
            "params" => array([
                "data" => array([
                    "token" => "c100685c7e9f294396e3ab6f48f317f1",
                    "bulan" => $bulan,
                    "tahun" => $tahun
                ])
            ])
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $client = new Client();
        $response = $client->request('POST','https://bphtb.tangerangkab.go.id/wsbphtb-tangerang-kab/api/server', [
            'headers' => $headers,
            'body' => json_encode($array)
        ]);

        $res = json_decode($response->getBody()->getContents(), true);

        foreach ($res['result']['params']['data'] as $key => $data) {
            foreach ($data['transaksi'] as $key => $value) {
                $tb = new TransaksiBPHTB;
                $tb->bulan_trx = $bulan;
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

        return "done";
    }

    public function get_retribusi_bulanan()
    {
        $tahun = '2021';
        
        for ($bulan=1; $bulan <= date('m'); $bulan++) { 
            $objRequest = '{
                "jsonrpc": "2.0",
                "method": "get_month",
                "params": {
                    "data": {
                        "bulan": "'.$bulan.'",
                        "tahun": "'.$tahun.'"
                    }
                },
                "id": 1
            }';

            $headers = [
                'Content-Type' => 'application/json',
            ];

            $client = new Client();
            $response = $client->request('POST','http://webr.tangkab.dapda.id/api/webr', [
                'headers' => $headers,
                'body' => $objRequest
            ]);

            $res = json_decode($response->getBody()->getContents(), true);

            foreach ($res['result']['data']['transaksi'] as $key => $data) {
                $tr = TransaksiRetribusi::where('kode_bayar', $data['kode_bayar'])->first();

                if (is_null($tr)) {
                    $tr = new TransaksiRetribusi;
                }

                $tr->bulan = $bulan;
                $tr->tahun = $tahun;
                $tr->kode_bayar = $data['kode_bayar'];
                $tr->tanggal_penerimaan = $data['tanggal_penerimaan'];
                $tr->nama_opd = $data['nama_opd'];
                $tr->jenis_retribusi = $data['jenis_retribusi'];
                $tr->jumlah = $data['jumlah'];
                $tr->save();
            }
        }

        return "done";
    }

    public function get_retribusi_harian()
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
        $response = $client->request('POST','http://webr.tangkab.dapda.id/api/webr', [
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

        return "done";
    }
}
