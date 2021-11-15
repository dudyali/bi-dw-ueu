<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PBBController extends Controller
{
    public function perkelurahan($id_kecamatan)
    {
        $kecamatan = Kecamatan::findOrFail($id_kecamatan);
        $kelurahan = Kelurahan::where('district_id', $id_kecamatan)->with('transaction')->get();
        $transaction = Transaction::where('district_id', $id_kecamatan)->get();

        $channel = Channel::with(['transaction' => function($query) use($id_kecamatan) {
            $query->where('district_id', $id_kecamatan);
        }])->get();

        return view('pages.pbb.perkelurahan')
            ->with('transaction', $transaction)
            ->with('channel', $channel)
            ->with('kecamatan', $kecamatan)
            ->with('kelurahan', $kelurahan);
    }

    public function detail_perkelurahan($id_kecamatan, $id_kelurahan)
    {
        $kecamatan = Kecamatan::findOrFail($id_kecamatan);
        $kelurahan = Kelurahan::findOrFail($id_kelurahan);
        $transaction = Transaction::where('village_id', $id_kelurahan)->orderby('tg_tx', 'desc')->orderby('jm_tx', 'desc')->get();

        return view('pages.pbb.detail-perkelurahan')
            ->with('transaction', $transaction)
            ->with('kecamatan', $kecamatan)
            ->with('kelurahan', $kelurahan);
    }

    public function perchannel($id_channel, $id_kecamatan = null)
    {
        $channel = Channel::findOrFail($id_channel);

        if (is_null($id_kecamatan)) {
            $transaction = Transaction::where('id_channel', $id_channel)
                ->orderby('tg_tx', 'desc')
                ->orderby('jm_tx', 'desc')
                ->get();

            return view('pages.pbb.perchannel')
                ->with('transaction', $transaction)
                ->with('channel', $channel);
        }

        $kecamatan = Kecamatan::findOrFail($id_kecamatan);
        $transaction = Transaction::where('id_channel', $id_channel)
            ->where('district_id', $id_kecamatan)
            ->orderby('tg_tx', 'desc')
            ->orderby('jm_tx', 'desc')
            ->get();

        return view('pages.pbb.perchannel')
            ->with('kecamatan', $kecamatan)
            ->with('transaction', $transaction)
            ->with('channel', $channel);
    }
}
