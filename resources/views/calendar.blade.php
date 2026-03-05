<x-app-layout>
        <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Calender</span>
            </h2>
        </div>
    </x-slot>

    <!-- Load FullCalendar & SweetAlert -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { 
            --dark-green: #142C14; 
            --cal-poly: #2D5128; 
            --fern: #537B2F; 
            --light-green: #E4EB9C;
            --cream: #FAFAF5; 
        }
        
        /* Layout */
        .page-title { font-size: 2rem; font-weight: 900; color: var(--dark-green); }
        .page-desc { color: #64748b; }
        
        /* Legend */
        .legend-box {
            display: flex; gap: 20px; padding: 12px 20px;
            background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;
        }
        .legend-item { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #475569; }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        
        /* Statistik Card */
        .stats-container {
            display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;
        }
        .stat-card {
            background: white; border-radius: 16px; padding: 16px 20px; flex: 1; min-width: 120px;
            border: 1px solid #e2e8f0; border-left: 4px solid var(--cal-poly);
        }
        .stat-label { font-size: 0.8rem; text-transform: uppercase; color: #64748b; }
        .stat-value { font-size: 2rem; font-weight: 800; color: var(--dark-green); line-height: 1.2; }
        
        /* Grid utama */
        .main-grid {
            display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; margin-top: 20px;
        }
        @media (max-width: 1024px) {
            .main-grid { grid-template-columns: 1fr; }
        }
        
        /* Card kalender */
        .calendar-card {
            background: white; border-radius: 24px; border: 1px solid #e2e8f0;
            padding: 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.02);
        }
        
        /* FullCalendar styling */
        .fc { font-size: 0.9em; }
        .fc-toolbar-title { font-size: 1.2rem !important; font-weight: 800; color: var(--dark-green); }
        .fc-button-primary { background-color: var(--cal-poly) !important; border-color: var(--cal-poly) !important; border-radius: 10px !important; }
        .fc-button-primary:hover { background-color: var(--fern) !important; }
        .fc-day-today { background-color: #f0fdf4 !important; }
        .fc-event { 
            cursor: pointer; border: none; padding: 4px 6px; font-size: 0.8rem; border-radius: 6px;
            background-color: var(--cal-poly); color: white; transition: transform 0.2s;
        }
        .fc-event:hover { transform: scale(1.02); }
        
        /* Daftar libur */
        .list-section {
            background: white; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden;
            display: flex; flex-direction: column; max-height: 600px;
        }
        .list-header {
            padding: 20px; background: linear-gradient(to right, #f8fafc, #f0fdf4);
            border-bottom: 2px solid var(--light-green);
        }
        .list-header h3 { font-weight: 800; color: var(--dark-green); font-size: 1.2rem; }
        .badge-count {
            background: var(--cal-poly); color: white; padding: 4px 12px; border-radius: 20px;
            font-size: 0.8rem; font-weight: 700;
        }
        .badge-type { font-size: 10px; padding: 4px 8px; border-radius: 12px; font-weight: 700; }
        .badge-national { background: #f0fdf4; color: var(--cal-poly); border: 1px solid #bbf7d0; }
        .badge-manual { background: #fef3e2; color: #9a3412; border: 1px solid #fed7aa; }
        
        .list-container {
            overflow-y: auto; flex: 1;
        }
        .list-container table { width: 100%; border-collapse: collapse; }
        .list-container th {
            position: sticky; top: 0; background: white; padding: 16px 20px;
            color: #64748b; font-weight: 700; font-size: 0.7rem; text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }
        .list-container td { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .date-cell { font-family: monospace; font-weight: 700; color: var(--cal-poly); }
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
        
        /* Responsif */
        @media (max-width: 768px) {
            .stats-container { flex-direction: column; }
            .list-container th, .list-container td { padding: 10px; }
        }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <!-- Header dengan judul dan legend -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div class="legend-box">
                    <div class="legend-item">
                        <span class="dot" style="background-color: #2D5128;"></span> Libur Nasional
                    </div>
                    <div class="legend-item">
                        <span class="dot" style="background-color: #d97706;"></span> Libur Manual
                    </div>
                </div>
            </div>

            @php
                $totalEvents = count($events);
                $nationalCount = collect($events)->where('extendedProps.type', 'national')->count();
                $manualCount = collect($events)->where('extendedProps.type', 'manual')->count();
            @endphp

            <!-- Statistik card -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-label">Total Libur</div>
                    <div class="stat-value">{{ $totalEvents }}</div>
                </div>
                <div class="stat-card" style="border-left-color: #2D5128;">
                    <div class="stat-label">Libur Nasional</div>
                    <div class="stat-value">{{ $nationalCount }}</div>
                </div>
                <div class="stat-card" style="border-left-color: #d97706;">
                    <div class="stat-label">Libur Manual</div>
                    <div class="stat-value">{{ $manualCount }}</div>
                </div>
            </div>

            <!-- Grid 2 kolom -->
            <div class="main-grid">
                <!-- Kolom kiri: kalender -->
                <div class="calendar-card">
                    <div id="calendar"></div>
                </div>

                <!-- Kolom kanan: daftar libur -->
                <div class="list-section">
                    <div class="list-header flex justify-between items-center">
                        <h3><i class="fa-regular fa-list mr-2"></i>Daftar Hari Libur</h3>
                        <span class="badge-count">{{ $totalEvents }} Data</span>
                    </div>
                    @if($totalEvents > 0)
                        <div class="list-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Judul</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr>
                                        <td class="date-cell">{{ \Carbon\Carbon::parse($event['start'])->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($event['start'])->isoFormat('dddd') }}</td>
                                        <td>
                                            <div class="font-medium">{{ $event['title'] }}</div>
                                            @if(!empty($event['description']))
                                                <div class="text-xs text-gray-400">{{ $event['description'] }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($event['extendedProps']['type'] == 'national')
                                                <span class="badge-type badge-national"><i class="fa-regular fa-flag mr-1"></i>NAS</span>
                                            @else
                                                <span class="badge-type badge-manual"><i class="fa-regular fa-building mr-1"></i>MNU</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-regular fa-calendar-xmark text-5xl mb-3"></i>
                            <p class="font-bold">Belum ada data libur</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventsData = @json($events);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                buttonText: { today: 'Hari Ini', month: 'Bulan', list: 'List' },
                events: eventsData,
                eventDidMount: function(info) {
                    // Warna sesuai tipe
                    if (info.event.extendedProps.type === 'manual') {
                        info.el.style.backgroundColor = '#d97706';
                    } else {
                        info.el.style.backgroundColor = '#2D5128';
                    }
                    // Tambahkan ikon
                    var icon = document.createElement('i');
                    icon.className = info.event.extendedProps.type === 'manual' 
                        ? 'fa-regular fa-building mr-1' 
                        : 'fa-regular fa-flag mr-1';
                    icon.style.fontSize = '10px';
                    info.el.insertBefore(icon, info.el.firstChild);
                },
                eventClick: function(info) {
                    var event = info.event;
                    var props = event.extendedProps;
                    var tanggal = event.start.toLocaleDateString('id-ID', { 
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                    });
                    Swal.fire({
                        title: event.title,
                        html: `
                            <div class="text-left mt-4">
                                <p class="mb-2"><span class="font-bold text-gray-500">Tanggal:</span> <br> ${tanggal}</p>
                                <p class="mb-2"><span class="font-bold text-gray-500">Tipe:</span> <br> ${props.type === 'manual' ? 'Libur Manual' : 'Libur Nasional'}</p>
                                <p><span class="font-bold text-gray-500">Keterangan:</span> <br> ${props.description || '-'}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonColor: '#2D5128',
                        confirmButtonText: 'Tutup'
                    });
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>