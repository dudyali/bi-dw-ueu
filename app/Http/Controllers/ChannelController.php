<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        $data = Channel::get();

        return view('pages.master.channel.index')->with('data', $data);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'nama' => 'required'
        ]);

        $data = new Channel;
        $data->nama = strtoupper($request->nama);
        $data->save();

        return redirect()->route('channel.index')->with('success', 'Berhasil memasukkan channel baru.');
    }

    public function update(Request $request, $id)
    {
        $validation = $request->validate([
            'nama' => 'required'
        ]);

        $data = Channel::findOrFail($id);
        $data->nama = strtoupper($request->nama);
        $data->save();

        return redirect()->route('channel.index')->with('success', 'Berhasil mengubah channel.');
    }

    public function edit($id)
    {
        return Channel::findOrFail($id);
    }

    public function destroy($id)
    {
        Channel::destroy($id);

        return redirect()->route('channel.index')->with('success', 'Berhasil menghapus channel.');
    }
}
