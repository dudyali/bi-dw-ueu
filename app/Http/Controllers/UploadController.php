<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Transaction;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function index()
    {
        $data = UploadedFile::with('channel')->orderby('id', 'desc')->limit(365)->get();

        return view('pages.upload.index')->with('data', $data);
    }

    public function create()
    {
        $channel = Channel::get();
        
        return view('pages.upload.create')->with('channel', $channel);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'id_channel' => 'required',
            'path' => 'required|mimes:xlsx,xls|max:1100',
        ]);

        $file = $request->file('path');
        $filename = time()."_".strtolower(str_replace(" ", "_", $file->getClientOriginalName()));
        $file->storeAs('pbb', $filename);
        
        $data = new UploadedFile;
        $data->id_channel = $request->id_channel;
        $data->path = $filename;
        $data->save();

        return redirect()->route('upload.index')->with('success', 'Berhasil mengupload file.');
    }

    public function destroy($id)
    {
        UploadedFile::destroy($id);

        return redirect()->route('upload.index')->with('success', 'Berhasil menghapus file.');
    }

    public function detail($id)
    {
        $file = UploadedFile::findOrFail($id);
        $data = Transaction::where('id_uploaded_file', $id)->get();

        return view('pages.upload.detail')
            ->with('file', $file)
            ->with('id_uploaded_file', $id)
            ->with('data', $data);
    }

    public function delete_upload($id)
    {
        UploadedFile::find($id)->delete();

        return redirect()->route('upload.index')->with('success', 'Berhasil menghapus file.');
    }

    public function delete(Request $request)
    {
        $validation = $request->validate([
            'id_uploaded_file' => 'required',
            'id.*' => 'required',
        ]);

        // return $request;

        foreach ($request->id as $key => $value) {
            // return $value;
            Transaction::destroy($value);
        }

        $check = Transaction::where('id_uploaded_file', $request->id_uploaded_file)->get();
        if ($check->count() == 0) {
            $update = UploadedFile::findOrFail($request->id_uploaded_file);
            $update->is_processed = 0;
            $update->save();
        }

        return redirect()->route('upload.detail', $request->id_uploaded_file)->with('success', 'Berhasil menghapus detail data.');
    }
}
