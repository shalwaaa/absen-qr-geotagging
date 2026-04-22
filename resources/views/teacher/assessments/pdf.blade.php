<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Penilaian Karakter</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2D5128; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; color: #142C14; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: center; }
        th { background-color: #f0fdf4; font-weight: bold; color: #142C14; }
        .text-left { text-align: left !important; padding-left: 10px; }
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-line { margin-top: 70px; border-bottom: 1px solid #000; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI PENILAIAN SIKAP & KARAKTER</h2>
        <p>Kelas: <strong>{{ $classroom->name }}</strong> | Periode: <strong>{{ \Carbon\Carbon::parse('01-'.$periodMonth)->translatedFormat('F Y') }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIS</th>
                <th class="text-left" width="25%">Nama Siswa</th>
                <!-- Dinamis membuat header kolom sesuai kategori -->
                @foreach($categories as $cat)
                    <th>{{ $cat->name }}</th>
                @endforeach
                <th width="10%">Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $index => $ast)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $ast->evaluatee->nip_nis }}</td>
                <td class="text-left">{{ $ast->evaluatee->name }}</td>
                
                <!-- Loop nilai tiap kategori -->
                @foreach($categories as $cat)
                    @php
                        // Cari nilai untuk kategori ini
                        // Karena detail terhubung ke question, kita hitung rata-rata dari pertanyaan di kategori ini
                        $catDetails = $ast->details->filter(function($q) use ($cat) {
                            return $q->question && $q->question->category_id == $cat->id; // Pastikan relasi question di model ada
                        });
                        $score = $catDetails->count() > 0 ? round($catDetails->avg('score'), 1) : '-';
                    @endphp
                    <td>{{ $score }}</td>
                @endforeach
                
                <!-- Rata-rata keseluruhan anak ini -->
                <td><strong>{{ number_format($ast->details->avg('score'), 1) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,<br>Wali Kelas</p>
            <div class="signature-line"></div>
            <p><strong>{{ $teacher->name }}</strong></p>
            <p>NIP. {{ $teacher->nip_nis ?? '-' }}</p>
        </div>
    </div>

</body>
</html>