<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Card Styling */
        .qr-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: fit-content;
        }

        .list-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* QR Placeholder */
        .qr-container {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        /* Badge Status */
        .badge-present {
            background: #f0f7ed;
            color: #4a6741;
            padding: 4px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
        }

        /* Buttons Custom */
/* Custom Button Styles (Aura Bootstrap Premium) */
.btn-toggle-active { 
    background: linear-gradient(135deg, #4a6741 0%, #5d8252 100%); 
    color: white; 
    border: none;
    height: 50px;
    width: 100%;
    transition: all 0.3s ease;
}
.btn-toggle-active:hover { 
    background: linear-gradient(135deg, #3d5535 0%, #4a6741 100%); 
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 103, 65, 0.3);
    color: white;
}

.btn-toggle-inactive { 
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); 
    color: white;
    height: 50px;
    width: 100%; 
    border: none;
    transition: all 0.3s ease;
}
.btn-toggle-inactive:hover { 
    background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); 
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
    color: white;
}

.btn.btn-dashboard {
    border: 2px solid #f1f5f9 !important; /* Paksa border agar tidak ditimpa btn-lg */
    background-color: #ffffff !important;
    color: #64748b !important;
    height: 50px !important; 
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none; /* Biar garis bawah ilang */
}

.btn.btn-dashboard:hover {
    background-color: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #1e293b !important;
    transform: scale(0.98);
}

        /* Table Styling */
        .attendance-table th {
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
        }
        .attendance-table td { padding: 16px; color: #1e293b; }
        .attendance-table tr:hover { background: #f8fafc; }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center animate-fade-in">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C;">Monitoring Absensi:</span> <span style="color: #E4EB9C">{{ $meeting->schedule->subject->name }} </span>
                </h2>
                <p class="text-sm text-gray-500 mt-1" style="color: #E4EB9C;">Kelas: {{ $meeting->schedule->classroom->name }}</p>
            </div>
            <div class="text-right">
                <span class="block font-bold text-gray-800"style="color: #E4EB9C;">{{ \Carbon\Carbon::parse($meeting->date)->format('d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-4 animate-fade-in">
                    <div class="qr-card">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-lg bg-green-50">
                                <i class="fa-solid fa-qrcode text-green-700"></i>
                            </div>
                            <h3 class="font-bold text-gray-800">QR Code Presensi</h3>
                        </div>
                        
                        @if($meeting->is_active)
                            <div class="p-4 bg-white border-2 border-green-100 rounded-2xl flex justify-center shadow-inner mb-6">
                                {!! QrCode::size(280)->margin(1)->generate($meeting->qr_token) !!}
                            </div>
                            <div class="text-center mb-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 animate-pulse">
                                    <span class="w-2 h-2 mr-2 rounded-full bg-green-500"></span> Sesi Aktif
                                </span>
                                <p class="text-xs text-gray-500 mt-3 italic">Minta siswa mendekat ke layar untuk melakukan pemindaian.</p>
                            </div>
                        @else
                            <div class="qr-container h-72 mb-6">
                                <div class="text-center p-6">
                                    <i class="fa-solid fa-lock text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">Sesi Terkunci</p>
                                </div>
                            </div>
                            <div class="text-center mb-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <span class="w-2 h-2 mr-2 rounded-full bg-red-500"></span> Sesi Ditutup
                                </span>
                            </div>
                        @endif

<div class="d-grid gap-3">
    <form action="{{ route('meetings.toggle', $meeting->id) }}" method="POST" class="w-full">
        @csrf
        <button type="submit" 
            class="btn btn-lg w-100 py-3 rounded-xl fw-bold d-flex align-items-center justify-content-center gap-2 {{ $meeting->is_active ? 'btn-toggle-inactive' : 'btn-toggle-active' }}">
            
            @if($meeting->is_active)
                <i class="fa-solid fa-circle-stop fs-5"></i>
                <span>Tutup & Kunci Absensi</span>
            @else
                <i class="fa-solid fa-circle-play fs-5"></i>
                <span>Aktifkan Sesi Sekarang</span>
            @endif
        </button>
    </form>

    <br>
    
    <a href="{{ route('teacher.dashboard') }}" 
        class="btn btn-dashboard btn-lg py-3 rounded-xl fw-semibold d-flex align-items-center justify-content-center gap-2 text-decoration-none">
        <i class="fa-solid fa-house-chimney fs-6 opacity-75"></i> 
        <span style="font-size: 14px;">Kembali ke Dashboard</span>
    </a>
</div>

<br>

                <div class="lg:col-span-8 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="list-card h-full">
                        <div class="flex justify-between items-center mb-8">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Kehadiran Siswa</h3>
                                <p class="text-sm text-gray-500">Total: {{ $meeting->attendances->count() }} Siswa Hadir</p>
                            </div>
                            <button onclick="location.reload()" class="flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 rounded-lg font-medium text-sm transition-all shadow-sm">
                                <i class="fa-solid fa-arrows-rotate"></i> Perbarui Data
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left attendance-table">
                                <thead class="border-b border-gray-100">
                                    <tr>
                                        <th>Waktu Scan</th>
                                        <th>Informasi Siswa</th>
                                        <th>Status</th>
                                        <th>Akurasi Jarak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($meeting->attendances->sortByDesc('created_at') as $attendance)
                                        <tr class="border-b border-gray-50 transition-colors">
                                            <td class="font-mono text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($attendance->scan_time)->format('H:i:s') }}
                                            </td>
                                            <td>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-800">{{ $attendance->student->name }}</span>
                                                    <span class="text-xs text-gray-400">NIS: {{ $attendance->student->nis }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-present">
                                                    <i class="fa-solid fa-check-double mr-1"></i> {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full max-w-[60px] overflow-hidden">
                                                        <div class="h-full bg-green-500" style="width: {{ max(100 - ($attendance->distance_meters / 2), 10) }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-gray-600">
                                                        {{ number_format($attendance->distance_meters, 1) }}m
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-20 text-center">
                                                <div class="flex flex-col items-center opacity-30">
                                                    <i class="fa-solid fa-users-slash text-5xl mb-4"></i>
                                                    <p class="font-medium text-lg">Belum ada siswa yang melakukan presensi.</p>
                                                    <p class="text-sm">Silakan tampilkan QR Code untuk memulai.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>