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
            <h4 class="my-0">Tabel Upload File</h4>
            <small><i class="text-muted">Seluruh file yang telah di upload ke dalam sistem.</i></small> <br><br>
            <div class="table-responsive">
                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Channel</th>                
                            <th>File</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total Transaksi</th>
                            <th>Tanggal Upload</th>
                            <th>Status Proses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $item)
                            <tr>
                                <td>{{ $key = $key + 1 }}</td>
                                <td>{{ $item->channel->nama }}</td>
                                <td>{{ $item->path }}</td>
                                <td>
                                    @if (!is_null($item->tanggal_transaksi))
                                        @php
                                            $tgl = explode(',', $item->tanggal_transaksi);
                                            if (count($tgl)==0) {
                                                echo $tgl;
                                            } else {
                                                foreach ($tgl as $key => $t) {
                                                    echo "- ".$t."<br>";
                                                }
                                            }
                                        @endphp
                                    @else
                                        <span class="text-muted">Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($item->total_transaksi, 0) }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->is_processed ? 'success' : 'danger' }}" style="font-size: 12px">{{ $item->is_processed ? 'Sudah' : 'Belum' }}</span>
                                </td>
                                <td>
                                    @if (!$item->is_processed)
                                        <a style="cursor: pointer"><i data-value="{{ $item->id }}" class="fa fa-sync text-success btn-process" data-toggle="modal" data-target="#confirm-process"></i></a>&nbsp;&nbsp;
                                        <a style="cursor: pointer" class="text-danger btn-delete" data-value="{{ $item->id}}"><i class="fa fa-trash" data-toggle="modal" data-target="#confirm-delete"></i></a>&nbsp;&nbsp;
                                    @else
                                        <a href="{{ route('upload.detail', $item->id) }}"><i class="fa fa-list text-primary btn-process"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-process" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Pemrosesan Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn-x">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirm-process-body">
                    Aksi ini akan memakan waktu beberapa menit, apakah anda yakin akan melanjutkan?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal" id="btn-batal">Batal</button>
                    <a style="cursor: pointer" class="btn btn-primary btn-lanjut text-white">Ya, Lanjutkan</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog">
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

        $('.btn-terapkan-pencarian').click(function() {
            let parameter = $('#parameter').val()
            let katakunci = $('#katakunci').val()
            let url = "{{ url('upload/filter-pencarian') }}/" + parameter + "/" + katakunci;
            window.location.href = url;
        })

        function stateAwal() {
            $('#confirm-process-body').html('Aksi ini akan memakan waktu beberapa menit, apakah anda yakin akan melanjutkan?')
            $('.btn-lanjut').show()
            $('.btn-lanjut').removeClass('disabled')
            $('.btn-lanjut').html('Ya, Lanjutkan')
            $('#btn-batal').html('Batal')
        }

        $('.btn-lanjut').click(function() {
            $('#confirm-process-body').html('Mohon menunggu, jangan menutup tab sampai proses selesai ...')
            $(this).addClass('disabled')
            $(this).html('Mohon menunggu ...')
            $('#btn-batal').hide()
            $('#btn-x').hide()

            let id = $(this).data('value');

            $.ajax({
                url: "{{ url('proces-upload') }}/" + id,
                success: function(res) {
                    $(location).attr('href', "{{ url('proses/success') }}")
                },
                error: function() { 
                    $('#confirm-process-body').html('<span class="text-danger">Mohon maaf, sistem mendeteksi adanya kesalahan pada file excel anda. Harap perbaiki file excel anda dan upload kembali ...<span>')
                    $('.btn-lanjut').hide()
                    $('#btn-batal').show()
                    $('#btn-batal').html('Oke')
                    $('#btn-x').show()
                }  
            })
        })

        $('.table').on('click', '.btn-delete', function () {
            let id = $(this).data('value')
            $('#form-delete').attr('action', "{{ url('delete_upload') }}/" + id)
        })

        $('.table').on('click', '.btn-process', function () {
            let id = $(this).data('value')
            $('.btn-lanjut').data('value', id)

            stateAwal()
        })
    </script>
@endsection