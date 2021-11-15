<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DW UTS DUDY ALI - Bapenda</title>
    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap.min.css') }}">
    <style>
        td, th {
            font-size: 12px;
        }
    </style>
</head>
<body onload="window.print()" onfocus="window.close()">
    <h4 style="display: inline-block;">Tabel Penerimaan PBB</h4> 
    <span class="float-right"><small>{{ date('d-m-Y') }}</small></span> <br>
    Seluruh agregat penerimaan PBB dikelompokkan berdasarkan Kecamatan.
    <br><br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="40">#</th>
                <th class="text-center">Kecamatan</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-center">Total Transaksi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $jumlah_transaksi = 0;
                $total_transaksi = 0;
            @endphp
            @foreach ($kecamatan as $key => $item)
                <tr>
                    <td>{{ $key = $key + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->transaction->count() }}</td>
                    <td class="text-right">{{ number_format($item->transaction->sum('total'), 0) }}</td>
                </tr>
                @php
                    $jumlah_transaksi += $item->transaction->count();
                    $total_transaksi += $item->transaction->sum('total');
                @endphp
            @endforeach
            <tr>
                <td colspan="2" class="text-center">Total</td>
                <td class="text-center">{{ $jumlah_transaksi }}</td>
                <td class="text-right">{{ number_format($total_transaksi, 0) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>