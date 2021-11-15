@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <script src="{{ asset('chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('chartjs/utils.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Pajak {{ ucwords(strtolower($kp->jenis->nama)) }} {{ $kp->tahun }} - Kategori {{ ucwords(strtolower($kp->nama)) }}</h3>
            <span class="text-muted">Data di sinkronisasi per tanggal {{ date('d-m-Y H:i:s', strtotime($ls->sync_at)) }}</span>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ url()->previous() }}" class="btn btn-dark pull-right"><i class="fa fa-arrow-left"></i> &nbsp;Kembali</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <small class="text-muted">Silakan pilih mode data:</small>
            <div class="alert" style="border: 1px solid #dedede">
                <a href="#" class="btn btn-dark mr-2">Non-PBB BPHTB</a>
                <a href="{{ route('dashboard.perbulan') }}" class="btn btn-outline-dark mr-2">PBB</a>
                <a href="#" class="btn btn-outline-dark mr-2">BPHTB</a>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 30px">
        <div class="col-md-12">
            <h6>Tabel Penerimaan Berdasarkan Kategori Pajak {{ ucwords(strtolower($kp->nama)) }} (Termasuk Piutang)</h6> <hr>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th>NPWPD</th>
                        <th>NOPD</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Pokok</th>
                        <th>Denda</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trx as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ $item->npwpd->npwpd }} <br> {{ $item->npwpd->nama_wp }}</td>
                            <td>{{ $item->nopd->nopd }} <br> {{ $item->nopd->nama_op }}</td>
                            <td>{{ date('F', mktime(0, 0, 0, $item->bulan, 10)) }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td class="text-right">{{ number_format($item->pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($item->denda, 0) }}</td>
                            <td class="text-right">{{ number_format($item->jumlah, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()
    </script>
@endsection