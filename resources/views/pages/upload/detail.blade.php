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
                <a class="nav-link {{ (request()->is('upload') || request()->is('upload-detail*')) ? 'active' : '' }}" href="{{ route('upload.index') }}">Data Upload</a>
                <a class="nav-link {{ (request()->is('upload/create')) ? 'active' : '' }}" href="{{ route('upload.create') }}">Formulir Upload</a>
            </div>
        </div>
        <div class="col-md-10 pl-4" style="border-left: 1px solid #dedede">

            <div class="row">
                <div class="col-md-6">
                    <h4 class="my-0">Tabel Detail Upload File - Channel {{ $file->channel->nama }}</h4>
                    <small><i class="text-muted">Seluruh data yang telah di proses ke dalam sistem.</i></small> <br><br>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('upload.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> &nbsp; Kembali</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h4>Oops, terjadi kesalahan!</h4>
                    @foreach ($errors->all() as $error)
                        -- {{ $error }} <br>
                    @endforeach
                </div>
            @endif

            @if (Session::has('success'))
                <div class="alert alert-success" id="alert-msg">
                    <h4>Ouyeah!</h4>
                    {{ Session::get('success') }}
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ $data->count() }}</h3>
                            <small>Total Transaksi</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3><sup>Rp</sup> {{ number_format($data->sum('total')) }},-</h3>
                            <small>Total Penerimaan</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <span class="text-muted mt-3" style="font-size: 12px">
                    Silakan gunakan menu di bawah ini untuk mengelola data:
                </span>
                <div class="alert mb-4" style="border: 1px solid #dedede">
                    <a class="btn btn-sm btn-outline-info text-info mr-2 ps">Pilih Semua</a>
                    <a href="" class="btn btn-sm btn-outline-danger mr-2" data-toggle="modal" data-target="#modalhapus">Hapus Data</a>
                </div>

                <form action="{{ route('upload.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_uploaded_file" value="{{ $id_uploaded_file }}">

                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th></th>
                                <th width="20">#</th>
                                <th>Channel</th>
                                <th>Nomor</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>NOP</th>
                                <th>Nama WP</th>
                                <th>Pokok Pajak</th>
                                <th>Denda</th>
                                <th>Potongan</th>
                                <th>Admin</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td><input type="checkbox" class="check" name="id[]" value="{{ $item->id }}"></td>
                                    <td>{{ $key = $key + 1 }}</td>
                                    <td>{{ $item->channel->nama }}</td>
                                    <td>{{ $item->nomor }}</td>
                                    <td>{{ $item->tg_tx }}</td>
                                    <td>{{ $item->jm_tx }}</td>
                                    <td>{{ $item->nop }}</td>
                                    <td>{{ $item->nama_wp }}</td>
                                    <td class="text-right">{{ number_format($item->pokok_pajak, 0) }}</td>
                                    <td class="text-right">{{ number_format($item->denda, 0) }}</td>
                                    <td class="text-right">{{ number_format($item->potongan, 0) }}</td>
                                    <td class="text-right">{{ number_format($item->admin, 0) }}</td>
                                    <td class="text-right">{{ number_format($item->total, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="modal fade" id="modalhapus" tabindex="-1" role="dialog">
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
                                    <input type="submit" class="btn btn-danger" value="Ya, Saya Yakin">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        var table = $('.table').DataTable()

        let flag_ps = 0
        $('.ps').click(function() {
            if (flag_ps == 0) {
                table.$('input[type="checkbox"]').prop("checked", true)
                $(this).html('Hapus Centang')
                flag_ps = 1
            } else {
                table.$('input[type="checkbox"]').prop("checked", false)
                $(this).html('Pilih Semua')
                flag_ps = 0
            }
        })

        // BEGIN: To detect all checkboxes in datatable across the pages
        $('form').on('submit', function(e) {
            var $form = $(this);
            table.$('input[type="checkbox"]').each(function(){
                if(!$.contains(document, this)){
                    if(this.checked){ 
                        $form.append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', this.name)
                                .val(this.value)
                        );
                    }
                } 
            });          
        });
        // END
    </script>
@endsection