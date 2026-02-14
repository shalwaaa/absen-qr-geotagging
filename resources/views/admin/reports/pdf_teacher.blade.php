<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aktivitas Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; }
        .text-left { text-align: left !important; }
        .footer { margin-top: 40px; float: right; width: 200px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI KEHADIRAN GURU</h2>
        <p>Periode: {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">NIP</th>
                <th class="text-left">Nama Guru</th>
                <th width="20%">Total Sesi Mengajar<br>(Kali Hadir)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['nip'] ?? '-' }}</td>
                <td class="text-left">{{ $row['name'] }}</td>
                <td><strong>{{ $row['teaching_hours'] }}</strong> Sesi</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak Oleh,</p>
        <br><br><br>
        <p><strong>Administrator</strong></p>
    </div>

</body>
</html>