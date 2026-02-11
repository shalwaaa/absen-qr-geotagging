<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Dashboard Admin</span>
                </h2>
            </div>
            <div class="hidden sm:block">
                {{-- <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200">
                    GEO-FENCING ACTIVE
                </span> --}}
            </div>
        </div>
    </x-slot>

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --asparagus:  #8DA750;
            --mindaro:    #E4EB9C;
            --cream:      #FAFAF5;
        }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* KARTU STATISTIK */
        .stat-card {
            background: white; 
            border: 1px solid #f0fdf4; 
            border-bottom: 4px solid var(--mindaro);
            border-radius: 20px; 
            padding: 30px 24px;
            display: flex; align-items: center; gap: 20px;
            transition: transform 0.2s, box-shadow 0.2s; 
            animation: fadeInUp 0.8s ease-out forwards;
            height: 100%;
        }
        .stat-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.08); 
            border-bottom-color: var(--fern); 
        }

        .icon-box { 
            width: 64px; height: 64px; 
            border-radius: 18px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.8rem; 
            background-color: #F7F9F0; color: var(--fern); 
        }

        .stat-label { font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
        .stat-value { font-size: 2.5rem; font-weight: 900; color: var(--dark-green); line-height: 1; }

        /* CHART CARD */
        .chart-card {
            background: white; 
            border-radius: 24px; 
            padding: 32px; /* Padding dalam lebih lega */
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            animation: fadeInUp 1s ease-out forwards;
            height: 100%; /* Biar tinggi kartu seragam */
            display: flex;
            flex-direction: column;
        }
        
        /* FILTER TINGKAT */
        .grade-filter {
            display: inline-flex; background: white; padding: 6px; border-radius: 16px;
            border: 1px solid #e2e8f0; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .filter-btn {
            padding: 10px 24px; border-radius: 12px; font-weight: 700; font-size: 14px;
            color: #64748b; text-decoration: none; transition: all 0.2s;
        }
        .filter-btn:hover { background: #f8fafc; color: var(--dark-green); }
        .filter-btn.active { background: var(--cal-poly); color: white; box-shadow: 0 4px 10px rgba(45,81,40,0.3); }

        .chart-title { font-size: 1.25rem; font-weight: 800; color: var(--dark-green); margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
        .badge-jurusan { background: var(--mindaro); color: var(--dark-green); padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; }
    </style>

    <div class="py-10 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto space-y-10">
            

            <!-- 1. KARTU STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Siswa -->
                <div class="stat-card">
                    <div class="icon-box"><i class="fa-solid fa-user-graduate"></i></div>
                    <div><p class="stat-label">Total Siswa</p><h3 class="stat-value">{{ $jumlah_siswa }}</h3></div>
                </div>
                <!-- Guru -->
                <div class="stat-card" style="animation-delay: 0.1s;">
                    <div class="icon-box" style="color: #d97706; background: #fffbeb;"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div><p class="stat-label">Total Guru</p><h3 class="stat-value">{{ $jumlah_guru }}</h3></div>
                </div>
                <!-- Kelas -->
                <div class="stat-card" style="animation-delay: 0.2s;">
                    <div class="icon-box" style="color: #2563eb; background: #eff6ff;"><i class="fa-solid fa-school"></i></div>
                    <div><p class="stat-label">Total Kelas</p><h3 class="stat-value">{{ $jumlah_kelas }}</h3></div>
                </div>
                <!-- Mapel -->
                <div class="stat-card" style="animation-delay: 0.3s;">
                    <div class="icon-box" style="color: #9333ea; background: #faf5ff;"><i class="fa-solid fa-book"></i></div>
                    <div><p class="stat-label">Total Mapel</p><h3 class="stat-value">{{ $jumlah_mapel }}</h3></div>
                </div>
            </div>

            <br>

            <!-- 2. FILTER TINGKAT -->
            <div class="flex justify-center">
                <div class="grade-filter">
                    <a href="?grade=10" class="filter-btn {{ $selectedGrade == 10 ? 'active' : '' }}">Kelas 10</a>
                    <a href="?grade=11" class="filter-btn {{ $selectedGrade == 11 ? 'active' : '' }}">Kelas 11</a>
                    <a href="?grade=12" class="filter-btn {{ $selectedGrade == 12 ? 'active' : '' }}">Kelas 12</a>
                </div>
            </div>

            <!-- 3. GRAFIK JURUSAN (GRID 2 KOLOM YANG LEBAR & TINGGI) -->
            @if(empty($charts))
                <div class="text-center py-24 bg-white rounded-3xl border border-dashed border-gray-300">
                    <i class="fa-regular fa-chart-bar text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 font-bold text-lg">Belum ada data kelas untuk Tingkat {{ $selectedGrade }}</p>
                    <p class="text-sm text-gray-400">Pastikan data kelas sudah disinkronisasi.</p>
                </div>
            @else
                <!-- Menggunakan Grid 1 Kolom (HP) dan 2 Kolom (Desktop) agar grafik LEBAR -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    @foreach($charts as $major => $data)
                        <div class="chart-card">
                            <div class="chart-title">
                                <span class="badge-jurusan">{{ $major }}</span>
                                <span>Performa {{ $major }}</span>
                            </div>

                            <!-- CANVAS DIPERBESAR TINGGINYA (500px) -->
                            <div class="relative w-full h-[500px]">
                                <canvas id="chart-{{ $major }}"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
<br>
            <div class="mt-12 text-center pb-10">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[var(--fern)] text-white rounded-xl font-bold text-sm hover:bg-[var(--dark-green)] transition shadow-lg" style="background-color: #2D5128">
                    <i class="fa-solid fa-file-pdf"></i> Lihat Laporan Lengkap
                </a>
            </div>

        </div>
    </div>

    <!-- SCRIPT RENDER CHART -->
 <!-- SCRIPT RENDER CHART (BAR CHART) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartsData = @json($charts);
            
            // Palet Warna untuk Bar Chart (Biar beda tiap kelas)
            const barColors = ['#2D5128', '#537B2F', '#8DA750', '#E4EB9C', '#F59E0B', '#1F2937'];

            Object.keys(chartsData).forEach(major => {
                const ctx = document.getElementById(`chart-${major}`).getContext('2d');
                const chartInfo = chartsData[major];

                // Transformasi Data untuk Bar Chart
                // Kita ambil rata-rata 6 bulan terakhir saja untuk Bar Chart agar lebih simpel
                // Atau, kita tampilkan data bulan terakhir saja
                
                // Opsi Terbaik: Menampilkan data bulan per bulan dengan Grouped Bar Chart
                
                new Chart(ctx, {
                    type: 'bar', // Ubah jadi BAR
                    data: {
                        labels: chartInfo.labels, // Nama Bulan
                        datasets: chartInfo.datasets.map((ds, index) => ({
                            ...ds,
                            backgroundColor: barColors[index % barColors.length], // Warna Bar Solid
                            borderColor: 'transparent',
                            borderRadius: 6, // Sudut tumpul
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: { 
                                    usePointStyle: true, 
                                    boxWidth: 10,
                                    font: { family: 'Poppins', size: 11 },
                                    color: '#64748b'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#142C14',
                                titleFont: { family: 'Poppins', size: 14 },
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: '#f1f5f9', borderDash: [5, 5] },
                                ticks: { font: { family: 'Poppins', weight: 'bold' } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Poppins' } }
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        }
                    }
                });
            });
        });
    </script>
</x-app-layout>