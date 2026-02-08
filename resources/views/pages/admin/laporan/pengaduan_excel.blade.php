<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengaduan</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="6" style="font-weight: bold; font-size: 14pt; text-align: center;">LAPORAN PENGADUAN SARPRAS</th>
            </tr>
            <tr>
                <th colspan="6" style="text-align: center;">Periode: {{ $startDate }} s/d {{ $endDate }}</th>
            </tr>
            <tr></tr>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>No</th>
                <th>Pelapor</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Tgl Lapor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->user?->name }}</td>
                    <td>{{ $row->judul }}</td>
                    <td>{{ $row->deskripsi }}</td>
                    <td>{{ $row->lokasi?->nama }}</td>
                    <td>{{ strtoupper($row->status) }}</td>
                    <td>{{ $row->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
