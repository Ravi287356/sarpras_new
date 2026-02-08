<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="7" style="font-weight: bold; font-size: 14pt; text-align: center;">LAPORAN PEMINJAMAN SARPRAS</th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center;">Periode: {{ $startDate }} s/d {{ $endDate }}</th>
            </tr>
            <tr></tr>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>No</th>
                <th>Kode</th>
                <th>Peminjam</th>
                <th>Barang</th>
                <th>Tujuan</th>
                <th>Tgl Pinjam</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->kode_peminjaman }}</td>
                    <td>{{ $row->user?->name }}</td>
                    <td>
                        @foreach($row->items as $item)
                            - {{ $item->sarprasItem?->sarpras?->nama }} ({{ $item->sarprasItem?->kode }})<br>
                        @endforeach
                    </td>
                    <td>{{ $row->tujuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td>{{ strtoupper($row->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
