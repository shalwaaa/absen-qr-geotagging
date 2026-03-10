<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aktivitas Guru</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2D5128; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; color: #142C14; }
        .header p { margin: 5px 0 0 0; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f0fdf4; font-weight: bold; color: #142C14; }
        
        .text-left { text-align: left !important; padding-left: 10px; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .text-success { color: #166534; font-weight: bold; }
        
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-line { margin-top: 70px; border-bottom: 1px solid #000; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI KEHADIRAN MENGAJAR GURU</h2>
        <p>Periode: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2" width="25%">Nama Guru / NIP</th>
                <th colspan="4">Detail Kehadiran (Sesi/Jam Pelajaran)</th>
                <th rowspan="2" width="10%">Total<br>Wajib Mengajar</th>
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
                    <span style="color: #666; font-size: 10px;">NIP: {{ $row['nip'] ?? '-' }}</span>
                </td>
                
                <!-- Pemanggilan Data Flat -->
                <td>{{ $row['present'] }}</td>
                <td>{{ $row['sick'] }}</td>
                <td>{{ $row['permission'] }}</td>
                
                <!-- Jika Alpha > 0 kasih warna merah -->
                <td class="{{ $row['alpha'] > 0 ? 'text-danger' : '' }}">
                    {{ $row['alpha'] }}
                </td>
                
                <td>{{ $row['total_scheduled'] }}</td>
                
                <!-- Pewarnaan Persentase -->
                <td>
                    <span class="{{ $row['percent'] >= 90 ? 'text-success' : ($row['percent'] < 70 ? 'text-danger' : '') }}">
                        {{ $row['percent'] }}%
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">Tidak ada aktivitas guru pada rentang waktu ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="signature-line"></div>
            <!-- Mengambil nama user yang sedang login, asumsikan yang ngeprint adalah admin/kepsek -->
            <p><strong>{{ Auth::user()->name ?? 'Administrator' }}</strong></p>
        </div>
    </div>

</body>
</html>