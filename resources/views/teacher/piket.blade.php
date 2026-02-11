<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Dashboard Guru Piket</span>
                </h2>
                
            </div>
        </div>
    </x-slot>
    
    <style>
        /* --- VARIABEL WARNA HIJAU --- */
        :root {
            --primary-dark: #142C14;
            --primary-cal: #2D5128;
            --primary-fern: #537B2F;
            --primary-aspar: #8DA750;
            --primary-light: #E4EB9C;
        }
        
        /* --- KONTAINER UTAMA --- */
        .piket-container { 
            max-width: 7xl; 
            margin: 0 auto; 
        }
        
        /* --- ANIMASI ALERT (MERAH UNTUK URGENT) --- */
        @keyframes blink-red {
            0%, 100% { border-color: #ef4444; box-shadow: 0 0 15px rgba(239, 68, 68, 0.2); }
            50% { border-color: #fee2e2; box-shadow: 0 0 0 rgba(239, 68, 68, 0); }
        }
        
        /* --- GRID LAYOUT --- */
        .schedule-grid {
            display: grid; 
            grid-template-columns: repeat(1, 1fr); 
            gap: 1.5rem; 
            margin-top: 2rem;
        }
        
        @media (min-width: 768px) { 
            .schedule-grid { 
                grid-template-columns: repeat(2, 1fr); 
            } 
        }
        
        @media (min-width: 1024px) { 
            .schedule-grid { 
                grid-template-columns: repeat(3, 1fr); 
            } 
        }
        
        /* --- CARD UTAMA (HIJAU TEMA) --- */
        .schedule-card {
            background: white; 
            border-radius: 20px; 
            padding: 1.5rem; 
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease; 
            position: relative; 
            overflow: hidden; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .schedule-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08); 
        }

        /* --- CARD ALERT (JIKA DIPANGGIL ADMIN) --- */
        .schedule-card.alert-piket {
            animation: blink-red 2s infinite;
            border: 2px solid #ef4444 !important;
            background: linear-gradient(135deg, #fff5f5 0%, #fff0f0 100%) !important;
            position: relative;
            z-index: 1;
        }

        /* --- STATUS INDICATOR STRIP SISI KIRI --- */
        .status-indicator { 
            position: absolute; 
            left: 0; 
            top: 0; 
            bottom: 0; 
            width: 6px; 
            border-radius: 6px 0 0 6px; 
        }
        
        .status-open { 
            background: linear-gradient(to bottom, var(--primary-fern) 0%, var(--primary-cal) 100%); 
        }
        
        .status-closed { 
            background: linear-gradient(to bottom, var(--primary-aspar) 0%, var(--primary-fern) 100%); 
        }
        
        .status-urgent { 
            background: linear-gradient(to bottom, #ef4444 0%, #b91c1c 100%); 
        }

        /* --- HEADER CARD (MAPEL & JAM) --- */
        .card-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 1rem; 
            padding-left: 10px; 
        }
        
        .subject-title { 
            font-size: 1.1rem; 
            font-weight: 700; 
            color: var(--primary-dark); 
            margin-bottom: 0.25rem; 
        }
        
        .classroom-name { 
            font-size: 0.85rem; 
            color: var(--primary-fern); 
            font-weight: 600; 
        }
        
        .time-badge { 
            background: #f0f7ed; 
            color: var(--primary-cal); 
            font-size: 0.8rem; 
            font-weight: 700; 
            padding: 0.25rem 0.75rem; 
            border-radius: 20px; 
            border: 1px solid #d1fae5; 
            display: inline-flex; 
            align-items: center; 
            gap: 4px; 
        }

        /* --- INFO GURU --- */
        .teacher-info { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            margin-bottom: 1.5rem; 
            padding-left: 10px; 
        }
        
        .teacher-avatar { 
            width: 36px; 
            height: 36px; 
            background: linear-gradient(135deg, var(--primary-cal) 0%, var(--primary-fern) 100%); 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-weight: 700; 
            font-size: 0.9rem; 
            flex-shrink: 0; 
        }
        
        .teacher-name { 
            font-size: 0.9rem; 
            font-weight: 600; 
            color: var(--primary-dark); 
        }

        /* --- CONTAINER TOMBOL AKSI --- */
        .action-container { 
            margin-top: 1.5rem; 
            padding-left: 10px; 
        }
        
        .btn-action { 
            width: 100%; 
            padding: 0.875rem; 
            border-radius: 12px; 
            font-size: 0.9rem; 
            font-weight: 700; 
            text-align: center; 
            transition: all 0.2s ease; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 8px; 
            border: none; 
            cursor: pointer; 
        }

        /* --- TOMBOL HIJAU (NORMAL) --- */
        .btn-open { 
            background: linear-gradient(135deg, var(--primary-fern) 0%, var(--primary-cal) 100%); 
            color: white; 
            box-shadow: 0 4px 15px rgba(83, 123, 47, 0.25); 
        }
        
        .btn-open:hover { 
            background: linear-gradient(135deg, var(--primary-cal) 0%, var(--primary-dark) 100%); 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(83, 123, 47, 0.3); 
        }

        /* --- TOMBOL MERAH (URGENT - JIKA DIPANGGIL ADMIN) --- */
        .btn-urgent { 
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); 
            color: white; 
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.25); 
        }
        
        .btn-urgent:hover { 
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%); 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3); 
        }

        /* --- TOMBOL SUDAH DIBUKA (HIJAU MUDA) --- */
        .btn-opened { 
            background: linear-gradient(135deg, var(--primary-aspar) 0%, var(--primary-fern) 100%); 
            color: white; 
        }
        
        .btn-opened:hover { 
            background: linear-gradient(135deg, var(--primary-fern) 0%, var(--primary-cal) 100%); 
        }

        /* --- INFO PEMBUKA KELAS --- */
        .opener-info { 
            font-size: 0.75rem; 
            color: var(--primary-fern); 
            text-align: center; 
            margin-top: 0.5rem; 
            padding-left: 10px; 
        }

        /* --- NOTIFIKASI ALERT ADMIN --- */
        .admin-alert {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* --- INFO HARI INI --- */
        .today-info { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            margin-bottom: 1.5rem; 
            padding: 1rem 1.25rem; 
            background: linear-gradient(135deg, #f0f7ed 0%, #f8faf7 100%);
            border-radius: 16px; 
            border: 1px solid #dcfce7; 
            box-shadow: 0 4px 12px rgba(83, 123, 47, 0.05);
        }
        
        .today-icon { 
            color: var(--primary-fern); 
            font-size: 1.25rem; 
        }
        
        .today-text { 
            font-size: 0.95rem; 
            color: var(--primary-dark); 
            font-weight: 600; 
        }
        
        .today-date { 
            font-size: 0.9rem; 
            color: var(--primary-fern); 
            font-weight: 700; 
            margin-left: auto; 
            background: white; 
            padding: 0.25rem 0.75rem; 
            border-radius: 20px; 
            border: 1px solid #d1fae5; 
        }

        /* --- EMPTY STATE --- */
        .empty-state { 
            grid-column: 1 / -1; 
            text-align: center; 
            padding: 3rem 1rem; 
        }
        
        .empty-icon { 
            font-size: 3rem; 
            color: #cbd5e1; 
            margin-bottom: 1rem; 
            opacity: 0.5; 
        }
        
        .empty-title { 
            font-size: 1.25rem; 
            font-weight: 600; 
            color: var(--primary-dark); 
            margin-bottom: 0.5rem; 
        }
        
        .empty-subtitle { 
            font-size: 0.9rem; 
            color: var(--primary-fern); 
            max-width: 400px; 
            margin: 0 auto; 
        }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="piket-container">
            
            <!-- INFO HARI INI -->
            <div class="today-info">
                <div class="today-icon">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="today-text">
                    Semua Jadwal Pelajaran
                </div>
                <div class="today-date">
                    {{ $today }}
                </div>
            </div>

            <!-- GRID JADWAL -->
            <div class="schedule-grid">
                @forelse($schedules->sortByDesc('request_piket') as $s)
                    
                    <!-- KARTU JADWAL -->
                    <div class="schedule-card {{ $s->request_piket && !$s->today_meeting ? 'alert-piket' : '' }}">
                        
                        <!-- 1. NOTIFIKASI ADMIN (Jika dipanggil) -->
                        @if($s->request_piket && !$s->today_meeting)
                            <div class="admin-alert">
                                <i class="fa-solid fa-bell fa-shake"></i>
                                Admin Meminta Bantuan!
                            </div>
                        @endif

                        <!-- 2. STATUS INDICATOR -->
                        <div class="status-indicator {{ $s->request_piket && !$s->today_meeting ? 'status-urgent' : ($s->today_meeting ? 'status-open' : 'status-closed') }}"></div>
                        
                        <!-- 3. HEADER MAPEL & JAM -->
                        <div class="card-header">
                            <div>
                                <div class="subject-title">{{ $s->subject->name }}</div>
                                <div class="classroom-name">{{ $s->classroom->name }}</div>
                            </div>
                            <span class="time-badge">
                                <i class="fa-regular fa-clock text-xs"></i>
                                {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}
                            </span>
                        </div>

                        <!-- 4. INFO GURU -->
                        <div class="teacher-info">
                            <div class="teacher-avatar">
                                {{ strtoupper(substr($s->teacher->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="teacher-name">{{ $s->teacher->name }}</div>
                                <div class="text-xs text-slate-400">Guru Pengajar</div>
                            </div>
                        </div>

                        <!-- 5. TOMBOL AKSI -->
                        <div class="action-container">
                            @if($s->today_meeting)
                                <!-- SUDAH DIBUKA -->
                                <a href="{{ route('meetings.show', $s->today_meeting->id) }}" class="btn-action btn-opened">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Sudah Dibuka
                                </a>
                                <div class="opener-info">
                                    Oleh: {{ $s->today_meeting->opener->name ?? 'Unknown' }}
                                </div>
                            @else
                                <!-- BELUM DIBUKA -->
                                <form action="{{ route('meetings.store') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="schedule_id" value="{{ $s->id }}">
                                    
                                    <button type="submit" 
                                        class="btn-action {{ $s->request_piket ? 'btn-urgent' : 'btn-open' }}" 
                                        onclick="return confirm('Anda akan membuka kelas ini sebagai Guru Pengganti. Lanjutkan?')">
                                        <i class="fa-solid fa-lock-open"></i>
                                        {{ $s->request_piket ? 'GANTIKAN SEKARANG!' : 'Gantikan & Buka' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- KOSONG -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa-regular fa-calendar-xmark"></i>
                        </div>
                        <div class="empty-title">Tidak ada jadwal pelajaran</div>
                        <div class="empty-subtitle">
                            Tidak ada jadwal pelajaran untuk hari ini.
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>