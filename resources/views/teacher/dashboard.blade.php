<x-app-layout>
    <style>
        /* --- PALET WARNA ADMIN --- */
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --asparagus:  #8DA750;
            --mindaro:    #E4EB9C;
            --cream:      #FAFAF5;
        }

        /* ANIMASI */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .animate-up { animation: fadeInUp 0.5s ease-out forwards; }

        /* BANNER (Style Admin) */
        .welcome-banner {
            background-color: var(--cal-poly);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(45, 81, 40, 0.3);
            min-height: 160px;
            display: flex;
            align-items: center;
        }
        .welcome-banner::after { content: ""; position: absolute; right: -40px; top: 20%; width: 250px; height: 250px; background: rgba(255, 255, 255, 0.08); border-radius: 50%; }

        /* SCHEDULE CARD */
        .schedule-card {
            background: white; border: 1px solid #f0fdf4; border-bottom: 4px solid var(--mindaro);
            border-radius: 20px; padding: 24px; transition: all 0.3s ease; position: relative;
        }
        .schedule-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1); border-bottom-color: var(--fern); }

        /* BADGE JAM */
        .time-badge { background: #F2F7E6; color: var(--cal-poly); padding: 4px 12px; border-radius: 8px; font-weight: 800; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; }

        /* BUTTONS */
        .btn-open { background-color: var(--cal-poly); transition: all 0.3s; height: 40px; box-shadow: 0 4px 12px rgba(45, 81, 40, 0.2); width: 100%; border-radius: 12px; font-weight: 800; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.85rem; text-transform: uppercase; }
        .btn-open:hover { background-color: var(--dark-green); transform: translateY(-2px); }

        .btn-continue { background-color: var(--fern); transition: all 0.3s; box-shadow: 0 4px 12px rgba(83, 123, 47, 0.2); height: 40px; align-items: center; justify-content: center; width: 100%; text-decoration: none; border-radius: 12px; font-weight: 800; color: white; display: flex; font-size: 0.85rem; text-transform: uppercase; gap: 8px; }
        .btn-continue:hover { background-color: var(--cal-poly); transform: translateY(-2px); }

        .btn-disabled { background-color: #f3f4f6; color: #9ca3af; border: 1px solid #e5e7eb; cursor: not-allowed; box-shadow: none; }
        .btn-disabled:hover { transform: none; background-color: #f3f4f6; }

        /* PAGINATION MINI */
        .pagination-mini { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 32px; flex-wrap: wrap; }
        .pagination-mini .page-link { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #64748b; font-weight: 600; font-size: 0.8rem; transition: all 0.2s ease; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .pagination-mini .page-link:hover { background: #f8fafc; border-color: var(--cal-poly); color: var(--cal-poly); transform: translateY(-1px); }
        .pagination-mini .page-link.disabled { opacity: 0.5; pointer-events: none; background: #f1f5f9; border-color: #e2e8f0; }
        .pagination-mini .page-info { font-size: 0.9rem; font-weight: 600; color: #64748b; padding: 0 8px; }

        /* ... CSS lainnya ... */

        /* TOMBOL MERAH (TERLAMBAT TAPI BISA AKSES) */
        .btn-late { 
            background-color: #dc2626; /* Merah */
            transition: all 0.3s; 
            height: 40px; 
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2); 
            width: 100%; 
            border-radius: 12px; 
            font-weight: 800; 
            border: none; 
            color: white; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 8px; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
        }
        .btn-late:hover { 
            background-color: #b91c1c; 
            transform: translateY(-2px); 
            box-shadow: 0 6px 15px rgba(220, 38, 38, 0.3);
        }   
        @media (max-width: 480px) { .pagination-mini .page-info { font-size: 0.8rem; } }
    </style>

    <x-slot name="header">
        <h1 class="text-3xl font-black text-white mb-1" style="color: #E4EB9C">Jadwal Hari Ini</h1>
        <p class="text-white/80 text-sm italic font-medium" style="color: #E4EB9C">
            <span><i class="fa-solid fa-calendar-day mr-1"></i> {{ $today }}</span>
            <span>|</span>
            <span class="bg-white/20 px-2 py-0.5 rounded text-white font-bold">
                {{ $weekInfo }}
            </span>
        </p>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">
            
            @if($schedules->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100 animate-up">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="fa-solid fa-mug-hot text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[var(--dark-green)]">Tidak Ada Jadwal Mengajar</h3>
                    <p class="text-gray-400 mt-2">Sepertinya hari ini jadwal Anda kosong. Selamat beristirahat!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-up" style="animation-delay: 0.1s;">
                    @foreach($schedules as $s)
                        <div class="schedule-card flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <span class="time-badge">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                    </span>
                                    <div class="w-10 h-10 rounded-xl bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)] shadow-sm">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                </div>

                                <h3 class="text-xl font-black text-[var(--dark-green)] mb-1 leading-tight">
                                    {{ $s->subject->name }}
                                </h3>
                                <p class="text-[var(--asparagus)] font-bold text-sm uppercase tracking-wider mb-6">
                                    <i class="fa-solid fa-door-open mr-1"></i> {{ $s->classroom->name }}
                                </p>
                            </div>

                            @php
                                $now = \Carbon\Carbon::now('Asia/Jakarta');
                                $start = \Carbon\Carbon::parse($s->start_time)->timezone('Asia/Jakarta');
                                
                                // 1. Belum Waktunya (Lebih dari 15 menit sebelum jam mulai)
                                // Contoh: Masuk jam 07.00. Sekarang 06.30. (Batas buka 06.45)
                                $openTime = $start->copy()->subMinutes(15);
                                $isTooEarly = $now->lessThan($openTime);

                                // 2. Terlambat (Lebih dari 15 menit setelah jam mulai)
                                // Contoh: Masuk jam 07.00. Sekarang 07.16.
                                $lateTime = $start->copy()->addMinutes(15);
                                $isLate = $now->greaterThan($lateTime);
                            @endphp

                            <div class="mt-4">
                                @if($s->today_meeting)
                                    <!-- JIKA SUDAH DIBUKA -->
                                    <a href="{{ route('meetings.show', $s->today_meeting->id) }}" class="btn-continue">
                                        Lanjutkan Sesi Absen <i class="fa-solid fa-arrow-right"></i>
                                    </a>

                                @elseif($isTooEarly)
                                    <!-- JIKA BELUM WAKTUNYA (DISABLED ABU-ABU) -->
                                    <button disabled class="btn-open btn-disabled">
                                        <i class="fa-regular fa-clock"></i> Belum Waktunya
                                    </button>
                                    <p class="text-[10px] text-center text-gray-400 mt-2 font-semibold">
                                        Bisa dibuka pukul {{ $openTime->format('H:i') }}
                                    </p>

                                @else
                                    <!-- JIKA BISA DIBUKA (HIJAU ATAU MERAH) -->
                                    <form action="{{ route('meetings.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="schedule_id" value="{{ $s->id }}">
                                        
                                        <!-- Cek apakah terlambat? Pakai class btn-late (Merah) atau btn-open (Hijau) -->
                                        <button type="submit" class="{{ $isLate ? 'btn-late' : 'btn-open' }}">
                                            @if($isLate)
                                                <i class="fa-solid fa-triangle-exclamation"></i> Buka (Terlambat)
                                            @else
                                                <i class="fa-solid fa-qrcode"></i> Buka Kelas (QR)
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINATION MINI -->
                @if($schedules instanceof \Illuminate\Pagination\LengthAwarePaginator && $schedules->hasPages())
                    <div class="pagination-mini">
                        @if($schedules->onFirstPage())
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $schedules->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
                        @endif

                        <span class="page-info">
                            Halaman {{ $schedules->currentPage() }} / {{ $schedules->lastPage() }}
                        </span>

                        @if($schedules->hasMorePages())
                            <a href="{{ $schedules->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
                        @else
                            <span class="page-link disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>