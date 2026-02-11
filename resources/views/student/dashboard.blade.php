<x-app-layout>
    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --asparagus:  #8DA750;
            --mindaro:    #E4EB9C;
            --cream:      #FAFAF5;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-up { animation: fadeInUp 0.6s ease-out forwards; }

        /* === BANNER === */
        .welcome-banner {
            background: linear-gradient(135deg, #142C14 0%, #2D5128 50%, #537B2F 100%);
            border-radius: 28px;
            position: relative;
            overflow: hidden;
            min-height: 160px;
            display: flex;
            align-items: center;
            box-shadow: 0 12px 30px -10px rgba(20, 44, 20, 0.3);
            padding: 1.5rem 1rem;
        }

        .welcome-banner::after {
            content: "";
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 280px;
            height: 280px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
        }

        /* === CLOCK - RESPONSIVE FIX === */
        .clock-card {
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 24px;
            padding: 1rem 1.5rem;
            text-align: center;
            min-width: unset; /* Hapus fixed width */
            width: auto;
            max-width: 100%;
            margin: 0 auto;
        }

        .clock-time {
            font-size: 2.5rem; /* Smaller base size */
            font-weight: 900;
            font-family: monospace;
            letter-spacing: -0.02em;
            color: white;
            line-height: 1.1;
            word-break: keep-all;
            white-space: nowrap;
        }

        .clock-divider {
            height: 1px;
            margin: 8px 0;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
        }

        .clock-date {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--mindaro);
            line-height: 1.2;
            word-break: break-word;
            white-space: normal;
        }

        /* === MAIN BUTTON (SCAN) === */
        .btn-scan {
            background: var(--cal-poly);
            color: white;
            padding: 1.2rem 3rem;
            border-radius: 22px;
            font-weight: 800;
            box-shadow: 0 10px 20px -5px rgba(45,81,40,0.4);
            transition: all 0.3s ease;
            width: 100%;
            max-width: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 1rem;
        }
        
        .btn-scan:hover {
            background: var(--dark-green);
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -5px rgba(20,44,20,0.5);
        }

        /* === SECONDARY BUTTONS (IZIN/SAKIT) === */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            width: 100%;
            max-width: 400px;
            margin-top: 24px;
        }

        .btn-secondary {
            background: #F7F9F0;
            color: var(--cal-poly);
            padding: 1rem;
            border-radius: 18px;
            font-weight: 700;
            font-size: 0.9rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
            min-height: 48px;
        }
        
        .btn-secondary:hover {
            background: white;
            border-color: var(--asparagus);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            color: var(--fern);
        }

        /* === STAT CARD === */
        .stat-card {
            background: white;
            border-radius: 26px;
            border: 1px solid #f3f4f6;
            border-bottom: 4px solid var(--mindaro);
            transition: 0.3s ease;
            padding: 1.5rem;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            border-bottom-color: var(--fern);
            box-shadow: 0 10px 25px -10px rgba(0,0,0,0.05);
        }

        /* === RESPONSIVE FIXES === */
        .banner-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .banner-content {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                text-align: left;
            }
            
            .clock-card {
                padding: 1rem 2.5rem;
            }
            
            .clock-time {
                font-size: 3rem;
            }
            
            .clock-date {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 640px) {
            .welcome-banner {
                padding: 1.25rem 1rem;
                min-height: 140px;
            }
            
            .clock-card {
                padding: 0.75rem 1.25rem;
                width: 100%;
                max-width: 280px;
            }
            
            .clock-time {
                font-size: 2rem;
            }
            
            .clock-date {
                font-size: 0.6rem;
                letter-spacing: 0.05em;
            }
            
            .btn-scan {
                padding: 1rem 2rem;
                font-size: 0.95rem;
            }
            
            .btn-secondary {
                font-size: 0.8rem;
                padding: 0.8rem;
                gap: 6px;
            }
        }

        @media (max-width: 480px) {
            .clock-time {
                font-size: 1.8rem;
            }
            
            .clock-date {
                font-size: 0.55rem;
            }
            
            .action-grid {
                gap: 12px;
            }
            
            .btn-secondary span {
                display: none;
            }
            
            .btn-secondary {
                padding: 0.75rem;
                font-size: 0.75rem;
            }
            
            .btn-secondary i {
                margin-right: 0;
                font-size: 1rem;
            }
        }

        @media (max-width: 360px) {
            .clock-time {
                font-size: 1.5rem;
            }
            
            .clock-date {
                font-size: 0.5rem;
                letter-spacing: 0.03em;
            }
            
            .clock-card {
                padding: 0.6rem 1rem;
            }
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">dashboard Siswa</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- BANNER & JAM - FIXED RESPONSIVE -->
            <div class="welcome-banner">
                <div class="banner-content">
                    <div class="text-white text-center sm:text-left">
                        <!-- Optional: You can add title here if needed -->
                    </div>
                    
                    <div class="clock-card">
                        <div id="digital-clock" class="clock-time">00:00:00</div>
                        <div class="clock-divider"></div>
                        <p class="clock-date">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8"> 
                
                <!-- KOLOM KIRI: SCANNER & ACTION -->
                <div class="lg:col-span-8 animate-up">
                    <div class="bg-white rounded-[32px] p-6 lg:p-12 border border-gray-100 flex flex-col items-center justify-center min-h-[480px] shadow-sm text-center">
                        
                        <!-- Icon Utama -->
                        <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-[30px] bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)] mb-6 lg:mb-8 shadow-inner">
                            <i class="fa-solid fa-qrcode text-3xl lg:text-4xl"></i>
                        </div>
                        
                        <h3 class="text-2xl lg:text-3xl font-black text-[var(--dark-green)] mb-2">Siap Absen?</h3>
                        <p class="text-gray-400 text-sm mb-8 lg:mb-10 max-w-sm leading-relaxed">
                            Pastikan Anda berada di lokasi kelas agar proses verifikasi berjalan lancar.
                        </p>

                        <!-- TOMBOL UTAMA: SCAN -->
                        <a href="{{ route('attendance.scan') }}" class="btn-scan">
                            <i class="fa-solid fa-camera"></i> 
                            <span class="hidden sm:inline">MULAI SCAN</span>
                            <span class="sm:hidden">SCAN</span>
                        </a>

                        <!-- TOMBOL SEKUNDER: IZIN & RIWAYAT -->
                        <div class="action-grid">
                            <a href="{{ route('leaves.create') }}" class="btn-secondary">
                                <i class="fa-solid fa-envelope-open-text"></i>
                                <span class="hidden xs:inline">Izin / Sakit</span>
                                <span class="xs:hidden">Izin</span>
                            </a>
                            <a href="{{ route('leaves.index') }}" class="btn-secondary">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span class="hidden xs:inline">Riwayat Izin</span>
                                <span class="xs:hidden">Riwayat</span>
                            </a>
                        </div>

                    </div>
                </div>

                <!-- KOLOM KANAN: STATISTIK -->
                <div class="lg:col-span-4 flex flex-col gap-6 animate-up" style="animation-delay: 0.2s;"> 
                    
                    <div class="space-y-2 px-2 mt-4 lg:mt-0">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">
                            Ringkasan
                        </h4>
                        <div class="h-1 w-8 bg-[var(--asparagus)] rounded-full"></div>
                    </div>

                    <!-- Card 1 -->
                    <div class="stat-card flex items-center gap-4 lg:gap-6">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)] text-lg lg:text-xl">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Hadir Bulan Ini</p>
                            <p class="text-2xl lg:text-3xl font-black text-[var(--dark-green)]">{{ $totalHadir }}</p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="stat-card flex items-center gap-4 lg:gap-6">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--asparagus)] text-lg lg:text-xl">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Ketepatan</p>
                            <p class="text-2xl lg:text-3xl font-black text-[var(--dark-green)]">{{ $persentase }}%</p>
                        </div>
                    </div>

                    <!-- Card 3 (Info Kelas) -->
                    <div class="stat-card flex items-center gap-4 lg:gap-6" style="border-bottom-color: var(--cal-poly);">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--cal-poly)] text-lg lg:text-xl">
                            <i class="fa-solid fa-chalkboard"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Kelas Saya</p>
                            <p class="text-lg lg:text-xl font-black text-[var(--dark-green)]">
                                {{ Auth::user()->classroom->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('digital-clock').textContent =
                now.toLocaleTimeString('en-GB', { hour12:false });
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</x-app-layout>