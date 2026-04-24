<x-app-layout>
    <x-slot name="header">
        <div class="header-section">
            <h2 class="font-semibold text-xl leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Analitik Helpdesk</span>
            </h2>
            <p class="text-xs" style="color: #E4EB9C; opacity: 0.8; margin-top: 4px;">
                Pantau performa layanan dan statistik aduan secara real-time.
            </p>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --mindaro: #E4EB9C; }

        /* PAKSA GRID 4 KOLOM */
        .grid-statistik-custom {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Paksa 4 kolom sama rata */
            gap: 20px;
            width: 100%;
            margin-bottom: 30px;
        }

        /* Responsive: Jika layar kecil, jadi 2 kolom atau 1 kolom */
        @media (max-width: 1024px) { .grid-statistik-custom { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .grid-statistik-custom { grid-template-columns: 1fr; } }

        .welcome-banner-new {
            background: var(--cal-poly);
            border-radius: 25px;
            padding: 35px;
            color: white;
            margin-bottom: 25px;
            width: 100%;
        }

        .card-stat-new {
            background: white;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            min-height: 110px;
        }

        .icon-square {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 1.2rem;
        }

        .label-mini { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .value-bold { font-size: 24px; font-weight: 900; color: var(--dark-green); line-height: 1; margin-top: 4px; }
    </style>

    <div class="py-8 px-6 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-[1400px] mx-auto">


            <div class="grid-statistik-custom">
                <div class="card-stat-new">
                    <div class="icon-square bg-gray-50 text-gray-400"><i class="fa-solid fa-layer-group"></i></div>
                    <div><p class="label-mini">Total Tiket</p><h3 class="value-bold">{{ $countTotal }}</h3></div>
                </div>
                <div class="card-stat-new">
                    <div class="icon-square bg-[#fef3c7] text-[#b45309]"><i class="fa-solid fa-envelope-open"></i></div>
                    <div><p class="label-mini">Open</p><h3 class="value-bold text-yellow-600">{{ $countOpen }}</h3></div>
                </div>
                <div class="card-stat-new">
                    <div class="icon-square bg-[#dbeafe] text-[#1d4ed8]"><i class="fa-solid fa-spinner"></i></div>
                    <div><p class="label-mini">In Progress</p><h3 class="value-bold text-blue-600">{{ $countProgress }}</h3></div>
                </div>
                <div class="card-stat-new">
                    <div class="icon-square bg-[#dcfce7] text-[#166534]"><i class="fa-solid fa-check-double"></i></div>
                    <div><p class="label-mini">Resolved</p><h3 class="value-bold text-green-700">{{ $countClosed }}</h3></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
                <div class="lg:col-span-1 bg-white p-6 rounded-[25px] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-[var(--dark-green)] mb-6 uppercase text-xs tracking-widest">Kategori Aduan</h4>
                    <div class="h-[280px]"><canvas id="categoryChart"></canvas></div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-[25px] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/30 font-bold text-[var(--dark-green)]">🏆 Performa Operator</div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[10px] text-gray-400 uppercase font-black">
                                <tr>
                                    <th class="px-6 py-4">Operator</th>
                                    <th class="px-6 py-4 text-center">Progress</th>
                                    <th class="px-6 py-4 text-right">Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($operatorStats as $op)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-5 font-bold text-gray-700">{{ $op->name }}</td>
                                    <td class="px-6 py-5">
                                        <div class="w-full bg-gray-100 h-1.5 rounded-full">
                                            <div class="bg-[var(--cal-poly)] h-1.5 rounded-full" style="width: {{ $countTotal > 0 ? ($op->resolved_count/$countTotal)*100 : 0 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right font-black text-xl text-[var(--dark-green)]">{{ $op->resolved_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryStats->pluck('category')) !!},
                    datasets: [{
                        data: {!! json_encode($categoryStats->pluck('count')) !!},
                        backgroundColor: ['#2D5128', '#537B2F', '#8DA750', '#E4EB9C', '#142C14'],
                        borderWidth: 5, borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } } }
                }
            });
        });
    </script>
</x-app-layout>
