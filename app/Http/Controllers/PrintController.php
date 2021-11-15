<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrintController extends Controller
{
    public function all_by_kecamatan()
    {
        $kecamatan = Kecamatan::with('transaction')->get();

        return view('pages.print.all-by-kecamatan')->with('kecamatan', $kecamatan);
    }

    public function all_by_channel()
    {
        $channel = Channel::with('transaction')->get();

        return view('pages.print.all-by-channel')->with('channel', $channel);
    }

    public function kecamatan_by_kelurahan($id_kecamatan)
    {
        $kecamatan = Kecamatan::findOrFail($id_kecamatan);
        $kelurahan = Kelurahan::where('district_id', $id_kecamatan)->with('transaction')->get();

        return view('pages.print.kecamatan-by-kelurahan')
            ->with('kecamatan', $kecamatan)
            ->with('kelurahan', $kelurahan);
    }

    public function kecamatan_by_channel($id_kecamatan)
    {
        $kecamatan = Kecamatan::findOrFail($id_kecamatan);

        $channel = Channel::with(['transaction' => function($query) use($id_kecamatan) {
            $query->where('district_id', $id_kecamatan);
        }])->get();

        return view('pages.print.kecamatan-by-channel')
            ->with('kecamatan', $kecamatan)
            ->with('channel', $channel);
    }

    public function transaction_by_channel($id_channel, $id_kecamatan)
    {
        $kecamatan = Kecamatan::findOrFail($id_kecamatan);
        $channel = Channel::findOrFail($id_channel);
        $transaction = Transaction::where('id_channel', $id_channel)
            ->where('district_id', $id_kecamatan)
            ->orderby('tg_tx', 'desc')
            ->orderby('jm_tx', 'desc')
            ->get();

        return view('pages.print.transaction-by-channel')
            ->with('kecamatan', $kecamatan)
            ->with('channel', $channel)
            ->with('transaction', $transaction);
    }

    public function kelurahan_all_channel($id_kecamatan, $id_kelurahan)
    {
        $kecamatan = Kecamatan::findOrFail($id_kecamatan);
        $kelurahan = Kelurahan::findOrFail($id_kelurahan);
        $transaction = Transaction::where('village_id', $id_kelurahan)->orderby('tg_tx', 'desc')->orderby('jm_tx', 'desc')->get();

        return view('pages.print.kelurahan-all-channel')
            ->with('transaction', $transaction)
            ->with('kecamatan', $kecamatan)
            ->with('kelurahan', $kelurahan);
    }
}
