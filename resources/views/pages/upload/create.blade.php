@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <h3>Halo, Admin.</h3>
    <span>Anda login sebagai Administrator.</span>

    @include('includes.navtab')

    <div class="mt-4 mb-4"><i class="text-muted"><small>Silakan lakukan manajemen Upload Data dengan menggunakan fitur di bawah ini.</small></i></div>
    
    <div class="row">
        <div class="col-md-2 pr-4">
            <div class="nav flex-column nav-pills">
                <a class="nav-link {{ (request()->is('upload')) ? 'active' : '' }}" href="{{ route('upload.index') }}">Data Upload</a>
                <a class="nav-link {{ (request()->is('upload/create')) ? 'active' : '' }}" href="{{ route('upload.create') }}">Formulir Upload</a>
            </div>
        </div>
        <div class="col-md-10 pl-4" style="border-left: 1px solid #dedede">
            @if (Session::has('success'))
                <div class="alert alert-success" id="alert-msg">
                    <h4>Ouyeah!</h4>
                    {{ Session::get('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h4>Oops, terjadi kesalahan!</h4>
                    @foreach ($errors->all() as $error)
                        -- {{ $error }} <br>
                    @endforeach
                </div>
            @endif
            <h4 class="my-0">Formulir Upload File</h4>
            <small><i class="text-muted">Silakan isi formulir di bawah ini</i></small>
            <form action="{{ route('upload.store') }}" method="post" class="my-4" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>File Upload</label>
                    <input type="file" class="form-control" required name="path">
                </div>
                <div class="form-group">
                    <label>Channel</label>
                    <select name="id_channel" class="form-control" required>
                        <option value="">-- Pilih Channel --</option>
                        @foreach ($channel as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Simpan">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
  
@endsection