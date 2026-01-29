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

        /* === BANNER (Kecil & Rapih) === */
        .welcome-banner {
            background: linear-gradient(135deg, #142C14 0%, #2D5128 50%, #537B2F 100%);
            border-radius: 28px;
            position: relative;
            overflow: hidden;
            min-height: 160px; /* Ukuran lebih compact */
            display: flex;
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

        /* === CLOCK (Disesuaikan agar pas di banner kecil) === */
        .clock-card {
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 24px;
            padding: 12px 40px;
            text-align: center;
            min-width: 280px;
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

        /* === BUTTON === */
        .btn-scan {
            background: var(--cal-poly);
            color: white;
            padding: 1.2rem 3rem;
            border-radius: 22px;
            font-weight: 800;
            box-shadow: 0 10px 20px -5px rgba(45,81,40,0.4);
            transition: all 0.3s ease;
        }
        .btn-scan:hover {
            background: var(--dark-green);
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -5px rgba(20,44,20,0.5);
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
                <br>
                <span style="color: var(--p-light); font-weight: 800;">Dashboard Siswa</span>
            </h2>
        </x-slot>

        <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
            <div class="max-w-7xl mx-auto space-y-8">

                <div class="welcome-banner px-8 py-6">
                    <div class="relative z-10 w-full md:w-2/3">
                        <div class="clock-card">
                        <div id="digital-clock" class="clock-time">00:00:00</div>
                        <div class="clock-divider"></div>
                        <p class="text-[10px] uppercase font-bold tracking-[0.4em] text-[var(--mindaro)]">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                    </div>
                </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12"> <div class="lg:col-span-8 animate-up">
                    <div class="bg-white rounded-[32px] p-12 lg:p-20 border border-gray-100 flex flex-col items-center justify-center min-h-[480px] shadow-sm">
                        <div class="w-24 h-24 rounded-[30px] bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)] mb-10 shadow-inner">
                            <i class="fa-solid fa-qrcode " ></i>
                        </div>
                        <h3 class="text-3xl font-black text-[var(--dark-green)] mb-4">Siap Absen?</h3>
                        <br>
                        <p class="text-gray-400 text-base mb-14 text-center max-w-sm leading-relaxed">
                            Pastikan Anda berada di lokasi kelas agar proses verifikasi berjalan lancar.
                        </p>
<br>
                        <a href="{{ route('attendance.scan') }}" class="btn-scan flex items-center gap-4 text-xl tracking-wide">
                            <i class="fa-solid fa-camera"></i> MULAI SCAN SEKARANG
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-10 animate-up"> <div class="space-y-2 px-2">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">
                            Ringkasan
                        </h4>
                        <div class="h-1 w-8 bg-[var(--asparagus)] rounded-full"></div>
                    </div>

                    <div class="stat-card p-8 flex items-center gap-8">
                        <div class="w-16 h-16 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--fern)]">
                            
                        </div>
                        <div>
                            <p class="text-[11px] uppercase font-bold text-gray-400 tracking-wider mb-1">Total Kehadiran</p>
                            <p class="text-4xl font-black text-[var(--dark-green)]">24</p>
                        </div>
                    </div>

                    <br>

                    <div class="stat-card p-8 flex items-center gap-8">
                        <div class="w-16 h-16 rounded-2xl bg-[#F7F9F0] flex items-center justify-center text-[var(--asparagus)]">
                            
                        </div>
                        <div>
                            <p class="text-[11px] uppercase font-bold text-gray-400 tracking-wider mb-1">Ketepatan</p>
                            <p class="text-4xl font-black text-[var(--dark-green)]">98%</p>
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