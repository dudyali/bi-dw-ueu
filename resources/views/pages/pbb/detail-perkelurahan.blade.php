@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <h3>Halo, Admin.</h3>
    <span>Anda login sebagai Administrator.</span>

    @include('includes.navtab')

    <div class="row mt-4">
        <div class="col-md-10">
            <h4 class="my-0">Tabel Penerimaan PBB - Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} - Kelurahan {{ ucwords(strtolower($kelurahan->name)) }} - Seluruh Channel Pembayaran</h4>
            <small><i class="text-muted">Seluruh data yang telah di proses ke dalam sistem.</i></small> <br><br>
        </div>
        <div class="col-md-2 text-right">
            <a href="{{ route('pbb.perkelurahan', $kecamatan->id) }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> &nbsp; Kembali</a>
        </div>
    </div>

    <div class="row mt-2" style="margin-bottom: 40px">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>{{ $transaction->count() }}</h3>
                    <small>Total Transaksi Kelurahan {{ ucwords(strtolower($kelurahan->name)) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($transaction->sum('total'), 0) }},-</h3>
                    <small>Total Penerimaan Kelurahan {{ ucwords(strtolower($kelurahan->name)) }}</small>
                </div>
            </div>
        </div>
    </div>

    Tabel Per Kelurahan - Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} - Kelurahan {{ ucwords(strtolower($kelurahan->name)) }} - Seluruh Channel Pembayaran  
    <a href="{{ route('print.kelurahan_all_channel', [$kecamatan->id, $kelurahan->id]) }}" target="_blank" class="btn btn-sm btn-outline-dark float-right"><i class="fa fa-print"></i></a>  
    <hr class="mb-4">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="40">#</th>
                <th class="text-center">Channel</th>
                <th class="text-center">Tanggal Transaksi</th>
                <th class="text-center">NOP</th>
                <th class="text-center">Nama WP</th>
                <th class="text-center">Pokok Pajak</th>
                <th class="text-center">Denda</th>
                <th class="text-center">Potongan</th>
                <th class="text-center">Admin</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction as $key => $item)
                <tr>
                    <td>{{ $key = $key + 1 }}</td>
                    <td>{{ $item->channel->nama }}</td>
                    <td>{{ $item->tg_tx }} {{ $item->jm_tx }}</td>
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
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()
    </script>
@endsection