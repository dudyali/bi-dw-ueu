@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Halo, Admin.</h3>
            <span>Anda login sebagai Administrator.</span>
        </div>
        <div class="col-md-6">
            <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tahun {{ $tahun }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach ($all_year as $item)
                            <a class="dropdown-item" href="{{ route('dashboard.perbulan', $item->year) }}">{{ $item->year }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('includes.navtab')

    <div class="row mt-4">
        <div class="col-md-6">
            <h4 class="my-0">Tabel Ringkasan Realisasi</h4>
            <small><i class="text-muted">Seluruh ringkasan data yang telah di proses ke dalam sistem.</i></small> <br><br>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <small class="text-muted">Silakan pilih mode ringkasan berikut ini:</small>
            <div class="alert" style="border: 1px solid #dedede">
                <a href="{{ route('dashboard.perbulan') }}" class="btn btn-outline-dark mr-2">Per Bulan</a>
                <a href="{{ route('dashboard.perkecamatan') }}" class="btn btn-dark mr-2">Per Kecamatan / Kelurahan</a>
                {{-- <a href="{{ route('dashboard.perchannel') }}" class="btn btn-outline-dark mr-2">Per Channel</a> --}}
            </div>
        </div>
    </div>

    <div class="row mb-3 mt-2">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($realisasi_tahun_berjalan->sum('total'), 0) }}</h3>
                    <small>Total Realisasi Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} Tahun {{ $tahun }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($realisasi_piutang->sum('total'), 0) }}</h3>
                    <small>Total Realisasi Piutang Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} Tahun {{ $tahun }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($realisasi_tahun_berjalan->sum('total') + $realisasi_piutang->sum('total'), 0) }}</h3>
                    <small>Jumlah Realisasi Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} Tahun {{ $tahun }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mt-3 table-responsive">
            Realisasi Per Kelurahan Pada Kecamatan {{ ucwords(strtolower($kecamatan->name)) }} Tahun {{ $tahun }}
            <hr class="mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th class="text-center">Kelurahan</th>
                        <th class="text-center">Realisasi Tahun {{ $tahun }}</th>
                        <th class="text-center">Realisasi Piutang</th>
                        <th class="text-center">Total Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_per_kelurahan as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ $item['kelurahan'] }}</td>
                            <td class="text-right">{{ number_format($item['realisasi_tahun_berjalan']) }}</td>
                            <td class="text-right">{{ number_format($item['realisasi_piutang']) }}</td>
                            <td class="text-right">{{ number_format($item['total_realisasi']) }}</td>
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