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
            align-items: center; /* Tengahkan vertikal */
            box-shadow: 0 12px 30px -10px rgba(20, 44, 20, 0.3);
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

        /* === CLOCK === */
        .clock-card {
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 24px;
            padding: 12px 40px;
            text-align: center;
            min-width: 280px;
            display: inline-block; /* Agar tidak melebar full */
        }

        .clock-time {
            font-size: 3rem; 
            font-weight: 900;
            font-family: monospace;
            letter-spacing: -0.02em;
            color: white;
            line-height: 1.1;
        }

        .clock-divider {
            height: 1px;
            margin: 8px 0;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.3), transparent);
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
            width: 100%; /* Full width di mobile */
            max-width: 400px;
            justify-content: center;
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
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-bottom-color: var(--fern);
            box-shadow: 0 10px 25px -10px rgba(0,0,0,0.05);
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <span style="color: var(--p-light); font-weight: 800;"></span>
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- BANNER & JAM -->
            <div class="welcome-banner px-8 py-6">
                <div class="relative z-10 w-full flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-white">
                        <h1 class="text-2xl font-bold opacity-90">Halo, {{ Auth::user()->name }}!</h1>
                        <p class="text-sm opacity-75">Jangan lupa absen tepat waktu ya.</p>
                    </div>
                    
                    <div class="clock-card">
                        <div id="digital-clock" class="clock-time">00:00:00</div>
                        <div class="clock-divider"></div>
                        <p class="text-[10px] uppercase font-bold tracking-[0.4em] text-[var(--mindaro)]">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8"> 
                
                <!-- KOLOM KIRI: SCANNER & ACTION -->
                <div class="lg:col-span-8 animate-up">
                    <div class="bg-white rounded-[32px] p-8 lg:p-12 border border-gray-100 flex flex-col items-center justify-center min-h-[480px] shadow-sm text-center">
                        
                        <!-- Icon Utama -->
                        <div class="w-24 h-24 rounded-[30px] bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)] mb-8 shadow-inner">
                            <i class="fa-solid fa-qrcode text-4xl"></i>
                        </div>
                        
                        <h3 class="text-3xl font-black text-[var(--dark-green)] mb-2">Siap Absen?</h3>
                        <p class="text-gray-400 text-sm mb-10 max-w-sm leading-relaxed">
                            Pastikan Anda berada di lokasi kelas agar proses verifikasi berjalan lancar.
                        </p>

                        <!-- TOMBOL UTAMA: SCAN -->
                        <a href="{{ route('attendance.scan') }}" class="btn-scan flex items-center gap-3 text-lg tracking-wide mb-2">
                            <i class="fa-solid fa-camera"></i> MULAI SCAN
                        </a>

                        <!-- TOMBOL SEKUNDER: IZIN & RIWAYAT (INI YANG BARU) -->
                        <div class="action-grid">
                            <a href="{{ route('leaves.create') }}" class="btn-secondary">
                                <i class="fa-solid fa-envelope-open-text"></i> Izin / Sakit
                            </a>
                            <a href="{{ route('leaves.index') }}" class="btn-secondary">
                                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Izin
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
                    <div class="stat-card p-6 flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)] text-xl">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Hadir Bulan Ini</p>
                            <!-- Kamu bisa ganti angka statis ini dengan variabel PHP nanti -->
                            <p class="text-3xl font-black text-[var(--dark-green)]">24</p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="stat-card p-6 flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--asparagus)] text-xl">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Ketepatan</p>
                            <p class="text-3xl font-black text-[var(--dark-green)]">98%</p>
                        </div>
                    </div>

                    <!-- Card 3 (Info Kelas) -->
                    <div class="stat-card p-6 flex items-center gap-6" style="border-bottom-color: var(--cal-poly);">
                        <div class="w-14 h-14 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--cal-poly)] text-xl">
                            <i class="fa-solid fa-chalkboard"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Kelas Saya</p>
                            <p class="text-xl font-black text-[var(--dark-green)]">
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