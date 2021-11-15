<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class KelurahanController extends Controller
{
    public function index($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $data = Kelurahan::where('district_id', $id)->get();

        return view('pages.master.kelurahan.index')
            ->with('kecamatan', $kecamatan)
            ->with('data', $data);
    }

    public function update(Request $request, $id)
    {
        $validation = $request->validate([
            'name' => 'required',
        ]);

        $data = Kelurahan::findOrFail($id);
        $data->name = strtoupper($request->name);
        $data->village_code = $request->village_code;
        $data->save();

        return redirect()->route('kelurahan.index', $data->district_id)->with('success', 'Berhasil mengubah kelurahan.');
    }

    public function edit($id)
    {
        return Kelurahan::findOrFail($id);
    }

    public function getByKecamatan($id_kecamatan)
    {
        return Kelurahan::where('district_id', $id_kecamatan)->get();
    }
}
