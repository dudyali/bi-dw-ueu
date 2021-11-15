@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <script src="{{ asset('chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('chartjs/utils.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Realisasi Penerimaan PAD Tahun {{ $tahun }}</h3>
            <span class="text-muted">Data di sinkronisasi per tanggal {{ date('d-m-Y H:i:s', strtotime($ls->sync_at)) }}</span>
        </div>
        <div class="col-md-6">
            <a href="{{ route('dashboard.index') }}" class="btn btn-dark float-right"><i class="fa fa-arrow-left"></i> &nbsp;Kembali</a>
            <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown" style="margin-right: 20px">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tahun {{ $tahun }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach ($all_year as $item)
                            <a class="dropdown-item" href="{{ route('view-bulanan.jenis', $item->year) }}">{{ $item->year }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <small class="text-muted">Silakan pilih mode data:</small>
            <div class="alert" style="border: 1px solid #dedede">
                <a href="{{ route('dashboard.index') }}" class="btn btn-dark mr-2">Non-PBB BPHTB</a>
                <a href="{{ route('dashboard.perbulan') }}" class="btn btn-outline-dark mr-2">PBB</a>
                <a href="{{ route('dashboard.bphtb') }}" class="btn btn-outline-dark mr-2">BPHTB</a>
                <a href="{{ route('dashboard.retribusi') }}" class="btn btn-outline-dark mr-2">Retribusi</a>
                <a href="{{ route('dashboard.total') }}" class="btn btn-outline-dark mr-2">Total Penerimaan</a>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px">
        <div class="col-md-12">
            <h6 class="float-left">Tabel Penerimaan Berdasarkan Jenis Pajak Dikelompokan Per Bulan (Termasuk Piutang)</h6>
            <hr style="margin-top: 35px">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th>Nama Pajak</th>
                            <th>Januari</th>
                            <th>Februari</th>
                            <th>Maret</th>
                            <th>April</th>
                            <th>Mei</th>
                            <th>Juni</th>
                            <th>Juli</th>
                            <th>Agustus</th>
                            <th>September</th>
                            <th>Oktober</th>
                            <th>November</th>
                            <th>Desember</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $item)
                            <tr>
                                <td>{{ $key = $key + 1 }}</td>
                                <td nowrap><a href="{{ route('view-bulanan.kategori', $item['id']) }}">{{ $item['jenis_pajak'] }}</a></td>
                                <td class="text-right">{{ number_format($item['jan'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['feb'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['mar'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['apr'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['mei'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['jun'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['jul'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['agu'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['sep'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['okt'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['nov'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['des'], 0) }}</td>
                                <td class="text-right">{{ number_format($item['total'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()
    </script>
@endsection