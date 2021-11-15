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
                            <a class="dropdown-item" href="{{ route('dashboard.perchannel', $item->year) }}">{{ $item->year }}</a>
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
                <a href="{{ route('dashboard.perkecamatan') }}" class="btn btn-outline-dark mr-2">Per Kecamatan / Kelurahan</a>
                {{-- <a href="{{ route('dashboard.perchannel') }}" class="btn btn-dark mr-2">Per Channel</a> --}}
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
                    <h3>{{ $sppt_tahun_berjalan }}</h3>
                    <small>Jumlah SPPT {{ $tahun }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3>{{ $sppt_piutang }}</h3>
                    <small>Jumlah SPPT Piutang</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="section-to-print">
        <div class="col-md-12 mt-3 table-responsive">
            <strong>
                Realisasi Per Channel Tahun {{ $tahun }}
            </strong>
            <hr class="mb-4 mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10" rowspan="2">#</th>
                        <th class="text-center" rowspan="2">Bulan</th>
                        @foreach ($channel as $item)
                            <th class="text-center" colspan="2"
                                @if ($item->id % 2 == 0)
                                    style="background: #e7f6ff"
                                @endif
                            >{{ $item->nama }}</th>
                        @endforeach
                        <th class="text-center" colspan="2" style="background: #e7ffeb">Total</th>
                    </tr>
                    <tr>
                        @foreach ($channel as $item)
                            <th
                                @if ($item->id % 2 == 0)
                                    style="background: #e7f6ff"
                                @endif
                            >SPPT</th>
                            <th
                                @if ($item->id % 2 == 0)
                                    style="background: #e7f6ff"
                                @endif
                            >Total</th>
                        @endforeach
                        
                        <th style="background: #e7ffeb">SPPT</th>
                        <th style="background: #e7ffeb">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total_arr = [] @endphp
                    @foreach ($channel as $c)
                        @php
                            $total_arr[$c->id]['sppt'] = 0;
                            $total_arr[$c->id]['total'] = 0;
                        @endphp
                    @endforeach

                    @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $item }}</td>

                            @php $total = 0; @endphp
                            @php $sppt = 0; @endphp
                            @foreach ($channel as $ch)
                                @php $flag = 0; @endphp
                                @foreach ($realisasi_channel as $rc)
                                    @if ($rc->month==$key && $ch->id==$rc->id_channel)
                                        <td class="text-right"
                                            @if ($ch->id % 2 == 0)
                                                    style="background: #e7f6ff"
                                            @endif
                                        >{{ $rc->sppt }}</td>
                                        <td class="text-right"
                                            @if ($ch->id % 2 == 0)
                                                    style="background: #e7f6ff"
                                            @endif
                                        >{{ number_format($rc->total, 0) }}</td>
                                        @php 
                                            $flag = 1; 
                                            $total += $rc->total; 
                                            $sppt += $rc->sppt; 

                                            $total_arr[$rc->id_channel]['sppt'] += $rc->sppt;
                                            $total_arr[$rc->id_channel]['total'] += $rc->total;
                                        @endphp
                                    @endif
                                @endforeach
                                @if ($flag!=1)
                                    <td class="text-right"
                                        @if ($ch->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >0</td>
                                    <td class="text-right"
                                        @if ($ch->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >0</td>
                                @endif
                            @endforeach
                            <td class="text-right" style="background: #e7ffeb">{{ $sppt }}</td>
                            <td class="text-right" style="background: #e7ffeb">{{ number_format($total) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-right"><strong>Total</strong></td>
                        @php
                            $total = 0;
                            $sppt = 0;
                        @endphp
                        @foreach ($channel as $chn)
                            @foreach ($total_arr as $key => $tot)
                                @if ($key==$chn->id)
                                    <td class="text-right"
                                        @if ($chn->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >{{ $tot['sppt'] }}</td>
                                    <td class="text-right"
                                        @if ($chn->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >{{ number_format($tot['total'], 0) }}</td>
                                    @php
                                        $total += $tot['total'];
                                        $sppt += $tot['sppt'];
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                        <td class="text-right" style="background: #e7ffeb">{{ $sppt }}</td>
                        <td class="text-right" style="background: #e7ffeb">{{ number_format($total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-12 mt-5 table-responsive">
            <strong>
                Piutang Per Channel Tahun {{ $tahun }}
            </strong>
            <hr class="mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10" rowspan="2">#</th>
                        <th class="text-center" rowspan="2">Bulan</th>
                        @foreach ($channel as $item)
                            <th class="text-center" colspan="2"
                                @if ($item->id % 2 == 0)
                                    style="background: #e7f6ff"
                                @endif
                            >{{ $item->nama }}</th>
                        @endforeach
                        <th class="text-center" colspan="2" style="background: #e7ffeb">Total</th>
                    </tr>
                    <tr>
                        @foreach ($channel as $item)
                            <th
                                @if ($item->id % 2 == 0)
                                    style="background: #e7f6ff"
                                @endif
                            >SPPT</th>
                            <th
                                @if ($item->id % 2 == 0)
                                    style="background: #e7f6ff"
                                @endif
                            >Total</th>
                        @endforeach
                        
                        <th style="background: #e7ffeb">SPPT</th>
                        <th style="background: #e7ffeb">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total_arr = [] @endphp
                    @foreach ($channel as $c)
                        @php
                            $total_arr[$c->id]['sppt'] = 0;
                            $total_arr[$c->id]['total'] = 0;
                        @endphp
                    @endforeach

                    @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $item }}</td>

                            @php $total = 0; @endphp
                            @php $sppt = 0; @endphp
                            @foreach ($channel as $ch)
                                @php $flag = 0; @endphp
                                @foreach ($piutang_channel as $rc)
                                    @if ($rc->month==$key && $ch->id==$rc->id_channel)
                                        <td class="text-right"
                                            @if ($ch->id % 2 == 0)
                                                    style="background: #e7f6ff"
                                            @endif
                                        >{{ $rc->sppt }}</td>
                                        <td class="text-right"
                                            @if ($ch->id % 2 == 0)
                                                    style="background: #e7f6ff"
                                            @endif
                                        >{{ number_format($rc->total, 0) }}</td>
                                        @php 
                                            $flag = 1; 
                                            $total += $rc->total; 
                                            $sppt += $rc->sppt; 

                                            $total_arr[$rc->id_channel]['sppt'] += $rc->sppt;
                                            $total_arr[$rc->id_channel]['total'] += $rc->total;
                                        @endphp
                                    @endif
                                @endforeach
                                @if ($flag!=1)
                                    <td class="text-right"
                                        @if ($ch->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >0</td>
                                    <td class="text-right"
                                        @if ($ch->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >0</td>
                                @endif
                            @endforeach
                            <td class="text-right" style="background: #e7ffeb">{{ $sppt }}</td>
                            <td class="text-right" style="background: #e7ffeb">{{ number_format($total) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-right"><strong>Total</strong></td>
                        @php
                            $total = 0;
                            $sppt = 0;
                        @endphp
                        @foreach ($channel as $chn)
                            @foreach ($total_arr as $key => $tot)
                                @if ($key==$chn->id)
                                    <td class="text-right"
                                        @if ($chn->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >{{ $tot['sppt'] }}</td>
                                    <td class="text-right"
                                        @if ($chn->id % 2 == 0)
                                            style="background: #e7f6ff"
                                        @endif
                                    >{{ number_format($tot['total'], 0) }}</td>
                                    @php
                                        $total += $tot['total'];
                                        $sppt += $tot['sppt'];
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                        <td class="text-right" style="background: #e7ffeb">{{ $sppt }}</td>
                        <td class="text-right" style="background: #e7ffeb">{{ number_format($total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // $('.table').DataTable()
    </script>
@endsection