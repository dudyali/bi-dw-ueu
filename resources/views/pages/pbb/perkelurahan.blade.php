@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <h3>Halo, Admin.</h3>
    <span>Anda login sebagai Administrator.</span>

    @include('includes.navtab')

    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="my-0">Tabel Penerimaan PBB - Kecamatan {{ ucwords(strtolower($kecamatan->name)) }}</h4>
            <small><i class="text-muted">Seluruh data yang telah di proses ke dalam sistem.</i></small> <br><br>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> &nbsp; Kembali</a>
        </div>
    </div>

    <div class="row mb-4 mt-2">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>{{ $transaction->count() }}</h3>
                    <small>Total Transaksi Kecamatan {{ ucwords(strtolower($kecamatan->name)) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($transaction->sum('total'), 0) }},-</h3>
                    <small>Total Penerimaan Kecamatan {{ ucwords(strtolower($kecamatan->name)) }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mt-3">
            Tabel Per Kelurahan - Kecamatan {{ ucwords(strtolower($kecamatan->name)) }}  
            <a href="{{ route('print.kecamatan-by-kelurahan', $kecamatan->id) }}" target="_blank" class="btn btn-sm btn-outline-dark float-right"><i class="fa fa-print"></i></a>  
            <hr class="mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th class="text-center">Kelurahan</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-center">Total Transaksi (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelurahan as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td><a href="{{ route('pbb.detail_perkelurahan', [$kecamatan->id, $item->id]) }}" data-toggle="tooltip" title="Detail Transaksi" data-placement="right">{{ $item->name }}</a></td>
                            <td class="text-center">{{ $item->transaction->count() }}</td>
                            <td class="text-right">{{ number_format($item->transaction->sum('total'), 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-6 mt-3">
            Tabel Per Channel Pembayaran - Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} 
            <a href="{{ route('print.kecamatan-by-channel', $kecamatan->id) }}" target="_blank" class="btn btn-sm btn-outline-dark float-right"><i class="fa fa-print"></i></a>  
            <hr class="mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th class="text-center">Channel</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-center">Total Transaksi (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($channel as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td><a href="{{ route('pbb.perchannel', [$item->id, $kecamatan->id]) }}" data-toggle="tooltip" title="Detail Transaksi" data-placement="right">{{ $item->nama }}</a></td>
                            <td class="text-center">{{ $item->transaction->count() }}</td>
                            <td class="text-right">{{ number_format($item->transaction->sum('total'), 0) }}</td>
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