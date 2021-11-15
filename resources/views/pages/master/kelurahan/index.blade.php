@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <h3>Halo, Admin.</h3>
    <span>Anda login sebagai Administrator.</span>

    @include('includes.navtab')

    <div class="mt-4 mb-4"><i class="text-muted"><small>Silakan lakukan manajemen Kelurahan dengan menggunakan fitur di bawah ini.</small></i></div>
    
    <div class="row">
        <div class="col-md-2 pr-4">
            @include('includes.navpill')
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

            <div class="row">
                <div class="col-md-6">
                    <h4 class="my-0">Tabel Kelurahan</h4>
                    <small><i class="text-muted">Seluruh data kelurahan yang ada pada kecamatan {{ $kecamatan->name }}.</i></small>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('kecamatan.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> &nbsp; Kembali</a>
                </div>
            </div>
            <br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>Kode</th>
                        <th>Kelurahan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ !is_null($item->village_code) ? $item->village_code : '-' }}</td>
                            <td>{{ $item->name }}</td>
                            <td>
                                <a><i data-value="{{ $item->id }}" class="fa fa-pencil-alt text-warning btn-edit" data-toggle="modal" data-target="#modal"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulir Kelurahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="form">
                        @csrf
                        <div id="method_put"></div>
                        <div class="form-group">
                            <label>Kode Kecamatan</label>
                            <input type="text" name="village_code" class="form-control" id="village_code">
                        </div>
                        <div class="form-group">
                            <label>Nama Kecamatan</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Simpan">
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()

        $('.table').on('click', '.btn-edit', function () {
            let id = $(this).data('value')

            $.ajax({
                url: "{{ url('master/kelurahan') }}/" + id + "/edit",
                success: function(res) {
                    $('#village_code').val(res.village_code)
                    $('#name').val(res.name)

                    $('#form').attr('action', "{{ url('master/kelurahan') }}/" + id)
                    $('#method_put').html("<input type='hidden' name='_method' value='PUT'>")
                }
            })
        })
    </script>
@endsection