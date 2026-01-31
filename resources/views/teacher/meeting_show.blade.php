<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Card Styling */
        .qr-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .list-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: 100%; }

        /* Badges */
        .badge-present { background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px; }
        .badge-sick { background: #ffedd5; color: #9a3412; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid #fed7aa; display: inline-flex; align-items: center; gap: 4px; }
        .badge-perm { background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 4px; }
        
        /* Badge Guru */
        .badge-teacher { background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 11px; text-transform: uppercase; border: 1px solid #fcd34d; }

        /* Buttons */
        .btn-toggle-active { background: linear-gradient(135deg, #4a6741 0%, #5d8252 100%); color: white; border: none; height: 50px; width: 100%; transition: 0.3s; border-radius: 12px; font-weight: bold; }
        .btn-toggle-active:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 103, 65, 0.3); }
        
        .btn-toggle-inactive { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; height: 50px; width: 100%; border: none; transition: 0.3s; border-radius: 12px; font-weight: bold; }
        .btn-toggle-inactive:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3); }

        .btn-dashboard { background: white; border: 2px solid #e2e8f0; color: #64748b; height: 50px; width: 100%; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 600; text-decoration: none; transition: 0.3s; }
        .btn-dashboard:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

        /* Table */
        .attendance-table th { color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; background: #f8fafc; }
        .attendance-table td { padding: 16px; color: #334155; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
        .attendance-table tr:hover { background: #fcfdfa; }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center animate-fade-in">
            <div>
                <h2 class="font-bold text-xl text-gray-800">
                    <span style="color: #8DA750;">Monitoring:</span> 
                    <span class="text-[#2D5128]">{{ $meeting->schedule->subject->name }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelas: {{ $meeting->schedule->classroom->name }}</p>
            </div>
            <div class="text-right">
                <span class="font-bold text-white bg-[#4a6741] px-4 py-2 rounded-lg text-sm">
                    {{ \Carbon\Carbon::parse($meeting->date)->format('d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- KIRI: QR CODE -->
                <div class="lg:col-span-4 animate-fade-in">
                    <div class="qr-card">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 rounded-lg bg-green-50"><i class="fa-solid fa-qrcode text-green-700"></i></div>
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
                            </div>
                        @else
                            <div class="bg-gray-50 border-2 dashed border-gray-300 rounded-2xl h-64 flex flex-col items-center justify-center mb-6">
                                <i class="fa-solid fa-lock text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-bold uppercase text-sm">Sesi Terkunci</p>
                            </div>
                        @endif

                        <div class="flex flex-col gap-3">
                            <form action="{{ route('meetings.toggle', $meeting->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="{{ $meeting->is_active ? 'btn-toggle-inactive' : 'btn-toggle-active' }}">
                                    @if($meeting->is_active)
                                        <i class="fa-solid fa-circle-stop mr-2"></i> Tutup Absensi
                                    @else
                                        <i class="fa-solid fa-circle-play mr-2"></i> Buka Absensi
                                    @endif
                                </button>
                            </form>
                            <a href="{{ route('teacher.dashboard') }}" class="btn-dashboard">
                                <i class="fa-solid fa-house mr-2"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KANAN: DAFTAR HADIR -->
                <div class="lg:col-span-8 animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="list-card">
                        
                        <!-- TABEL 1: SISWA -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Kehadiran Siswa</h3>
                                <p class="text-sm text-gray-500">Total: {{ $meeting->attendances->where('student.role', 'student')->count() }} Data</p>
                            </div>
                            <button onclick="location.reload()" class="flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 rounded-lg font-medium text-sm transition-all shadow-sm">
                                <i class="fa-solid fa-arrows-rotate"></i> Refresh
                            </button>
                        </div>

                        <div class="overflow-x-auto mb-8">
                            <table class="w-full text-left attendance-table">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Nama Siswa</th>
                                        <th>Status</th>
                                        <th>Jarak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($meeting->attendances->sortByDesc('created_at') as $attendance)
                                        <!-- FILTER: HANYA SISWA -->
                                        @if($attendance->student && $attendance->student->role == 'student')
                                            <tr>
                                                <td class="font-mono text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($attendance->scan_time)->timezone('Asia/Jakarta')->format('H:i') }}
                                                </td>
                                                <td>
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-gray-800">{{ $attendance->student->name }}</span>
                                                        <span class="text-xs text-gray-400">NIS: {{ $attendance->student->nip_nis ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($attendance->status == 'present')
                                                        <span class="badge-present"><i class="fa-solid fa-check"></i> Hadir</span>
                                                    @elseif($attendance->status == 'sick')
                                                        <span class="badge-sick"><i class="fa-solid fa-notes-medical"></i> Sakit</span>
                                                    @elseif($attendance->status == 'permission')
                                                        <span class="badge-perm"><i class="fa-solid fa-envelope"></i> Izin</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attendance->status == 'present')
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                                <div class="h-full bg-green-500" style="width: {{ max(100 - ($attendance->distance_meters), 10) }}%"></div>
                                                            </div>
                                                            <span class="text-xs font-bold text-gray-600">{{ number_format($attendance->distance_meters, 1) }}m</span>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400 italic">Via Wali Kelas</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-12 text-center text-gray-400">Belum ada data presensi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- TABEL 2: GURU / PENGAWAS -->
                        <div class="border-t border-dashed border-gray-200 pt-6">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Pengawas / Guru</h4>
                            <table class="w-full text-left attendance-table">
                                <thead class="bg-yellow-50 border-b border-yellow-100">
                                    <tr>
                                        <th class="text-yellow-800">Waktu Buka</th>
                                        <th class="text-yellow-800">Nama Pengajar</th>
                                        <th class="text-yellow-800">Status</th>
                                        <th class="text-yellow-800">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($meeting->attendances as $guru)
                                        <!-- FILTER: HANYA GURU -->
                                        @if($guru->student && $guru->student->role == 'teacher')
                                            <tr class="bg-yellow-50/30">
                                                <td class="font-mono text-sm text-gray-600">
                                                    {{ \Carbon\Carbon::parse($guru->scan_time)->timezone('Asia/Jakarta')->format('H:i') }}
                                                </td>
                                                <td>
                                                    <span class="font-bold text-gray-800">{{ $guru->student->name }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge-teacher"><i class="fa-solid fa-user-check mr-1"></i> PENGAJAR</span>
                                                </td>
                                                <td class="text-xs text-gray-500 italic">Membuka Sesi</td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="4" class="text-center py-2 text-xs text-gray-400">Data pengajar tidak ditemukan.</td></tr>
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