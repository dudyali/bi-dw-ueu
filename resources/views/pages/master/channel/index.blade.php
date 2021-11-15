@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <h3>Halo, Admin.</h3>
    <span>Anda login sebagai Administrator.</span>

    @include('includes.navtab')

    <div class="mt-4 mb-4"><i class="text-muted"><small>Silakan lakukan manajemen Channel dengan menggunakan fitur di bawah ini.</small></i></div>
    
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
                    <h4 class="my-0">Tabel Channel</h4>
                    <small><i class="text-muted">Seluruh data channel di dalam sistem.</i></small>
                </div>
                <div class="col-md-6 text-right">
                    <a href="" class="btn btn-primary btn-sm btn-add" data-toggle="modal" data-target="#modal"><i class="fa fa-plus-circle"></i> &nbsp; Tambah Data</a>
                </div>
            </div>
            <br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>Channel</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>
                                <a><i data-value="{{ $item->id }}" class="fa fa-pencil-alt text-warning btn-edit" data-toggle="modal" data-target="#modal"></i></a>&nbsp;&nbsp;
                                <a><i data-value="{{ $item->id }}" class="fa fa-trash text-danger btn-delete" data-toggle="modal" data-target="#confirm"></i></a>
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
                    <h5 class="modal-title" id="exampleModalLabel">Formulir Channel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('channel.store') }}" method="post" id="form">
                        @csrf
                        <div id="method_put"></div>
                        <div class="form-group">
                            <label>Nama Channel</label>
                            <input type="text" name="nama" class="form-control" id="nama">
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Simpan">
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Aksi ini tidak dapat dikembalikan, apakah anda yakin?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Batal</button>
                    <form action="" method="post" id="form-delete">
                        @csrf
                        @method('DELETE')
                    <input type="submit" class="btn btn-danger" value="Ya, Saya Yakin">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()

        $('.btn-add').click(function() {
            $('#form').attr('action', "{{ url('master/channel') }}")
            $('#method_put').html("")

            $('#nama').val("")
        })

        $('.table').on('click', '.btn-edit', function () {
            let id = $(this).data('value')

            $.ajax({
                url: "{{ url('master/channel') }}/" + id + "/edit",
                success: function(res) {
                    $('#nama').val(res.nama)

                    $('#form').attr('action', "{{ url('master/channel') }}/" + id)
                    $('#method_put').html("<input type='hidden' name='_method' value='PUT'>")
                }
            })
        })

        $('.table').on('click', '.btn-delete', function () {
            let id = $(this).data('value')
            $('#form-delete').attr('action', "{{ url('master/channel') }}/" + id)
        })
    </script>
@endsection