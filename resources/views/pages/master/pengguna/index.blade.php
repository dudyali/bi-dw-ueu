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
            <div class="nav flex-column nav-pills">
                @include('includes.navpill')
            </div>
        </div>
        <div class="col-md-10 pl-4" style="border-left: 1px solid #dedede">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="my-0">Tabel Pengguna</h4>
                    <small><i class="text-muted">Seluruh data pengguna di dalam sistem.</i></small>
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                                <a><i data-value="{{ $item->id }}" class="fa fa-pencil-alt text-warning btn-edit" data-toggle="modal" data-target="#modal"></i></a>&nbsp;&nbsp;
                                <a><i data-value="{{ $item->id}}" class="fa fa-trash text-danger btn-delete" data-toggle="modal" data-target="#confirm"></i></a>
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
                    <h5 class="modal-title" id="exampleModalLabel">Formulir Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pengguna.store') }}" method="post" id="form">
                        @csrf
                        <div id="method_put"></div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                            <span class="help-block hb-password text-muted"><small><i>* Kosongkan jika tidak ingin diganti</i></small></span>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                            <span class="help-block hb-password text-muted"><small><i>* Kosongkan jika tidak ingin diganti</i></small></span>
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
        $('.hb-password').hide()

        $('.btn-add').click(function() {
            $('#form').attr('action', "{{ url('master/pengguna') }}")
            $('#method_put').html("")

            $('.hb-password').hide()

            $('#password').attr('required')
            $('#password_confirmation').attr('required')

            $('#name').val("")
            $('#email').val("")
            $('#password').val("")
            $('#password_confirmation').val("")
        })

        $('.table').on('click', '.btn-edit', function () {
            let id = $(this).data('value')

            $('#password').removeAttr('required')
            $('#password_confirmation').removeAttr('required')
            $('.hb-password').show()

            $.ajax({
                url: "{{ url('master/pengguna') }}/" + id + "/edit",
                success: function(res) {
                    $('#name').val(res.name)
                    $('#email').val(res.email)

                    $('#form').attr('action', "{{ url('master/pengguna') }}/" + id)
                    $('#method_put').html("<input type='hidden' name='_method' value='PUT'>")
                }
            })
        })

        $('.table').on('click', '.btn-delete', function () {
            let id = $(this).data('value')
            $('#form-delete').attr('action', "{{ url('master/pengguna') }}/" + id)
        })
    </script>
@endsection