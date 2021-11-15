<?php

namespace App\Http\Controllers;

use App\Models\NOPD;
use GuzzleHttp\Client;
use App\Models\JenisPajak;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransaksiBPHTB;
use App\Models\TransaksiPajak;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MCPKPKController extends Controller
{
    public function checkPBB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string|min:18',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        // cek pbb
        $pbbCheck = Transaction::where('nop', $request->nop)->orderby('tahun', 'asc')->get();

        if ($pbbCheck->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => "Can't find this nop in PBB database."
            ], 404);
        }

        $tunggakan = 0;
        $tahunMenunggak = [];
        $tahunSekarang = (int) date('Y');
        $tahunAwalBayar = $pbbCheck[0]->tahun;
        $tahunTelahBayar = $pbbCheck->pluck('tahun')->toArray();

        for ($i=$tahunSekarang; $i >= $tahunAwalBayar; $i--) { 
            if (!in_array($i, $tahunTelahBayar)) {
                $tunggakan = 1;    
                $tahunMenunggak[] = $i;
            }
        }

        $data = [
            "status" => ($tunggakan ? "NOT_VALID" : "VALID"),
            "keterangan" => ($tunggakan ? "Terdapat tunggakan." : "Tidak ada tunggakan."),
            "tahun_tunggakan" => $tahunMenunggak,
        ];

        return response()->json([
            'status_code' => 200,
            'message' => "Success",
            'data' => $data
        ], 200);   
    }

    public function checkBPHTB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string|min:18',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $bphtbCheck = TransaksiBPHTB::where('nop', $request->nop)->first();
     
        if (is_null($bphtbCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => "Can't find this nop in BPHTB database."
            ], 404);
        }

        $data = [
            "status" => "VALID",
            "keterangan" => "NOP found in BPHTB database.",
            "transaksi_terakhir" => [
                "no_trx" => $bphtbCheck->no_transaksi,
                "jenis_trx" => $bphtbCheck->jenis_perolehan,
                "tanggal_trx" => $bphtbCheck->tgl_bayar
            ]
        ];

        return response()->json([
            'status_code' => 200,
            'message' => "Success",
            'data' => $data
        ], 200);   
    }

    public function checkReklame(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Validation error!',
                'data' => $validator->messages()
            ], 400);
        }

        $array_nop = str_replace(";", ",", $request->nop);

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $array = [
            "jsonrpc" => "2.0",
            "method" => "get_tunggakan_reklame_nopd",
            "params" => array([
                "data" => array([
                    "token" => "89ec2deaf766818bac83c4ed818d10bb",
                    "nopd" => $array_nop,
                ])
            ])
        ];

        $client = new Client();
        $response = $client->request('POST','https://pajak.tangerangkab.go.id/wspdl-tangerang-kab/api/server', [
            'headers' => $headers,
            'body' => json_encode($array)
        ]);

        $res = json_decode($response->getBody()->getContents(), true);

        $data = [];
        foreach ($res['result']['params']['data'] as $key => $value) {
            $row = [];
            $row['nopd'] = $value['nopd'];
            if ($value['jenis_pajak'] == "-") {
                $row['status'] = "NOT VALID";
                $row['kode_keterangan'] = 2;
                $row['keterangan'] = "NOPD tidak valid.";
                $row['data_wp'] = null;
                $row['tunggakan'] = null;
            } else {
                if ($value['transaksi']=="Tunggakan Tidak Ditemukan") {
                    $row['status'] = "VALID";
                    $row['kode_keterangan'] = 1;
                    $row['keterangan'] = "Tidak memiliki tunggakan.";

                    $wp = [];
                    $wp['npwpd'] = $value['npwpd'];
                    $wp['nama_wp'] = $value['nama_wp'];
                    $wp['nopd'] = $value['nopd'];
                    $wp['nama_op'] = $value['nama_op'];
                    $wp['jenis_pajak'] = $value['jenis_pajak'];

                    $row['data_wp'] = $wp;
                    $row['tunggakan'] = null;
                } else {
                    $row['status'] = "NOT VALID";
                    $row['kode_keterangan'] = 3;
                    $row['keterangan'] = "Memiliki tunggakan.";
    
                    $wp = [];
                    $wp['npwpd'] = $value['transaksi']['npwpd'];
                    $wp['nama_wp'] = $value['transaksi']['nama_wp'];
                    $wp['nopd'] = $value['nopd'];
                    $wp['nama_op'] = $value['transaksi']['nama_op'];
                    $wp['jenis_pajak'] = $value['transaksi']['jenis_pajak'];
                    $row['data_wp'] = $wp;
    
                    $tunggakan = [];
                    $tunggakan['tahun'] = $value['transaksi']['tahun'];
                    $tunggakan['bulan_ketetapan'] = $value['transaksi']['tanggal_ketetapan'];
                    $tunggakan['tanggal_jatuh_tempo'] = $value['transaksi']['jatuhtempotgl'];
                    $tunggakan['pokok'] = $value['transaksi']['pokok'];
                    $tunggakan['denda'] = $value['transaksi']['denda'];
                    $row['tunggakan'] = $tunggakan;
                }
            }

            $data[] = $row;
        }
        
        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => $data
        ]);
    }

    public function checkAirTanah(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $nopCheck = NOPD::where('nopd', $request->nop)->first();
        
        if (is_null($nopCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'NOP tidak ditemukan; (Format NOP tidak valid atau NOP tidak terdaftar atau tidak ada transaksi sejak tahun 2020)'
            ], 404);
        }

        $jenisPajakID = JenisPajak::where('nama', 'AIR TANAH')->get()->pluck('id');

        $checkTransaksi = TransaksiPajak::where('id_nopd', $nopCheck->id)->wherein('id_jenis_pajak', $jenisPajakID)->get();

        if ($checkTransaksi->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Bukan NOP Air Tanah.'
            ], 404);
        }

        $tahunTersedia = $checkTransaksi->pluck('tahun')->unique()->sort();
        
        $masaPajakTersedia = $checkTransaksi->pluck('masa_pajak');
        $kumpulanMasaPajak = [];
        foreach ($masaPajakTersedia as $value) {
            $pecah = explode(",", $value);
            foreach ($pecah as $p) {
                $kumpulanMasaPajak[] = $p;
            }
        }

        $bulanTunggakan = [];
        foreach ($tahunTersedia as $value) {
            $bulanAkhir = 12;
            if ($value==date('Y')) {
                $bulanAkhir = ((int) date('m')) - 2;
            }
            for ($i=1; $i <= $bulanAkhir ; $i++) { 
                $keywordPencarian = "$i-$value";
                if ($i < 10) {
                    $keywordPencarian = "0$i-$value";
                }
                if (!in_array($keywordPencarian, $kumpulanMasaPajak)) {
                    $bulanTunggakan[] = $keywordPencarian;
                }
            }            
        }

        if (count($bulanTunggakan)!=0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    "status" => "NOT_VALID",
                    "keterangan" => "Terdapat tunggakan untuk NOPD tersebut.",
                    "data_tunggakan" => $bulanTunggakan
                ]
            ], 200);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => [
                "status" => "VALID",
                "keterangan" => "NOP telah melengkapi pembayaran.",
                "data_tunggakan" => null
            ]
        ], 200);
    }

    public function checkHiburan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $nopCheck = NOPD::where('nopd', $request->nop)->first();
        
        if (is_null($nopCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'NOP tidak ditemukan; (Format NOP tidak valid atau NOP tidak terdaftar atau tidak ada transaksi sejak tahun 2020)'
            ], 404);
        }

        $jenisPajakID = JenisPajak::where('nama', 'HIBURAN')->get()->pluck('id');

        $checkTransaksi = TransaksiPajak::where('id_nopd', $nopCheck->id)->wherein('id_jenis_pajak', $jenisPajakID)->get();

        if ($checkTransaksi->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Bukan NOP Hiburan.'
            ], 404);
        }

        $tahunTersedia = $checkTransaksi->pluck('tahun')->unique()->sort();
        
        $masaPajakTersedia = $checkTransaksi->pluck('masa_pajak');
        $kumpulanMasaPajak = [];
        foreach ($masaPajakTersedia as $value) {
            $pecah = explode(",", $value);
            foreach ($pecah as $p) {
                $kumpulanMasaPajak[] = $p;
            }
        }

        $bulanTunggakan = [];
        foreach ($tahunTersedia as $value) {
            $bulanAkhir = 12;
            if ($value==date('Y')) {
                $bulanAkhir = ((int) date('m')) - 2;
            }
            for ($i=1; $i <= $bulanAkhir ; $i++) { 
                $keywordPencarian = "$i-$value";
                if ($i < 10) {
                    $keywordPencarian = "0$i-$value";
                }
                if (!in_array($keywordPencarian, $kumpulanMasaPajak)) {
                    $bulanTunggakan[] = $keywordPencarian;
                }
            }            
        }

        if (count($bulanTunggakan)!=0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    "status" => "NOT_VALID",
                    "keterangan" => "Terdapat tunggakan untuk NOPD tersebut.",
                    "data_tunggakan" => $bulanTunggakan
                ]
            ], 200);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => [
                "status" => "VALID",
                "keterangan" => "NOP telah melengkapi pembayaran.",
                "data_tunggakan" => null
            ]
        ], 200);
    }

    public function checkParkir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $nopCheck = NOPD::where('nopd', $request->nop)->first();
        
        if (is_null($nopCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'NOP tidak ditemukan; (Format NOP tidak valid atau NOP tidak terdaftar atau tidak ada transaksi sejak tahun 2020)'
            ], 404);
        }

        $jenisPajakID = JenisPajak::where('nama', 'PARKIR')->get()->pluck('id');

        $checkTransaksi = TransaksiPajak::where('id_nopd', $nopCheck->id)->wherein('id_jenis_pajak', $jenisPajakID)->get();

        if ($checkTransaksi->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Bukan NOP Parkir.'
            ], 404);
        }

        $tahunTersedia = $checkTransaksi->pluck('tahun')->unique()->sort();
        
        $masaPajakTersedia = $checkTransaksi->pluck('masa_pajak');
        $kumpulanMasaPajak = [];
        foreach ($masaPajakTersedia as $value) {
            $pecah = explode(",", $value);
            foreach ($pecah as $p) {
                $kumpulanMasaPajak[] = $p;
            }
        }

        $bulanTunggakan = [];
        foreach ($tahunTersedia as $value) {
            $bulanAkhir = 12;
            if ($value==date('Y')) {
                $bulanAkhir = ((int) date('m')) - 2;
            }
            for ($i=1; $i <= $bulanAkhir ; $i++) { 
                $keywordPencarian = "$i-$value";
                if ($i < 10) {
                    $keywordPencarian = "0$i-$value";
                }
                if (!in_array($keywordPencarian, $kumpulanMasaPajak)) {
                    $bulanTunggakan[] = $keywordPencarian;
                }
            }            
        }

        if (count($bulanTunggakan)!=0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    "status" => "NOT_VALID",
                    "keterangan" => "Terdapat tunggakan untuk NOPD tersebut.",
                    "data_tunggakan" => $bulanTunggakan
                ]
            ], 200);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => [
                "status" => "VALID",
                "keterangan" => "NOP telah melengkapi pembayaran.",
                "data_tunggakan" => null
            ]
        ], 200);
    }

    public function checkHotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $nopCheck = NOPD::where('nopd', $request->nop)->first();
        
        if (is_null($nopCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'NOP tidak ditemukan; (Format NOP tidak valid atau NOP tidak terdaftar atau tidak ada transaksi sejak tahun 2020)'
            ], 404);
        }

        $jenisPajakID = JenisPajak::where('nama', 'HOTEL')->get()->pluck('id');

        $checkTransaksi = TransaksiPajak::where('id_nopd', $nopCheck->id)->wherein('id_jenis_pajak', $jenisPajakID)->get();

        if ($checkTransaksi->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Bukan NOP Hotel.'
            ], 404);
        }

        $tahunTersedia = $checkTransaksi->pluck('tahun')->unique()->sort();
        
        $masaPajakTersedia = $checkTransaksi->pluck('masa_pajak');
        $kumpulanMasaPajak = [];
        foreach ($masaPajakTersedia as $value) {
            $pecah = explode(",", $value);
            foreach ($pecah as $p) {
                $kumpulanMasaPajak[] = $p;
            }
        }

        $bulanTunggakan = [];
        foreach ($tahunTersedia as $value) {
            $bulanAkhir = 12;
            if ($value==date('Y')) {
                $bulanAkhir = ((int) date('m')) - 2;
            }
            for ($i=1; $i <= $bulanAkhir ; $i++) { 
                $keywordPencarian = "$i-$value";
                if ($i < 10) {
                    $keywordPencarian = "0$i-$value";
                }
                if (!in_array($keywordPencarian, $kumpulanMasaPajak)) {
                    $bulanTunggakan[] = $keywordPencarian;
                }
            }            
        }

        if (count($bulanTunggakan)!=0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    "status" => "NOT_VALID",
                    "keterangan" => "Terdapat tunggakan untuk NOPD tersebut.",
                    "data_tunggakan" => $bulanTunggakan
                ]
            ], 200);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => [
                "status" => "VALID",
                "keterangan" => "NOP telah melengkapi pembayaran.",
                "data_tunggakan" => null
            ]
        ], 200);
    }

    public function checkPeneranganJalan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $nopCheck = NOPD::where('nopd', $request->nop)->first();
        
        if (is_null($nopCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'NOP tidak ditemukan; (Format NOP tidak valid atau NOP tidak terdaftar atau tidak ada transaksi sejak tahun 2020)'
            ], 404);
        }

        $jenisPajakID = JenisPajak::where('nama', 'PENERANGAN JALAN')->get()->pluck('id');

        $checkTransaksi = TransaksiPajak::where('id_nopd', $nopCheck->id)->wherein('id_jenis_pajak', $jenisPajakID)->get();

        if ($checkTransaksi->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Bukan NOP Penerangan Jalan.'
            ], 404);
        }

        $tahunTersedia = $checkTransaksi->pluck('tahun')->unique()->sort();
        
        $masaPajakTersedia = $checkTransaksi->pluck('masa_pajak');
        $kumpulanMasaPajak = [];
        foreach ($masaPajakTersedia as $value) {
            $pecah = explode(",", $value);
            foreach ($pecah as $p) {
                $kumpulanMasaPajak[] = $p;
            }
        }

        $bulanTunggakan = [];
        foreach ($tahunTersedia as $value) {
            $bulanAkhir = 12;
            if ($value==date('Y')) {
                $bulanAkhir = ((int) date('m')) - 2;
            }
            for ($i=1; $i <= $bulanAkhir ; $i++) { 
                $keywordPencarian = "$i-$value";
                if ($i < 10) {
                    $keywordPencarian = "0$i-$value";
                }
                if (!in_array($keywordPencarian, $kumpulanMasaPajak)) {
                    $bulanTunggakan[] = $keywordPencarian;
                }
            }            
        }

        if (count($bulanTunggakan)!=0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    "status" => "NOT_VALID",
                    "keterangan" => "Terdapat tunggakan untuk NOPD tersebut.",
                    "data_tunggakan" => $bulanTunggakan
                ]
            ], 200);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => [
                "status" => "VALID",
                "keterangan" => "NOP telah melengkapi pembayaran.",
                "data_tunggakan" => null
            ]
        ], 200);
    }

    public function checkRestoran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nop' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }

        $nopCheck = NOPD::where('nopd', $request->nop)->first();
        
        if (is_null($nopCheck)) {
            return response()->json([
                'status_code' => 404,
                'message' => 'NOP tidak ditemukan; (Format NOP tidak valid atau NOP tidak terdaftar atau tidak ada transaksi sejak tahun 2020)'
            ], 404);
        }

        $jenisPajakID = JenisPajak::where('nama', 'RESTORAN')->get()->pluck('id');

        $checkTransaksi = TransaksiPajak::where('id_nopd', $nopCheck->id)->wherein('id_jenis_pajak', $jenisPajakID)->get();

        if ($checkTransaksi->count()==0) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Bukan NOP Restoran.'
            ], 404);
        }

        $tahunTersedia = $checkTransaksi->pluck('tahun')->unique()->sort();
        
        $masaPajakTersedia = $checkTransaksi->pluck('masa_pajak');
        $kumpulanMasaPajak = [];
        foreach ($masaPajakTersedia as $value) {
            $pecah = explode(",", $value);
            foreach ($pecah as $p) {
                $kumpulanMasaPajak[] = $p;
            }
        }

        $bulanTunggakan = [];
        foreach ($tahunTersedia as $value) {
            $bulanAkhir = 12;
            if ($value==date('Y')) {
                $bulanAkhir = ((int) date('m')) - 2;
            }
            for ($i=1; $i <= $bulanAkhir ; $i++) { 
                $keywordPencarian = "$i-$value";
                if ($i < 10) {
                    $keywordPencarian = "0$i-$value";
                }
                if (!in_array($keywordPencarian, $kumpulanMasaPajak)) {
                    $bulanTunggakan[] = $keywordPencarian;
                }
            }            
        }

        if (count($bulanTunggakan)!=0) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Success',
                'data' => [
                    "status" => "NOT_VALID",
                    "keterangan" => "Terdapat tunggakan untuk NOPD tersebut.",
                    "data_tunggakan" => $bulanTunggakan
                ]
            ], 200);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'data' => [
                "status" => "VALID",
                "keterangan" => "NOP telah melengkapi pembayaran.",
                "data_tunggakan" => null
            ]
        ], 200);
    }
}
