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

        @page { size: landscape; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Realisasi Penerimaan PBB Tahun {{ $tahun }}</h3>
            @if (!is_null($lastSync))
                <span class="text-muted">Data di sinkronisasi per tanggal {{ date('d-m-Y H:i:s', strtotime($lastSync->sync_at)) }}</span>
            @endif
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
                <a href="{{ route('dashboard.perbulan') }}" class="btn btn-dark mr-2">Per Bulan</a>
                <a href="{{ route('dashboard.perkecamatan') }}" class="btn btn-outline-dark mr-2">Per Kecamatan / Kelurahan</a>
                {{-- <a href="{{ route('dashboard.perchannel') }}" class="btn btn-outline-dark mr-2">Per Channel</a> --}}
                <a href="#" onclick="window.print()" class="btn btn-outline-dark float-right"><i class="fa fa-print"></i> &nbsp;Cetak PDF</a>
            </div>
        </div>
    </div>

    <div class="row mb-3 mt-2">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($transaction_tahun_berjalan->sum('total'), 0) }}</h3>
                    <small>Total Realisasi Tahun {{ $tahun }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($transaction_piutang->sum('total'), 0) }}</h3>
                    <small>Total Realisasi Piutang</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3>{{ number_format($sppt_tahun_berjalan, 0) }}</h3>
                    <small>Jumlah SPPT {{ $tahun }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3>{{ number_format($sppt_piutang, 0) }}</h3>
                    <small>Jumlah SPPT Piutang</small>
                </div>
            </div>
        </div>
    </div>

    <div id="section-to-print">
        <div class="row">
            <div class="col-md-4 mt-3 table-responsive">
                Realisasi Tahun {{ $tahun }}
                <hr class="mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">SPPT</th>
                            <th class="text-center">Pokok</th>
                            <th class="text-center">Denda</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sppt = 0;
                            $pokok = 0;
                            $denda = 0;
                            $total = 0;
                        @endphp
                        @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $item }}</td>
    
                                @php $flag = 0; @endphp
                                @foreach ($transaction_tahun_berjalan as $tb)
                                    @if ($tb->month==$key)
                                        <td class="text-center">{{ number_format($tb->sppt, 0) }}</td>
                                        <td class="text-right">{{ number_format($tb->pokok, 0) }}</td>
                                        <td class="text-right">{{ number_format($tb->denda, 0) }}</td>
                                        <td class="text-right">{{ number_format($tb->total, 0) }}</td>
                                        @php 
                                            $flag = 1; 
                                            $sppt += $tb->sppt;
                                            $pokok += $tb->pokok;
                                            $denda += $tb->denda;
                                            $total += $tb->total;
                                        @endphp
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
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td class="text-center">{{ number_format($sppt, 0) }}</td>
                            <td class="text-right">{{ number_format($pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($denda, 0) }}</td>
                            <td class="text-right">{{ number_format($total, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 mt-3 table-responsive">
                Piutang Tahun {{ $tahun }}
                <hr class="mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">SPPT</th>
                            <th class="text-center">Pokok</th>
                            <th class="text-center">Denda</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sppt = 0;
                            $pokok = 0;
                            $denda = 0;
                            $total = 0;
                        @endphp
                        @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $item }}</td>
    
                                @php $flag = 0; @endphp
                                @foreach ($transaction_piutang as $piu)
                                    @if ($piu->month==$key)
                                        <td class="text-center">{{ number_format($piu->sppt) }}</td>
                                        <td class="text-right">{{ number_format($piu->pokok, 0) }}</td>
                                        <td class="text-right">{{ number_format($piu->denda, 0) }}</td>
                                        <td class="text-right">{{ number_format($piu->total, 0) }}</td>
                                        @php 
                                            $flag = 1; 
                                            $sppt += $piu->sppt;
                                            $pokok += $piu->pokok;
                                            $denda += $piu->denda;
                                            $total += $piu->total;
                                        @endphp
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
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td class="text-center">{{ number_format($sppt, 0) }}</td>
                            <td class="text-right">{{ number_format($pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($denda, 0) }}</td>
                            <td class="text-right">{{ number_format($total, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 mt-3 table-responsive">
                Total Realisasi & Piutang Tahun {{ $tahun }}
                <hr class="mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">SPPT</th>
                            <th class="text-center">Pokok</th>
                            <th class="text-center">Denda</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sppt = 0;
                            $pokok = 0;
                            $denda = 0;
                            $total = 0;
                        @endphp
                        @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $item }}</td>
    
                                @php $flag = 0; @endphp
                                @foreach ($transaction_tahun_berjalan as $tb)
                                    @foreach ($transaction_piutang as $piu)
                                        @if ($tb->month==$piu->month)
                                            @if ($tb->month==$key)
                                                <td class="text-center">{{ number_format(($piu->sppt + $tb->sppt), 0) }}</td>
                                                <td class="text-right">{{ number_format(($piu->pokok + $tb->pokok), 0) }}</td>
                                                <td class="text-right">{{ number_format(($piu->denda + $tb->denda), 0) }}</td>
                                                <td class="text-right">{{ number_format(($piu->total + $tb->total), 0) }}</td>
                                                @php 
                                                    $flag = 1; 
                                                    $sppt += ($piu->sppt + $tb->sppt);
                                                    $pokok += ($piu->pokok + $tb->pokok);
                                                    $denda += ($piu->denda + $tb->denda);
                                                    $total += ($piu->total + $tb->total);
                                                @endphp
                                                @break
                                            @endif
                                        @endif
                                    @endforeach
                                @endforeach

                                @if (!$flag)
                                    <td class="text-center">0</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                    <td class="text-right">0</td>
                                @endif
                                
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                            <td class="text-center">{{ number_format($sppt) }}</td>
                            <td class="text-right">{{ number_format($pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($denda, 0) }}</td>
                            <td class="text-right">{{ number_format($total, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // $('.table').DataTable()
    </script>
@endsection