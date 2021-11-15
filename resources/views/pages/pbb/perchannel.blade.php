@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <h3>Halo, Admin.</h3>
    <span>Anda login sebagai Administrator.</span>

    @include('includes.navtab')

    <div class="row mt-4">
        <div class="col-md-11">
            <h4 class="my-0">Tabel Penerimaan PBB - {{ isset($kecamatan) ? 'Kecamatan '.$kecamatan->name.' - ' : '' }} Channel {{ $channel->nama }}</h4>
            <small><i class="text-muted">Seluruh data yang telah di proses ke dalam sistem.</i></small> <br><br>
        </div>
        <div class="col-md-1 text-right">
            @if (isset($kecamatan))
                <a href="{{ route('pbb.perkelurahan', $kecamatan->id) }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> &nbsp; Kembali</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> &nbsp; Kembali</a>
            @endif
        </div>
    </div>

    <div class="row mt-2" style="margin-bottom: 40px">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>{{ $transaction->count() }}</h3>
                    <small>Total Transaksi Channel {{ $channel->nama }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($transaction->sum('total'), 0) }},-</h3>
                    <small>Total Penerimaan Channel {{ $channel->nama }}</small>
                </div>
            </div>
        </div>
    </div>

    Tabel Transaksi - Channel {{ $channel->nama }} 
    @if (isset($kecamatan))
        <a href="{{ route('print.transaction-by-channel', [$channel->id, $kecamatan->id]) }}" target="_blank" class="btn btn-sm btn-outline-dark float-right"><i class="fa fa-print"></i></a>  
    @endif
    <hr class="mb-4">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="40">#</th>
                <th class="text-center">Kecamatan</th>
                <th class="text-center">Kelurahan</th>
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
                    <td>{{ $item->kecamatan->name }}</td>
                    <td>{{ $item->kelurahan->name }}</td>
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