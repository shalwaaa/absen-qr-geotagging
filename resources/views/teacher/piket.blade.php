<x-app-layout>
    <x-slot name="header"></x-slot>
    
    <!-- Style Hijau (Copy dari Dashboard Admin sebelumnya biar seragam) -->
    <style>
        .banner-piket {
            background: linear-gradient(135deg, #d97706, #fbbf24); /* Warna Kuning/Orange utk Piket */
            border-radius: 16px; color: white; padding: 24px; margin-bottom: 24px;
        }
        .card-schedule {
            background: white; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px;
            transition: 0.2s; position: relative; overflow: hidden;
        }
        .card-schedule:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .badge-teacher {
            background: #f0fdf4; color: #166534; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: bold;
        }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <div class="banner-piket shadow-lg">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fa-solid fa-user-shield"></i> Dashboard Guru Piket
                </h1>
                <p class="opacity-90 mt-1">Anda memiliki akses untuk membuka kelas guru lain yang berhalangan hadir.</p>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-4">Semua Jadwal Hari Ini ({{ $today }})</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($schedules as $s)
                    <div class="card-schedule border-l-4 {{ $s->today_meeting ? 'border-green-500' : 'border-gray-300' }}">
                        
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg text-gray-800">{{ $s->subject->name }}</h4>
                                <span class="text-sm text-gray-500 font-medium">{{ $s->classroom->name }}</span>
                            </div>
                            <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded">
                                {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}
                            </span>
                        </div>

                        <div class="mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-chalkboard-user text-gray-400"></i>
                            <span class="text-sm font-semibold text-gray-700">{{ $s->teacher->name }}</span>
                        </div>

                        <!-- LOGIKA TOMBOL -->
                        @if($s->today_meeting)
                            <a href="{{ route('meetings.show', $s->today_meeting->id) }}" class="block w-full text-center py-2 rounded-lg bg-green-100 text-green-700 font-bold hover:bg-green-200 transition">
                                <i class="fa-solid fa-check-circle"></i> Sudah Dibuka
                            </a>
                            <p class="text-xs text-center text-gray-400 mt-2">
                                Oleh: {{ $s->today_meeting->opener->name ?? 'Unknown' }}
                            </p>
                        @else
                            <form action="{{ route('meetings.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="schedule_id" value="{{ $s->id }}">
                                <button type="submit" class="block w-full text-center py-2 rounded-lg bg-orange-500 text-white font-bold hover:bg-orange-600 transition shadow-md" onclick="return confirm('Anda akan membuka kelas ini sebagai Guru Pengganti. Lanjutkan?')">
                                    <i class="fa-solid fa-lock-open"></i> Gantikan & Buka
                                </button>
                            </form>
                        @endif

                    </div>
                @empty
                    <div class="col-span-3 text-center py-10 text-gray-400">
                        <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
                        <p>Tidak ada jadwal pelajaran apapun hari ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>