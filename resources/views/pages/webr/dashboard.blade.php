@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <script src="{{ asset('chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('chartjs/utils.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Realisasi Penerimaan Retribusi Tahun {{ $tahun }}</h3>
            @if (!is_null($ls))
                <span class="text-muted">Data di sinkronisasi per tanggal {{ date('d-m-Y H:i:s', strtotime($ls->sync_at)) }}</span>
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
                            <a class="dropdown-item" href="{{ route('dashboard.retribusi', $item->year) }}">{{ $item->year }}</a>
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
                <a href="{{ route('dashboard.index') }}" class="btn btn-outline-dark mr-2">Non-PBB BPHTB</a>
                <a href="{{ route('dashboard.perbulan') }}" class="btn btn-outline-dark mr-2">PBB</a>
                <a href="{{ route('dashboard.bphtb') }}" class="btn btn-outline-dark mr-2">BPHTB</a>
                <a href="#" class="btn btn-dark mr-2">Retribusi</a>
                <a href="{{ route('dashboard.total') }}" class="btn btn-outline-dark mr-2">Total Penerimaan</a>
                {{-- <a href="{{ route('dashboard.hasil-integrasi') }}" class="btn btn-outline-dark mr-2">Hasil Integrasi</a> --}}
            </div>
        </div>
    </div>

    <div class="row mb-3 mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($bulan_lalu, 0) }}</h3>
                    <small>Total Penerimaan Retribusi Bulan Lalu</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($bulan_ini, 0) }}</h3>
                    <small>Total Penerimaan Retribusi Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($retribusi_bulan->sum('total'), 0) }}</h3>
                    <small>Total Penerimaan Retribusi s.d Bulan Ini</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 40px">
        <div class="col-md-6">
            <canvas id="canvas"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="canvasbar"></canvas>
        </div>
    </div>

    <div class="row" style="margin-top: 40px">
        <div class="col-md-6">
            <h6 class="float-left">Tabel Penerimaan Berdasarkan Bulan</h6>
            <hr style="margin-top: 35px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Bulan</th>
                        <th>Nilai Penerimaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $item }}</td>
                            <td class='text-right'>
                                @php
                                    $flag=0;
                                @endphp
                                @foreach ($retribusi_bulan as $rb)
                                    @if ($rb->bulan==$key)
                                        {{ number_format($rb->total, 0) }}
                                        @php
                                            $flag=1;
                                        @endphp
                                    @endif
                                @endforeach
                                @if ($flag==0)
                                    0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h6 class="float-left">Tabel Penerimaan Berdasarkan OPD</h6>
            <hr style="margin-top: 35px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>OPD</th>
                        <th>Nilai Penerimaan</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($retribusi_opd as $key => $item)
                       <tr>
                           <td>{{ $key = $key + 1 }}</td>
                           <td>{{ $item->nama_opd }}</td>
                           <td class="text-right">{{ number_format($item->total, 0) }}</td>
                       </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row" style="margin-top: 50px">
        <div class="col-md-12">
            <h6 class="float-left">Tabel Penerimaan Berdasarkan Jenis Retribusi</h6>
            <hr style="margin-top: 35px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Jenis Retribusi</th>
                        <th>Nilai Penerimaan</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($retribusi_jenis as $key => $item)
                       <tr>
                           <td>{{ $key = $key + 1 }}</td>
                           <td>{{ $item->jenis_retribusi }}</td>
                           <td class="text-right">{{ number_format($item->total, 0) }}</td>
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

    <script>
		var config = {
			type: 'line',
			data: {
				labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
				datasets: [{
					label: 'Total Penerimaan',
					fill: false,
					backgroundColor: window.chartColors.blue,
					borderColor: window.chartColors.blue,
					data: [
						@foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                            @php
                                $flag=0;
                            @endphp
                            @foreach ($retribusi_bulan as $rb)
                                @if ($rb->bulan==$key)
                                    {{ round($rb->total / 1000000000, 4) }},
                                    @php
                                        $flag=1;
                                    @endphp
                                @endif
                            @endforeach
                            @if ($flag==0)
                                0,
                            @endif
                        @endforeach
					],
				}]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Grafik Total Penerimaan Per Bulan'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Bulan'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Total Penerimaan Dalam Milyar'
						}
					}]
				}
			}
		};
	</script>

    <script>
		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
			datasets: [{
				label: 'Total Penerimaan Kumulatif',
				backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
				borderColor: window.chartColors.red,
				borderWidth: 1,
				data: [
                    @php
                        $total_kumulatif=0;
                    @endphp
                    @foreach (\App\Helpers\BulanHelper::listMonth() as $key => $item)
                        @php
                            $flag=0;
                        @endphp
                        @foreach ($retribusi_bulan as $rb)
                            @if ($rb->bulan==$key)
                                @php
                                    $total_kumulatif += $rb->total;
                                @endphp
                                {{ round($total_kumulatif / 1000000000, 4) }},
                                @php
                                    $flag=1;
                                @endphp
                            @endif
                        @endforeach
                        @if ($flag==0)
                            0,
                        @endif
                    @endforeach
				]
			}]

		};

        window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myLine = new Chart(ctx, config);

            var ctx = document.getElementById('canvasbar').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Grafik Total Penerimaan Per Bulan (Kumulatif)'
					}
				}
			});
		};
    </script>
@endsection