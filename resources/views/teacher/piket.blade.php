<x-app-layout>
    <x-slot name="header">
        <div style="margin-bottom: 2rem;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Dashboard Guru Piket</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Akses khusus untuk membuka kelas guru yang berhalangan hadir</p>
        </div>
    </x-slot>
    
    <style>
        /* Main Container */
        .piket-container {
            max-width: 7xl;
            margin: 0 auto;
        }
        
        /* Hero Banner - GANTI KE HIJAU */
        .piket-banner {
            background: linear-gradient(135deg, #2D5128 0%, #537B2F 100%);
            border-radius: 20px;
            padding: 2rem 2.5rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(45, 81, 40, 0.2);
        }
        
        .piket-banner::after {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        
        .piket-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 2;
        }
        
        .piket-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.5;
            position: relative;
            z-index: 2;
        }
        
        /* Schedule Grid */
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
        
        /* Schedule Card */
        .schedule-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .schedule-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }
        
        /* Status Indicator - GANTI KE HIJAU */
        .status-indicator {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            border-radius: 6px 0 0 6px;
        }
        
        .status-open {
            background: linear-gradient(to bottom, #059669 0%, #047857 100%);
        }
        
        .status-closed {
            background: linear-gradient(to bottom, #8DA750 0%, #537B2F 100%);
        }
        
        /* Card Header */
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
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        
        .classroom-name {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }
        
        .time-badge {
            background: #f8fafc;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Teacher Info */
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
            background: linear-gradient(135deg, #2D5128 0%, #537B2F 100%);
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
            color: #334155;
        }
        
        /* Action Buttons - GANTI KE HIJAU */
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
        
        .btn-open {
            background: linear-gradient(135deg, #537B2F 0%, #2D5128 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(83, 123, 47, 0.25);
        }
        
        .btn-open:hover {
            background: linear-gradient(135deg, #2D5128 0%, #142C14 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(83, 123, 47, 0.3);
        }
        
        .btn-opened {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
        }
        
        .btn-opened:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }
        
        /* Opener Info */
        .opener-info {
            font-size: 0.75rem;
            color: #64748b;
            text-align: center;
            margin-top: 0.5rem;
            padding-left: 10px;
        }
        
        /* Empty State */
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
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        
        .empty-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Today Info - GANTI KE HIJAU */
        .today-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            background: #f0f7ed;
            border-radius: 12px;
            border: 1px solid #dcfce7;
        }
        
        .today-icon {
            color: #537B2F;
            font-size: 1.25rem;
        }
        
        .today-text {
            font-size: 0.95rem;
            color: #142C14;
            font-weight: 600;
        }
        
        .today-date {
            font-size: 0.9rem;
            color: #537B2F;
            font-weight: 700;
            margin-left: auto;
            background: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            border: 1px solid #d1fae5;
        }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="piket-container">
            
            <!-- Hero Banner - SUDAH HIJAU -->
            <div class="piket-banner">
                <div class="piket-title">
                    <i class="fa-solid fa-user-shield"></i>
                    Dashboard Guru Piket
                </div>
                <p class="piket-subtitle">
                    Anda memiliki akses khusus untuk membuka kelas guru lain yang berhalangan hadir.
                </p>
            </div>

            <!-- Today Info - SUDAH HIJAU -->
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

            <!-- Schedule Grid -->
            <div class="schedule-grid">
                @forelse($schedules as $s)
                    <div class="schedule-card">
                        <!-- Status Indicator - SUDAH HIJAU -->
                        <div class="status-indicator {{ $s->today_meeting ? 'status-open' : 'status-closed' }}"></div>
                        
                        <!-- Card Header -->
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

                        <!-- Teacher Info -->
                        <div class="teacher-info">
                            <div class="teacher-avatar">
                                {{ strtoupper(substr($s->teacher->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="teacher-name">{{ $s->teacher->name }}</div>
                                <div class="text-xs text-slate-400">Guru Pengajar</div>
                            </div>
                        </div>

                        <!-- Action Button - SUDAH HIJAU -->
                        <div class="action-container">
                            @if($s->today_meeting)
                                <a href="{{ route('meetings.show', $s->today_meeting->id) }}" class="btn-action btn-opened">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Sudah Dibuka
                                </a>
                                <div class="opener-info">
                                    Oleh: {{ $s->today_meeting->opener->name ?? 'Unknown' }}
                                </div>
                            @else
                                <form action="{{ route('meetings.store') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="schedule_id" value="{{ $s->id }}">
                                    <button type="submit" class="btn-action btn-open" onclick="return confirm('Anda akan membuka kelas ini sebagai Guru Pengganti. Lanjutkan?')">
                                        <i class="fa-solid fa-lock-open"></i>
                                        Gantikan & Buka
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa-regular fa-calendar-xmark"></i>
                        </div>
                        <div class="empty-title">Tidak ada jadwal pelajaran</div>
                        <div class="empty-subtitle">
                            Tidak ada jadwal pelajaran untuk hari ini. 
                            Silahkan cek kembali besok atau hubungi admin jika ada perubahan jadwal.
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <script>
        // Confirmation with better UI
        document.addEventListener('DOMContentLoaded', function() {
            const openButtons = document.querySelectorAll('.btn-open');
            
            openButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('⚠️ Anda akan membuka kelas ini sebagai Guru Pengganti.\n\nPastikan guru yang bersangkutan berhalangan hadir.\n\nLanjutkan?')) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            });
        });
    </script>
</x-app-layout>