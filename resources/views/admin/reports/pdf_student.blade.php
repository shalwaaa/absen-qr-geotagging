<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aktivitas Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-left { text-align: left !important; padding-left: 10px; }
        
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-line { margin-top: 60px; border-bottom: 1px solid #000; }
        
        .badge-success { color: green; font-weight: bold; }
        .badge-danger { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI KEHADIRAN GURU</h2>
        <p>Periode: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2" width="25%">Nama Guru / NIP</th>
                <th colspan="4">Detail Kehadiran (Sesi/Jam Pelajaran)</th>
                <th rowspan="2" width="10%">Total<br>Wajib</th>
                <th rowspan="2" width="10%">Persentase<br>Kehadiran</th>
            </tr>
            <tr>
                <th width="8%">Hadir</th>
                <th width="8%">Sakit</th>
                <th width="8%">Izin</th>
                <th width="8%">Alpha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">
                    <strong>{{ $row['name'] }}</strong><br>
                    <span style="color: #666;">{{ $row['nip'] ?? '-' }}</span>
                </td>
                <td>{{ $row['stats']['present'] }}</td>
                <td>{{ $row['stats']['sick'] }}</td>
                <td>{{ $row['stats']['permission'] }}</td>
                <td style="{{ $row['stats']['alpha'] > 0 ? 'color:red; font-weight:bold;' : '' }}">
                    {{ $row['stats']['alpha'] }}
                </td>
                <td>{{ $row['stats']['total_scheduled'] }}</td>
                <td>
                    <span class="{{ $row['percent'] >= 90 ? 'badge-success' : ($row['percent'] < 70 ? 'badge-danger' : '') }}">
                        {{ $row['percent'] }}%
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">Tidak ada data guru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="signature-line"></div>
            <p>( .................................................... )</p>
            <p>NIP.</p>
        </div>
    </div>

</body>
</html>