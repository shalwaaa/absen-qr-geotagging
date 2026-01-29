    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <br>
                <span style="color: var(--p-light); font-weight: 800;">Dashboard Admin</span>
            </h2>
        </x-slot>

        <style>
            /* --- PALET WARNA (DIKUNCI) --- */
            :root {
                --dark-green: #142C14;
                --cal-poly:   #2D5128;
                --fern:       #537B2F;
                --asparagus:  #8DA750;
                --mindaro:    #E4EB9C;
                --cream:      #FAFAF5; /* Background halaman lebih soft */
            }

            /* 1. ANIMASI */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* 2. BANNER BARU (Lengkungan Mulus) */
            .welcome-banner {
                background-color: var(--cal-poly);
                border-radius: 20px;
                position: relative;
                overflow: hidden; /* Penting biar lingkaran ga bocor */
                box-shadow: 0 10px 25px -5px rgba(45, 81, 40, 0.4);
                animation: fadeInUp 0.6s ease-out;
                min-height: 180px; /* Tinggi fix biar gagah */
                display: flex;
                align-items: center;
            }

            /* Lingkaran Dekorasi (Mulus tidak penyon) */
            .welcome-banner::after {
                content: "";
                position: absolute;
                right: -50px;
                top: 50%;
                transform: translateY(-50%); /* Tengah vertikal presisi */
                width: 350px; 
                height: 350px;
                background: rgba(255, 255, 255, 0.1); 
                border-radius: 50%; /* Lingkaran sempurna */
                z-index: 0;
            }

            /* Lingkaran kecil tambahan biar estetik */
            .welcome-banner::before {
                content: "";
                position: absolute;
                right: 240px;
                top: -20px;
                width: 100px; height: 100px;
                background: rgba(228, 235, 156, 0.1); /* Warna Mindaro transparan */
                border-radius: 50%;
                z-index: 0;
            }

            /* 3. KARTU STATISTIK */
            .stat-card {
                background: white;
                border: 1px solid #f0fdf4;
                border-bottom: 3px solid var(--mindaro); /* Aksen bawah */
                border-radius: 16px;
                padding: 20px;
                display: flex;
                align-items: center;
                gap: 16px;
                transition: transform 0.2s, box-shadow 0.2s;
                animation: fadeInUp 0.8s ease-out forwards;
            }
            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
                border-bottom-color: var(--fern);
            }

            .icon-box {
                width: 48px; height: 48px;
                border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.2rem;
                background-color: #F7F9F0; 
                color: var(--fern);
            }

            /* 4. CONTENT WRAPPER */
            .content-card {
                background: white;
                border-radius: 20px;
                padding: 24px;
                border: 1px solid #eef2eb;
                box-shadow: 0 2px 10px rgba(0,0,0,0.01);
                height: 100%;
                animation: fadeInUp 1s ease-out forwards;
            }

            /* 5. TABEL YANG LEBIH RAPI */
            .modern-table { width: 100%; border-collapse: collapse; }
            .modern-table th {
                text-align: left;
                font-size: 0.75rem;
                color: var(--asparagus);
                font-weight: 700;
                text-transform: uppercase;
                padding: 12px 16px;
                border-bottom: 2px solid #f0fdf4;
            }
            .modern-table td {
                padding: 16px;
                font-size: 0.9rem;
                color: var(--dark-green);
                border-bottom: 1px solid #f7fee7;
            }
            .modern-table tr:last-child td { border-bottom: none; }
            .modern-table tr:hover td { background-color: #FAFCF8; }

            .time-badge {
                background: #F2F7E6; 
                color: var(--cal-poly);
                padding: 6px 12px;
                border-radius: 8px;
                font-weight: 700;
                font-size: 0.8rem;
                display: inline-block;
            }

            /* List Akses Cepat */
            .quick-link {
                display: flex; align-items: center; justify-content: space-between;
                padding: 12px 16px;
                background: #FAFCF8;
                border: 1px solid #eef2eb;
                border-radius: 12px;
                margin-bottom: 8px;
                transition: all 0.2s;
            }
            .quick-link:hover {
                border-color: var(--mindaro);
                background: #F2F7E6;
            }
        </style>

        <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- 1. BANNER -->
                <div class="welcome-banner px-8 py-6">
                    <div class="relative z-10 w-full md:w-2/3">
                        <h1 class="text-3xl font-bold text-white mb-2">Dashboard Overview</h1>
                        <p class="text-sm">Pantau statistik dan jadwal hari ini dalam satu layar.</p>
                    </div>
                </div>

                <!-- 2. STATISTIK -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="stat-card">
                        <div class="icon-box"><i class="fa-solid fa-user-graduate"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Siswa</p>
                            <h3 class="text-2xl font-black text-[#142C14]">{{ $jumlah_siswa }}</h3>
                        </div>
                    </div>
                    <div class="stat-card" style="animation-delay: 0.1s;">
                        <div class="icon-box"><i class="fa-solid fa-chalkboard-user"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Guru</p>
                            <h3 class="text-2xl font-black text-[#142C14]">{{ $jumlah_guru }}</h3>
                        </div>
                    </div>
                    <div class="stat-card" style="animation-delay: 0.2s;">
                        <div class="icon-box"><i class="fa-solid fa-school"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Kelas</p>
                            <h3 class="text-2xl font-black text-[#142C14]">{{ $jumlah_kelas }}</h3>
                        </div>
                    </div>
                    <div class="stat-card" style="animation-delay: 0.3s;">
                        <div class="icon-box"><i class="fa-solid fa-book"></i></div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Mapel</p>
                            <h3 class="text-2xl font-black text-[#142C14]">{{ $jumlah_mapel }}</h3>
                        </div>
                    </div>
                </div>

                <br>
                <br>

                <!-- 3. KONTEN UTAMA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- TABEL JADWAL (KIRI) -->
                    <div class="lg:col-span-2">
                        <div class="content-card">
                            <div class="flex justify-between items-end mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-[#142C14]">Jadwal Pelajaran Hari Ini</h3>
                                    <p class="text-xs text-gray-400 mt-1">Status realtime berdasarkan jadwal aktif.</p>
                                </div>
                                <a href="{{ route('schedules.index') }}" class="text-xs font-bold text-[#537B2F] hover:underline">
                                    Lihat Semua
                                </a>
                            </div>

                            <div class="overflow-hidden rounded-xl border border-gray-100">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Mapel</th>
                                            <th>Guru</th>
                                            <th>Kelas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($schedules_today as $s)
                                        <tr>
                                            <td>
                                                <span class="time-badge">
                                                    {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-bold">{{ $s->subject->name }}</span>
                                            </td>
                                            <td class="text-sm text-gray-600">
                                                {{ $s->teacher->name }}
                                            </td>
                                            <td>
                                                <span class="text-xs font-bold text-[#537B2F] bg-[#f0fdf4] px-2 py-1 rounded">
                                                    {{ $s->classroom->name }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <div class="flex flex-col items-center opacity-50">
                                                    <i class="fa-regular fa-calendar-xmark text-3xl mb-2"></i>
                                                    <span class="text-sm">Tidak ada jadwal hari ini.</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- WIDGET KELAS (KANAN) -->
                    <div>
                        <div class="content-card flex flex-col h-full">
                            <h3 class="text-lg font-bold text-[#142C14] mb-4">Kelas Terbaru</h3>
                            
                            <div class="flex-1 space-y-2">
                                @forelse($classrooms_list as $c)
                                <a href="{{ route('classrooms.show', $c->id) }}" class="quick-link group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#E4EB9C] flex items-center justify-center text-[#2D5128] font-bold text-xs group-hover:bg-[#2D5128] group-hover:text-white transition">
                                            {{ substr($c->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-bold text-[#142C14]">{{ $c->name }}</span>
                                    </div>
                                    <i class="fa-solid fa-arrow-right text-xs text-gray-300 group-hover:text-[#537B2F]"></i>
                                </a>
                                @empty
                                <p class="text-sm text-gray-400 italic">Belum ada kelas.</p>
                                @endforelse
                            </div>

                            <div class="mt-6 pt-6 border-t border-dashed border-gray-200">
                                <a href="{{ route('classrooms.create') }}" class="block w-full text-center py-3 rounded-xl border border-dashed border-[#8DA750] text-[#537B2F] font-bold text-sm hover:bg-[#f0fdf4] transition">
                                    + Tambah Kelas Baru
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </x-app-layout>