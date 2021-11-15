@extends('layouts.master')

@section('title')
    <title>DW UTS DUDY ALI - Bapenda</title>

    <script src="{{ asset('chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('chartjs/utils.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h3>Realisasi Penerimaan BPHTB Tahun {{ $tahun }}</h3>
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
                            <a class="dropdown-item" href="{{ route('dashboard.bphtb', $item->year) }}">{{ $item->year }}</a>
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
                <a href="#" class="btn btn-dark mr-2">BPHTB</a>
                <a href="{{ route('dashboard.retribusi') }}" class="btn btn-outline-dark mr-2">Retribusi</a>
                <a href="{{ route('dashboard.total') }}" class="btn btn-outline-dark mr-2">Total Penerimaan</a>
                {{-- {{-- <a href="{{ route('dashboard.hasil-integrasi') }}" class="btn btn-outline-dark mr-2">Hasil Integrasi</a> --}} --}}
            </div>
        </div>
    </div>

    <div class="row mb-3 mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($bulan_lalu, 0) }}</h3>
                    <small>Total Penerimaan Bulan Lalu</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($bulan_ini, 0) }}</h3>
                    <small>Total Penerimaan Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3><sup>Rp</sup> {{ number_format($total, 0) }}</h3>
                    <small>Total Penerimaan s.d Bulan Ini</small>
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
                    @foreach ($trx_bulanan as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ $item['bulan'] }}</td>
                            <td class="text-right">{{ number_format($item['nilai'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h6 class="float-left">Tabel Penerimaan Berdasarkan Jenis Perolehan</h6>
            <hr style="margin-top: 35px">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th>Jenis Perolehan</th>
                        <th>Nilai Penerimaan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trx_jenis_perolehan as $key => $item)
                        <tr>
                            <td>{{ $key = $key + 1 }}</td>
                            <td>{{ $item->jenis_perolehan }}</td>
                            <td class="text-right">{{ number_format($item->bphtb_yang_dibayar, 0) }}</td>
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
                            @foreach ($trx_bulanan as $rb)
                                @if ($rb['bulan_digit']==$key)
                                    {{ round($rb['nilai'] / 1000000000, 4) }},
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
                        @foreach ($trx_bulanan as $rb)
                            @if ($rb['bulan_digit']==$key && $rb['nilai']!=0)
                                @php
                                    $total_kumulatif += $rb['nilai'];
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