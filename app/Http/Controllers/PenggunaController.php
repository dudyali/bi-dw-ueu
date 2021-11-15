<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        $data = User::get();

        return view('pages.master.pengguna.index')->with('data', $data);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required|email|unique:users,email'
        ]);

        $data = new User;
        $data->name = ucwords($request->name);
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->save();

        return redirect()->route('pengguna.index')->with('success', 'Berhasil memasukkan pengguna baru.');
    }

    public function update(Request $request, $id)
    {
        $validation = $request->validate([
            'name' => 'required',
            'password' => 'nullable|confirmed',
            'password_confirmation' => 'nullable',
            'email' => 'required|email|unique:users,email,'.$id
        ]);

        $data = User::findOrFail($id);
        $data->name = ucwords($request->name);
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->save();

        return redirect()->route('pengguna.index')->with('success', 'Berhasil mengubah pengguna.');
    }

    public function edit($id)
    {
        return User::findOrFail($id);
    }

    public function destroy($id)
    {
        User::destroy($id);

        return redirect()->route('pengguna.index')->with('success', 'Berhasil menghapus pengguna.');
    }
}
