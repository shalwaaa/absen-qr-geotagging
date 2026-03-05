<x-app-layout>
        <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Jadwal Hari Libur</span>
            </h2>
        </div>
    </x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --cream:      #FAFAF5;
        }

        /* Animasi Muncul */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Animasi Ikon Goyang */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .holiday-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: var(--cream);
        }

        .holiday-card {
            background: white;
            border-radius: 32px;
            padding: 50px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            border: 1px solid #f0fdf4;
            box-shadow: 0 20px 40px -10px rgba(45, 81, 40, 0.1);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .icon-wrapper {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px auto;
            color: var(--fern);
            font-size: 3.5rem;
            box-shadow: 0 10px 20px rgba(83, 123, 47, 0.15);
            animation: float 3s ease-in-out infinite;
        }

        .title {
            font-size: 2rem;
            font-weight: 900;
            color: var(--dark-green);
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .reason-badge {
            background: var(--fern);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(83, 123, 47, 0.3);
        }

        .date {
            color: #64748b;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 30px;
            font-family: monospace;
        }

        .message {
            color: #475569;
            line-height: 1.6;
            margin-bottom: 40px;
            font-size: 0.95rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--cal-poly);
            font-weight: 700;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: var(--cal-poly);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 81, 40, 0.2);
        }
    </style>

    <div class="holiday-container">
        <div class="holiday-card">
            
            <!-- Ikon -->
            <div class="icon-wrapper">
                <i class="fa-solid {{ $icon ?? 'fa-mug-hot' }}"></i>
            </div>

            <h1 class="title">Sekolah Libur</h1>
            
            <!-- Alasan Libur -->
            <div class="reason-badge">
                {{ $reason ?? 'Hari Libur' }}
            </div>

            <!-- Tanggal -->
            <div class="date">
                <i class="fa-regular fa-calendar mr-2"></i>
                {{ $date ?? date('d F Y') }}
            </div>

            <p class="message">
                Tidak ada kegiatan belajar mengajar (KBM) yang aktif hari ini. 
                Silakan gunakan waktu ini untuk beristirahat.
            </p>

            <!-- Jika Admin, kasih tombol ke pengaturan -->
            @if(Auth::user()->role == 'admin')
                <a href="{{ route('holidays.index') }}" class="btn-back">
                    <i class="fa-solid fa-gear"></i> Atur Kalender
                </a>
            @else
                <div class="text-xs text-gray-400">
                    &copy; ClockIn System
                </div>
            @endif
        </div>
    </div>
</x-app-layout>