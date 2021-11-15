@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <script src="{{ asset('chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('chartjs/utils.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Piutang Pajak {{ ucwords(strtolower($jp->nama)) }} {{ $tahun }}</h3>
            <span class="text-muted">Data di sinkronisasi per tanggal {{ date('d-m-Y H:i:s', strtotime($ls->sync_at)) }}</span>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('dashboard.index') }}" class="btn btn-dark pull-right"><i class="fa fa-arrow-left"></i> &nbsp;Kembali</a>
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
        <div class="col-md-{{ $jp->id != 6 ? '6' : '12' }}">
            <h6>Tabel Penerimaan Berdasarkan Kategori Pajak {{ ucwords(strtolower($jp->nama)) }}</h6> <hr>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" width="10">#</th>
                        <th rowspan="2">Nama Pajak</th>
                        <th colspan="3" class="text-center">Realisasi Penerimaan</th>
                    </tr>
                    <tr>
                        <th>Pokok</th>
                        <th>Denda</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kp as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td><a href="{{ route('piutang-pajak.detail', $item->id) }}">{{ $item->nama }}</a></td>
                            <td class="text-right">{{ number_format($item->piutang_pokok, 0) }}</td>
                            <td class="text-right">{{ number_format($item->piutang_denda, 0) }}</td>
                            <td class="text-right">{{ number_format($item->piutang_jumlah, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($jp->id != 6)
            <div class="col-md-6">
                <h6>Grafik Jumlah Penerimaan Berdasarkan Kategori Pajak {{ ucwords(strtolower($jp->nama)) }}</h6> <hr>
                <div id="canvas-holder" style="margin-top: 60px">
                    <canvas id="chart-area"></canvas>
                </div>
            </div>
        @endif
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
						@foreach($kp as $item)
                            @if($item->piutang_jumlah!=0)
                                {{ $item->piutang_jumlah }},
                            @endif
                        @endforeach
					],
					backgroundColor: [
						window.chartColors.green,
						window.chartColors.orange,
						window.chartColors.yellow,
						window.chartColors.purple,
						window.chartColors.grey,
						window.chartColors.blue,
                        window.chartColors.red,
						window.chartColors.orange,
						window.chartColors.yellow,
						window.chartColors.purple,
						window.chartColors.grey,
						window.chartColors.blue,
						window.chartColors.red,
                        window.chartColors.green,
					],
					label: 'Dataset 1'
				}],
				labels: [
                    @foreach($kp as $item)
                        @if($item->total!=0)
                            '{{ ucwords(strtolower($item->nama)) }}',
                        @endif
                    @endforeach
				]
			},
			options: {
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: false,
					text: 'Realisasi Penerimaan PAD 2020 Non-PBB BPHTB'
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('chart-area').getContext('2d');
			window.myDoughnut = new Chart(ctx, config);
		};
	</script>
@endsection