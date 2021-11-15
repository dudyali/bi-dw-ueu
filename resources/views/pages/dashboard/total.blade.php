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
            <h3>Realisasi Penerimaan Tahun Berjalan ({{ $tahun }})</h3>
        </div>
        {{-- <div class="col-md-6">
            <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tahun {{ $tahun }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach ($all_year as $item)
                            <a class="dropdown-item" href="{{ route('dashboard.total', $item->year) }}">{{ $item->year }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <small class="text-muted">Silakan pilih mode data:</small>
            <div class="alert" style="border: 1px solid #dedede">
                <a href="{{ route('dashboard.index') }}" class="btn btn-outline-dark mr-2">Non-PBB BPHTB</a>
                <a href="{{ route('dashboard.perbulan') }}" class="btn btn-outline-dark mr-2">PBB</a>
                <a href="{{ route('dashboard.bphtb') }}" class="btn btn-outline-dark mr-2">BPHTB</a>
                <a href="{{ route('dashboard.retribusi') }}" class="btn btn-outline-dark mr-2">Retribusi</a>
                <a href="#" class="btn btn-dark mr-2">Total Penerimaan</a>
                {{-- <a href="{{ route('dashboard.hasil-integrasi') }}" class="btn btn-outline-dark mr-2">Hasil Integrasi</a> --}}
                <a href="#" onclick="window.print()" class="btn btn-outline-dark float-right"><i class="fa fa-print"></i> &nbsp;Cetak PDF</a>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 20px">
        <div class="col-md-12" id="section-to-print">
            <h6 class="float-left">Total Realisasi Penerimaan Per Bulan {{ date('F') }} {{ $tahun }}</h6>
            <hr style="margin-top: 40px; margin-bottom: 25px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center">Uraian</th>
                        <th rowspan="2" class="text-center">Target</th>
                        <th colspan="3" class="text-center">Realisasi Penerimaan Tahun {{ $tahun }}</th>
                        <th rowspan="2" class="text-center">Lebih/Kurang</th>
                        <th rowspan="2" class="text-center">Persentase</th>
                    </tr>
                    <tr>
                        <th class="text-center">Bulan Lalu</th>
                        <th class="text-center">Bulan Ini ({{ date('F') }})</th>
                        <th class="text-center">s.d Bulan Ini</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>PENDAPATAN PAJAK NON PBB & BPHTB</strong></td>
                        <td class="text-right">{{ number_format($total_non_pbb['total_target'], 0) }}</td>
                        <td class="text-right">{{ number_format($total_non_pbb['bulan_lalu'], 0) }}</td>
                        <td class="text-right">{{ number_format($total_non_pbb['bulan_ini'], 0) }}</td>
                        <td class="text-right">{{ number_format($total_non_pbb['bulan_ini'] + $total_non_pbb['bulan_lalu'], 0) }}</td>
                        <td class="text-right">{{ $total_non_pbb['lebih_kurang'] < 0 ? '('.number_format(abs($total_non_pbb['lebih_kurang']), 0).')' : number_format($total_non_pbb['lebih_kurang'], 0) }}</td>
                        <td class="text-right">{{ round($total_non_pbb['persentase'], 2) }} %</td>
                    </tr>
                    @foreach ($non_pbb as $item)
                        <tr>
                            <td> &nbsp; &nbsp; &nbsp; Pajak {{ ucwords(strtolower($item['jenis_pajak'])) }}</td>
                            <td class="text-right">{{ number_format($item['target']) }}</td>
                            <td class="text-right">{{ number_format($item['realisasi']['bulan_lalu'], 0) }}</td>
                            <td class="text-right">{{ number_format($item['realisasi']['bulan_ini'], 0) }}</td>
                            <td class="text-right">{{ number_format($item['realisasi']['bulan_lalu'] + $item['realisasi']['bulan_ini'], 0) }}</td>
                            <td class="text-right">{{ $item['lebih_kurang'] < 0 ? '('.number_format(abs($item['lebih_kurang']), 0).')' : number_format($item['lebih_kurang'], 0) }}</td>
                            <td class="text-right">{{ round($item['persentase'], 2) }} %</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="7"><strong>PENDAPATAN PAJAK PBB</strong></td>
                    </tr>
                    <tr>
                        <td>  &nbsp; &nbsp; &nbsp; Pajak Bumi dan Bangunan (PBB)</td>
                        <td class="text-right">{{ number_format($targetPBB->target, 0) }}</td>
                        <td class="text-right">{{ number_format($pbb['bulan_lalu'], 0) }}</td>
                        <td class="text-right">{{ number_format($pbb['bulan_ini'], 0) }}</td>
                        <td class="text-right">{{ number_format($pbb['bulan_ini'] + $pbb['bulan_lalu'], 0) }}</td>
                        <td class="text-right">{{ $pbb_lebih_kurang < 0 ? '('.number_format(abs($pbb_lebih_kurang), 0).')' : number_format($pbb_lebih_kurang, 0) }}</td>
                        <td class="text-right">{{ round($pbb_persentase, 2) }} %</td>
                    </tr>
                    <tr>
                        <td colspan="7"><strong>PENDAPATAN PAJAK BPHTB</strong></td>
                    </tr>
                    <tr>
                        <td>  &nbsp; &nbsp; &nbsp; Pajak Bea Perolehan Hak atas Tanah dan Bangunan (BPHTB)</td>
                        <td class="text-right">{{ number_format($targetBPHTB->target, 0) }}</td>
                        <td class="text-right">{{ number_format($bphtb['bulan_lalu'], 0) }}</td>
                        <td class="text-right">{{ number_format($bphtb['bulan_ini'], 0) }}</td>
                        <td class="text-right">{{ number_format($bphtb['bulan_ini'] + $bphtb['bulan_lalu'], 0) }}</td>
                        <td class="text-right">{{ $bphtb_lebih_kurang < 0 ? '('.number_format(abs($bphtb_lebih_kurang), 0).')' : number_format($bphtb_lebih_kurang, 0) }}</td>
                        <td class="text-right">{{ round($bphtb_persentase, 2) }} %</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('page-script')

@endsection