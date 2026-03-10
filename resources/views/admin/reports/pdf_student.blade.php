<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f0f0f0; }
        .text-left { text-align: left; padding-left: 8px;}
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0 0 0; }
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
                <th width="25%">Nama Siswa / NIS</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Total Sesi</th>
                <th>% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">
                    <strong>{{ $row['name'] }}</strong><br>
                    <small style="color: #555;">{{ $row['nis'] ?? '-' }}</small>
                </td>
                
                <!-- PERBAIKANNYA ADA DI SINI (Kata ['stats'] dihapus) -->
                <td>{{ $row['present'] }}</td>
                <td>{{ $row['sick'] }}</td>
                <td>{{ $row['perm'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td><strong>{{ $row['percent'] }}</strong></td>
                
            </tr>
            @empty
            <tr>
                <td colspan="7">Tidak ada data kehadiran untuk rentang waktu ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <br><br>
    <div style="float: right; text-align: center; width: 250px;">
        <p>Mengetahui,</p>
        <p>Wali Kelas</p>
        <br><br><br>
        <p><strong>{{ $classroom->homeroomTeacher->name ?? '...........................' }}</strong></p>
        <p>NIP. {{ $classroom->homeroomTeacher->nip_nis ?? '-' }}</p>
    </div>
</body>
</html>