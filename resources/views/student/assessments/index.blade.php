<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Evaluasi Sikap & Performa</span>
                </h2>
                <p style="color: #E4EB9C">Lihat grafik perkembangan karaktermu berdasarkan penilaian Wali Kelas.</p>
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

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .header-banner {
            background: linear-gradient(135deg, var(--cal-poly), var(--fern));
            border-radius: 24px; padding: 32px; color: white;
            box-shadow: 0 10px 30px -5px rgba(45, 81, 40, 0.3);
            margin-bottom: 32px; position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: space-between;
        }
        .header-banner::after {
            content: ""; position: absolute; right: -5%; top: -20%; width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); border-radius: 50%;
        }

        .card-custom {
            background: white; border-radius: 24px; padding: 32px;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            animation: fadeInUp 0.6s ease-out forwards; height: 100%;
        }

        /* Timeline Style */
        .timeline-item {
            padding: 20px; border-radius: 16px; border: 1px solid #f1f5f9;
            background: #fdfdf9; margin-bottom: 16px; transition: 0.3s;
            border-left: 5px solid var(--fern);
        }
        .timeline-item:hover { transform: translateX(5px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left-color: var(--cal-poly); }
        
        .score-badge {
            background: #f0fdf4; color: var(--dark-green); padding: 4px 12px;
            border-radius: 20px; font-weight: 800; font-size: 0.8rem; border: 1px solid #bbf7d0;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .quote-box {
            background: white; padding: 16px; border-radius: 12px; font-style: italic;
            color: #475569; margin-top: 12px; border: 1px dashed #cbd5e1; font-size: 0.9rem;
        }
    </style>

    <div class="py-10 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto">

            @if($assessments->isEmpty())
                <div class="text-center py-20 bg-white rounded-[24px] border border-dashed border-gray-300">
                    <i class="fa-regular fa-star text-6xl text-gray-200 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-600">Belum Ada Penilaian</h3>
                    <p class="text-gray-400 mt-2">Wali Kelasmu belum memberikan evaluasi karakter bulan ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    
                    <!-- KIRI: GRAFIK RADAR -->
                    <div class="lg:col-span-2">
                        <div class="card-custom flex flex-col justify-center items-center">
                            <div class="w-full text-center mb-6">
                                <h3 class="font-black text-xl text-[var(--dark-green)]">Kekuatan Karakter</h3>
                                <p class="text-sm text-gray-500">Periode: {{ \Carbon\Carbon::parse('01-'.$latestAssessment->period_month)->translatedFormat('F Y') }}</p>
                            </div>
                            
                            <!-- CANVAS RADAR CHART -->
                            <div class="relative w-full aspect-square max-h-[350px]">
                                <canvas id="radarChart"></canvas>
                            </div>

                            <div class="mt-8 text-center bg-green-50 p-4 rounded-xl border border-green-100 w-full">
                                @php
                                    $avgScore = $latestAssessment->details->avg('score');
                                @endphp
                                <p class="text-xs font-bold text-green-600 uppercase tracking-widest mb-1">Rata-Rata Nilai</p>
                                <h4 class="text-3xl font-black text-[var(--dark-green)]">{{ number_format($avgScore, 1) }} <span class="text-lg text-gray-400">/ 5.0</span></h4>
                            </div>
                        </div>
                    </div>

                    <!-- KANAN: RIWAYAT FEEDBACK -->
                    <div class="lg:col-span-3">
                        <div class="card-custom">
                            <h3 class="font-black text-xl text-[var(--dark-green)] mb-6 flex items-center gap-3">
                                <i class="fa-solid fa-clock-rotate-left text-[var(--fern)]"></i> Catatan Wali Kelas
                            </h3>

                            <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($assessments as $ast)
                                    <div class="timeline-item">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-lg">
                                                    {{ \Carbon\Carbon::parse('01-'.$ast->period_month)->translatedFormat('F Y') }}
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-1">Penilai: {{ $ast->evaluator->name }}</p>
                                            </div>
                                            <div class="score-badge">
                                                <i class="fa-solid fa-star text-yellow-400"></i>
                                                {{ number_format($ast->details->avg('score'), 1) }}
                                            </div>
                                        </div>

                                        @if($ast->general_notes)
                                            <div class="quote-box">
                                                <i class="fa-solid fa-quote-left text-gray-300 mr-2 text-lg"></i>
                                                {{ $ast->general_notes }}
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 italic mt-2">Tidak ada catatan tambahan.</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>

    <!-- SCRIPT RADAR CHART -->
    @if($assessments->isNotEmpty())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('radarChart').getContext('2d');
            
            const labels = @json($chartLabels);
            const dataScores = @json($chartData);

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets:[{
                        label: 'Nilai Sikap',
                        data: dataScores,
                        backgroundColor: 'rgba(83, 123, 47, 0.2)', // Fern Green Transparan
                        borderColor: '#2D5128', // Cal Poly Green
                        pointBackgroundColor: '#8DA750', // Asparagus
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#2D5128',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { color: 'rgba(0, 0, 0, 0.05)' },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            pointLabels: {
                                font: { family: 'Poppins', size: 11, weight: 'bold' },
                                color: '#475569'
                            },
                            ticks: {
                                beginAtZero: true,
                                min: 0,
                                max: 5,
                                stepSize: 1,
                                display: false 
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#142C14',
                            titleFont: { family: 'Poppins' },
                            bodyFont: { family: 'Poppins', size: 14, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Nilai: ' + context.raw + ' / 5';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</x-app-layout>