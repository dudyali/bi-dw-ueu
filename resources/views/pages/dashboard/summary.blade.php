@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <script src="{{ asset('chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('chartjs/utils.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Realisasi Penerimaan Non PBB BPHTB Tahun {{ $tahun }}</h3>
            <span class="text-muted">Data di sinkronisasi per tanggal {{ date('d-m-Y H:i:s', strtotime($ls->sync_at)) }}</span>
        </div>
        <div class="col-md-6">
            <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tahun {{ $tahun }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        @foreach ($all_year as $item)
                            <a class="dropdown-item" href="{{ route('dashboard.index', $item->year) }}">{{ $item->year }}</a>
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
                {{-- <a href="{{ route('dashboard.hasil-integrasi') }}" class="btn btn-outline-dark mr-2">Hasil Integrasi</a> --}}
            </div>
        </div>
    </div>

    <div class="row mb-3 mt-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('target'), 0) }}</h5>
                    <small>Total Target Penerimaan Non PBB BPHTB</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('pokok'), 0) }}</h5>
                    <small>Total Pokok Pajak</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('denda'), 0) }}</h5>
                    <small>Total Denda Pajak</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('total'), 0) }}</h5>
                    <small>Jumlah Penerimaan Pajak Non PBB BPHTB</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h5>{{ round($jp->sum('total') * 100 / $jp->sum('target'), 2) }} %</h5> 
                    <small>Persentase Penerimaan</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3 mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('piutang_jumlah')) }}</h5>
                    <small>Total Realisasi Piutang Non-PBB BPHTB</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('piutang_pokok')) }}</h5>
                    <small>Total Pokok Piutang Non-PBB BPHTB</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5><sup>Rp</sup> {{ number_format($jp->sum('piutang_denda')) }}</h5>
                    <small>Total Denda Piutang Non-PBB BPHTB</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 40px">
        <div class="col-md-7">
            <h6 class="float-left">Tabel Penerimaan Berdasarkan Jenis Pajak (Termasuk Piutang)</h6>
            <small>
                <a href="{{ route('view-bulanan.jenis', $tahun) }}" class="float-right">Tampilkan Bulanan</a>
            </small>
            <hr style="margin-top: 35px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" width="10">#</th>
                        <th rowspan="2">Nama Pajak</th>
                        <th rowspan="2">Target Penerimaan</th>
                        <th colspan="3" class="text-center">Realisasi Penerimaan</th>
                        <th rowspan="2">Persentase</th>
                    </tr>
                    <tr>
                        <th>Pokok</th>
                        <th>Denda</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jp as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td nowrap><a href="{{ route('ret-pajak.kategori', $item->id) }}">{{ $item->nama }}</a></td>
                            <td class="text-right">{{ number_format($item->target, 0) }}</td>
                            <td class="text-right">{{ number_format($item->pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($item->denda, 0) }}</td>
                            <td class="text-right">{{ number_format($item->total, 0) }}</td>
                            <td class="text-right">
                                @if ($item->target!=0)
                                    {{ round($item->total * 100 / $item->target, 2) }} %
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-5">
            <h6 class="float-left">Tabel Realisasi Piutang Berdasarkan Jenis Pajak</h6>
            <small>
                <a href="{{ route('view-piutang-bulanan.jenis', $tahun) }}" class="float-right">Tampilkan Bulanan</a>
            </small>
            <hr style="margin-top: 35px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" width="10">#</th>
                        <th rowspan="2">Nama Pajak</th>
                        <th colspan="3" class="text-center">Realisasi Piutang</th>
                    </tr>
                    <tr>
                        <th>Pokok</th>
                        <th>Denda</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jp as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td nowrap><a href="{{ route('piutang-pajak.kategori', $item->id) }}">{{ $item->nama }}</a></td>
                            <td class="text-right">{{ number_format($item->piutang_pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($item->piutang_denda, 0) }}</td>
                            <td class="text-right">{{ number_format($item->piutang_jumlah, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- <div class="col-md-5">
            <h6>Grafik Jumlah Penerimaan Berdasarkan Jenis Pajak</h6> <hr>
            <div id="canvas-holder" style="margin-top: 60px">
                <canvas id="chart-area"></canvas>
            </div>
        </div> --}}
    </div>
@endsection

@section('page-script')
    <script>
        $('.table').DataTable()
    </script>
    <script>
		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};

		var config = {
			type: 'doughnut',
			data: {
				datasets: [{
					data: [
						@foreach($jp as $item)
                            {{ $item->total }},
                        @endforeach
					],
					backgroundColor: [
						window.chartColors.grey,
						window.chartColors.orange,
						window.chartColors.yellow,
						window.chartColors.green,
						window.chartColors.purple,
						window.chartColors.blue,
						window.chartColors.red,
					],
					label: 'Dataset 1'
				}],
				labels: [
                    @foreach($jp as $item)
                        '{{ ucwords(strtolower($item->nama)) }}',
                    @endforeach
				]
			},
			options: {
				responsive: true,
				legend: {
					position: 'left',
				},
				title: {
					display: false,
					text: 'Realisasi Penerimaan PAD {{ $tahun }} Non-PBB BPHTB'
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		};

		// window.onload = function() {
		// 	var ctx = document.getElementById('chart-area').getContext('2d');
		// 	window.myDoughnut = new Chart(ctx, config);
		// };
	</script>
@endsection