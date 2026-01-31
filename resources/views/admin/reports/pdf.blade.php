<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #ddd; }
        .text-left { text-align: left; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEHADIRAN SISWA</h2>
        <h3>Kelas: {{ $classroom->name }}</h3>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Siswa</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Total</th>
                <th>% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $row['name'] }} <br><small>{{ $row['nis'] }}</small></td>
                <td>{{ $row['present'] }}</td>
                <td>{{ $row['sick'] }}</td>
                <td>{{ $row['perm'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td><strong>{{ $row['percent'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <br><br>
    <div style="float: right; text-align: center; width: 200px;">
        <p>Mengetahui,</p>
        <p>Wali Kelas</p>
        <br><br><br>
        <p><strong>{{ $classroom->homeroomTeacher->name ?? '..................' }}</strong></p>
    </div>
</body>
</html>