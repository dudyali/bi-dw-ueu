@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #section-to-print, #section-to-print * {
                visibility: visible;
            }
            #section-to-print {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Halo, Admin.</h3>
            <span>Anda login sebagai Administrator.</span>
        </div>
    </div>


    @include('includes.navtab')

    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="my-0">Formulir Laporan</h4>
            <small><i class="text-muted">Silakan masukan parameter laporan di bawah ini.</i></small> <br><br>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 table-responsive">
            <form action="{{ route('laporan.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Jenis Penerimaan</label>
                    <select name="jenis_penerimaan" class="form-control" required>
                        <option value="realisasi_piutang" {{ isset($request) && $request->jenis_penerimaan=="realisasi_piutang" ? 'selected' : '' }}>Realisasi & Piutang</option>
                        <option value="realisasi" {{ isset($request) && $request->jenis_penerimaan=="realisasi" ? 'selected' : '' }}>Realisasi</option>
                        <option value="piutang" {{ isset($request) && $request->jenis_penerimaan=="piutang" ? 'selected' : '' }}>Piutang</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Kecamatan</label>
                    <select name="kecamatan" class="form-control" required id="kecamatan">
                        <option value="all">Seluruh Kecamatan</option>
                        @foreach ($kecamatan as $item)
                            <option value="{{ $item->id }}" {{ isset($request) && $request->kecamatan==$item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih Kelurahan</label>
                    <select name="kelurahan" class="form-control" required id="kelurahan">
                        <option value="all">Seluruh Kelurahan</option>
                        @if (isset($request))
                            @foreach ($kelurahan as $item)
                                <option value="{{ $item->id }}" {{ $request->kelurahan==$item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label>Rentang Tanggal</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="date" name="dari" class="form-control" placeholder="Dari" required value="{{ isset($request) ? $request->dari : '' }}">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="sampai" class="form-control" placeholder="Sampai" required value="{{ isset($request) ? $request->sampai : '' }}">
                        </div>
                    </div>
                </div>
                <div class="form-group mt-5">
                    <input type="submit" value="Terapkan" class="btn btn-primary">
                    @if (isset($request))
                        &nbsp;<a href="#" onclick="window.print()" class="btn btn-outline-dark"><i class="fa fa-print"></i> &nbsp;Cetak PDF</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="col-md-8 table-responsive" id="section-to-print">
            Hasil Laporan
            <hr class="mb-4">
            @if (!isset($trans))
                <small class="text-muted font-small"><i>Hasil laporan akan tampil disini ..</i></small>
            @else
                <div class="alert" style="border: 1px solid #dedede">
                    Parameter Laporan: <br>
                    <table class="table table-striped">
                        <tr>
                            <td width="100">Jenis Penerimaan</td>
                            <td width="4">:</td>
                            <td>{{ ($request->jenis_penerimaan=='realisasi_piutang') ? 'Realisasi & Piutang' : ucwords($request->jenis_penerimaan) }}</td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td>:</td>
                            <td>{{ $kecamatan_dipilih!="all" ? $kecamatan_dipilih : 'Seluruh Kecamatan' }}</td>
                        </tr>
                        <tr>
                            <td>Kelurahan</td>
                            <td>:</td>
                            <td>{{ $kelurahan_dipilih!="all" ? $kelurahan_dipilih : 'Seluruh Kelurahan' }}</td>
                        </tr>
                        <tr>
                            <td>Rentang Tanggal</td>
                            <td>:</td>
                            <td>{{ date('d-m-Y', strtotime($request->dari)) }} s.d {{ date('d-m-Y', strtotime($request->sampai)) }}</td>
                        </tr>
                    </table>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">Jumlah SPPT</th>
                            <th class="text-center">Pokok</th>
                            <th class="text-center">Denda</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $item }}</td>

                                @php $flag = 0; @endphp
                                @foreach ($trans as $tb)
                                    @if ($tb->month==$key)
                                        <td class="text-center">{{ $tb->sppt }}</td>
                                        <td class="text-right">{{ number_format($tb->pokok, 0) }}</td>
                                        <td class="text-right">{{ number_format($tb->denda, 0) }}</td>
                                        <td class="text-right">{{ number_format($tb->total, 0) }}</td>
                                        @php $flag = 1; @endphp
                                        @break
                                    @endif
                                @endforeach
                                @if (!$flag)
                                    <td class="text-center">0</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()

        $('#kecamatan').change(function() {
            let id_kecamatan = $(this).val()
            if (id_kecamatan!="all") {
                $.ajax({
                    url: "{{ url('get-kelurahan-by-kecamatan') }}/" + id_kecamatan,
                    success: function(res) {
                        let html = '<option value="all">Seluruh Kelurahan</option>'

                        res.forEach(item => {
                            html += '<option value="'+item.id+'">'+item.name+'</option>'
                        })

                        $('#kelurahan').html(html)
                    }
                })
            }
        })
    </script>
@endsection