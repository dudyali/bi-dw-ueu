<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function index()
    {
        $data = Kecamatan::with('kelurahan')->get();

        return view('pages.master.kecamatan.index')->with('data', $data);
    }

    public function update(Request $request, $id)
    {
        $validation = $request->validate([
            'name' => 'required',
        ]);

        $data = Kecamatan::findOrFail($id);
        $data->name = strtoupper($request->name);
        $data->district_code = $request->district_code;
        $data->save();

        return redirect()->route('kecamatan.index')->with('success', 'Berhasil mengubah kecamatan.');
    }

    public function edit($id)
    {
        return Kecamatan::findOrFail($id);
    }
}
