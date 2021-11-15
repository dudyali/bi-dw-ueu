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
    Seluruh detail penerimaan PBB via channel {{ $channel->nama }} pada kecamatan {{ ucwords(strtolower($kecamatan->name)) }}.
    <br><br>
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
            @php
                $pokok_pajak = 0;
                $denda = 0;
                $potongan = 0;
                $admin = 0;
                $total = 0;
            @endphp
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
                @php
                    $pokok_pajak += $item->pokok_pajak;
                    $denda += $item->denda;
                    $potongan += $item->potongan;
                    $admin += $item->admin;
                    $total += $item->total;
                @endphp
            @endforeach
            <tr>
                <td colspan="6" class="text-center">Total</td>
                <td class="text-right">{{ number_format($pokok_pajak, 0) }}</td>
                <td class="text-right">{{ number_format($denda, 0) }}</td>
                <td class="text-right">{{ number_format($potongan, 0) }}</td>
                <td class="text-right">{{ number_format($admin, 0) }}</td>
                <td class="text-right">{{ number_format($total, 0) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>