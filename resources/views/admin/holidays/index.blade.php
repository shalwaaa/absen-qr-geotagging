<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Manajemen Hari Libur</span>
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
        
        /* Layout Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 24px;
            margin-top: 20px;
        }
        
        /* Responsive Breakpoints yang Lebih Baik */
        @media (max-width: 1200px) {
            .main-grid {
                gap: 16px;
            }
            .stats-container {
                gap: 8px;
            }
        }
        
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
            .list-section {
                max-height: none;
                position: static;
            }
        }
        
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }
            .fc-toolbar-title {
                font-size: 1rem !important;
            }
            .stats-container {
                flex-direction: column;
            }
            .stat-card {
                min-width: auto;
            }
            .legend-box {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 1.4rem;
            }
            .page-desc {
                font-size: 0.85rem;
            }
            .list-header {
                padding: 15px;
                flex-direction: column;
                align-items: flex-start;
            }
            .list-header h3 {
                font-size: 1rem;
            }
            .list-container th,
            .list-container td {
                padding: 10px 8px;
                font-size: 0.75rem;
            }
            .badge-type {
                font-size: 8px;
                padding: 2px 6px;
            }
            .btn-delete {
                padding: 3px 6px;
            }
            .fc-daygrid-day-frame {
                min-height: 60px !important;
            }
            .fc-event {
                padding: 2px 4px !important;
                font-size: 0.7rem !important;
            }
        }
        
        /* Card Utama */
        .calendar-card {
            background: white; 
            border-radius: 24px; 
            border: 1px solid #e2e8f0;
            padding: 20px; 
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); 
            height: fit-content;
        }

        /* Styling FullCalendar Hijau - Diperkecil */
        .fc {
            font-size: 0.9em;
        }
        .fc-toolbar-title { 
            font-size: 1.2rem !important; 
            font-weight: 800; 
            color: var(--dark-green); 
            text-transform: uppercase; 
        }
        .fc-button { 
            padding: 4px 8px !important;
            font-size: 0.75rem !important;
        }
        .fc-button-primary { 
            background-color: var(--cal-poly) !important; 
            border-color: var(--cal-poly) !important; 
            text-transform: uppercase; 
            font-weight: 700; 
            border-radius: 8px !important;
        }
        .fc-button-primary:hover { 
            background-color: var(--fern) !important; 
            border-color: var(--fern) !important; 
        }
        .fc-button-active { 
            background-color: var(--dark-green) !important; 
        }
        .fc-day-today { 
            background-color: #f0fdf4 !important; 
        }
        
        .fc-event { 
            cursor: pointer; 
            border: none !important; 
            padding: 4px 6px !important; 
            font-size: 0.75rem !important; 
            border-radius: 6px !important; 
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            transition: 0.2s;
            margin: 1px 2px !important;
        }
        .fc-event:hover { 
            transform: scale(1.02); 
        }
        .fc-daygrid-day-frame {
            min-height: 80px !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.85rem;
            padding: 4px !important;
        }

        /* Header Info */
        .page-title { 
            font-size: 1.8rem; 
            font-weight: 900; 
            color: var(--dark-green); 
        }
        .page-desc { 
            color: #64748b; 
            font-size: 0.95rem; 
            margin-top: 4px; 
        }
        
        /* Legend */
        .legend-box {
            display: flex; 
            gap: 20px; 
            padding: 12px 20px;
            background: #f8fafc; 
            border-radius: 16px; 
            border: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }
        .legend-item { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: #475569; 
        }
        .dot { 
            width: 12px; 
            height: 12px; 
            border-radius: 50%; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Statistik Card - Lebih kecil */
        .stats-container {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 12px 16px;
            flex: 1;
            min-width: 100px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.02);
        }
        .stat-card.total { border-left: 4px solid var(--cal-poly); }
        .stat-card.nasional { border-left: 4px solid var(--cal-poly); }
        .stat-card.manual { border-left: 4px solid #d97706; }
        
        .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark-green);
            line-height: 1.2;
        }
        .stat-icon {
            font-size: 1.2rem;
            opacity: 0.2;
        }

        /* Tabel Daftar Libur - Di samping */
        .list-section {
            background: white;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            height: fit-content;
            max-height: calc(100vh - 200px);
            position: sticky;
            top: 100px;
        }
        
        @media (max-width: 1024px) {
            .list-section {
                max-height: none;
                position: static;
            }
        }
        
        .list-header {
            padding: 20px 24px;
            background: linear-gradient(to right, #f8fafc, #f0fdf4);
            border-bottom: 2px solid var(--light-green);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .list-header h3 {
            font-weight: 800;
            color: var(--dark-green);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .list-header h3 i {
            color: var(--fern);
        }
        .badge-count {
            background: var(--cal-poly);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .badge-type { 
            font-size: 10px; 
            padding: 4px 10px; 
            border-radius: 20px; 
            text-transform: uppercase; 
            font-weight: 800; 
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .badge-national { 
            background: #f0fdf4; 
            color: var(--cal-poly); 
            border: 1px solid #bbf7d0; 
        }
        .badge-manual { 
            background: #fef3e2; 
            color: #9a3412; 
            border: 1px solid #fed7aa; 
        }
        
        /* Container List dengan Scroll */
        .list-container {
            overflow-y: auto;
            max-height: 500px;
            padding: 4px;
        }
        
        @media (max-width: 768px) {
            .list-container {
                max-height: 350px;
            }
        }
        
        @media (max-width: 480px) {
            .list-container {
                max-height: 300px;
            }
        }
        
        .list-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .list-container thead {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .list-container th {
            text-align: left;
            padding: 16px 20px;
            background: white;
            color: #64748b;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }
        .list-container td {
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.85rem;
        }
        .list-container tbody tr {
            transition: 0.2s;
        }
        .list-container tbody tr:hover {
            background: #fafafa;
        }
        .date-cell {
            font-family: monospace;
            font-weight: 700;
            color: var(--cal-poly);
            white-space: nowrap;
        }
        
        /* Responsive Table di Mobile */
        @media (max-width: 640px) {
            .list-container table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .list-container th,
            .list-container td {
                padding: 12px 10px;
            }
            .date-cell {
                white-space: nowrap;
            }
        }
        
        .btn-delete {
            color: #ef4444;
            background: #fef2f2;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-delete:hover {
            background: #fee2e2;
            color: #b91c1c;
        }
        
        /* Touch-friendly untuk mobile */
        @media (max-width: 768px) {
            .btn-delete {
                padding: 8px 10px;
                font-size: 0.75rem;
            }
            .fc-event {
                min-height: 24px;
            }
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .empty-state i {
            font-size: 3rem;
            color: #e2e8f0;
            margin-bottom: 15px;
        }

        /* Style untuk form input manual */
        .input-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }
        .input-card h4 {
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--cal-poly);
            box-shadow: 0 0 0 3px rgba(45,81,40,0.1);
        }
        .btn-primary {
            background: var(--cal-poly);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            white-space: nowrap;
        }
        .btn-primary:hover {
            background: var(--fern);
            transform: translateY(-1px);
        }
        .btn-blue {
            background: #2563eb;
            color: white;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-blue:hover {
            background: #1d4ed8;
        }
    </style>

    <div class="py-10 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <!-- HEADER DENGAN LEGEND -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="page-title">📅 Kalender Akademik</h1>
                    <p class="page-desc">Informasi hari libur nasional dan agenda sekolah.</p>
                </div>

                <!-- Legend -->
                <div class="legend-box">
                    <div class="legend-item">
                        <span class="dot" style="background-color: #2D5128;"></span> Libur Nasional
                    </div>
                    <div class="legend-item">
                        <span class="dot" style="background-color: #d97706;"></span> Libur Sekolah (Manual)
                    </div>
                </div>
            </div>

            <!-- STATISTIK CARD -->
            @php
                $totalEvents = count($events);
                $nationalCount = collect($events)->where('extendedProps.type', 'national')->count();
                $manualCount = collect($events)->where('extendedProps.type', 'manual')->count();
            @endphp
            
            <div class="stats-container">
                <div class="stat-card total">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="stat-label">Total Libur</div>
                            <div class="stat-value">{{ $totalEvents }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card nasional">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="stat-label">Libur Nasional</div>
                            <div class="stat-value">{{ $nationalCount }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-regular fa-flag"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card manual">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="stat-label">Libur Manual</div>
                            <div class="stat-value">{{ $manualCount }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-regular fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM INPUT MANUAL DAN SYNC NASIONAL (BARU) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Input Manual (2 kolom) -->
                <div class="md:col-span-2 input-card">
                    <h4>
                        <i class="fa-regular fa-pen-to-square" style="color: var(--fern);"></i>
                        Tambah Libur Manual
                    </h4>
                    <form action="{{ route('holidays.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="title" placeholder="Nama libur (contoh: Libur Semester)" class="form-input" required>
                        </div>
                        <div class="flex-1">
                            <input type="date" name="date" class="form-input" required>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="description" placeholder="Keterangan (opsional)" class="form-input">
                        </div>
                        <button type="submit" class="btn-primary whitespace-nowrap">
                            <i class="fa-regular fa-calendar-plus mr-1"></i> Tambah
                        </button>
                    </form>
                </div>
                <!-- Sync Nasional (1 kolom) -->
                <div class="md:col-span-1 input-card bg-blue-50 border-blue-200">
                    <div class="flex items-center justify-between h-full">
                        <div>
                            <h4 class="text-blue-800 flex items-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                Sync Nasional
                            </h4>
                            <p class="text-xs text-blue-600 mt-1">Tarik data libur dari pemerintah</p>
                        </div>
                        <form action="{{ route('holidays.sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-blue px-4 py-2 text-sm">
                                <i class="fa-solid fa-rotate"></i> Sync
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- GRID 2 KOLOM: Kalender (kiri) + Daftar Libur (kanan) -->
            <div class="main-grid">
                
                <!-- KOLOM KIRI: KALENDER -->
                <div class="calendar-card">
                    <div id="calendar"></div>
                </div>

                <!-- KOLOM KANAN: DAFTAR LIBUR -->
                <div class="list-section">
                    <div class="list-header">
                        <h3>
                            <i class="fa-regular fa-list"></i>
                            Daftar Hari Libur
                        </h3>
                        <div class="flex gap-2">
                            <span class="badge-count">
                                <i class="fa-regular fa-calendar mr-1"></i> {{ $totalEvents }}
                            </span>
                        </div>
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
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr>
                                        <td class="date-cell">
                                            {{ \Carbon\Carbon::parse($event['start'])->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($event['start'])->isoFormat('dddd') }}
                                        </td>
                                        <td class="font-medium">
                                            <div class="max-w-[150px] md:max-w-none truncate" title="{{ $event['title'] }}">
                                                {{ $event['title'] }}
                                            </div>
                                            @if(!empty($event['description']))
                                                <div class="text-xs text-gray-400 mt-1 truncate max-w-[150px] md:max-w-none" title="{{ $event['description'] }}">
                                                    {{ $event['description'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($event['extendedProps']['type'] == 'national')
                                                <span class="badge-type badge-national">
                                                    <i class="fa-regular fa-flag"></i> NAS
                                                </span>
                                            @else
                                                <span class="badge-type badge-manual">
                                                    <i class="fa-regular fa-building"></i> MNU
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(!empty($event['id']))
                                                <form action="{{ route('holidays.destroy', $event['id']) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Hapus {{ $event['title'] }}?')"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-delete" title="Hapus">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                        <span class="hidden sm:inline">Hapus</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fa-regular fa-calendar-xmark"></i>
                            <h4 class="text-lg font-bold text-gray-400 mb-2">Belum Ada Data Libur</h4>
                            <p class="text-sm text-gray-400">Silahkan tambah data libur melalui form di atas.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Hidden Form untuk Delete (tetap ada untuk fallback) -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            // Ambil data dari Controller
            var eventsData = @json($events);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    list: 'List'
                },
                events: eventsData,
                height: 'auto',
                aspectRatio: 1.2,
                
                // Responsive calendar
                windowResize: function(view) {
                    if (window.innerWidth < 768) {
                        this.setOption('aspectRatio', 1);
                    } else {
                        this.setOption('aspectRatio', 1.2);
                    }
                },
                
                // Styling event berdasarkan tipe
                eventDidMount: function(info) {
                    if (info.event.extendedProps.type === 'manual') {
                        info.el.style.backgroundColor = '#d97706';
                        info.el.style.borderColor = '#d97706';
                    } else {
                        info.el.style.backgroundColor = '#2D5128';
                        info.el.style.borderColor = '#2D5128';
                    }
                    
                    // Tambah icon
                    var icon = document.createElement('i');
                    icon.className = info.event.extendedProps.type === 'manual' 
                        ? 'fa-regular fa-building mr-1' 
                        : 'fa-regular fa-flag mr-1';
                    icon.style.fontSize = '10px';
                    info.el.insertBefore(icon, info.el.firstChild);
                },
                
                // SAAT ITEM DIKLIK -> TAMPILKAN DETAIL (POPUP) DENGAN TOMBOL HAPUS
                eventClick: function(info) {
                    var event = info.event;
                    var props = event.extendedProps;
                    
                    var tanggal = event.start.toLocaleDateString('id-ID', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    var typeText = props.type === 'manual' ? 'Libur Sekolah (Manual)' : 'Libur Nasional';
                    var eventId = event.id; // ID event harus ada di data

                    Swal.fire({
                        title: event.title,
                        html: `
                            <div class="text-left mt-4">
                                <p class="mb-3">
                                    <span class="font-bold text-gray-500">Tanggal:</span><br>
                                    <span class="text-gray-700">${tanggal}</span>
                                </p>
                                <p class="mb-3">
                                    <span class="font-bold text-gray-500">Tipe:</span><br>
                                    <span class="badge-type ${props.type === 'manual' ? 'badge-manual' : 'badge-national'}">${typeText}</span>
                                </p>
                                <p>
                                    <span class="font-bold text-gray-500">Keterangan:</span><br>
                                    <span class="text-gray-700">${props.description || '-'}</span>
                                </p>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#2D5128',
                        confirmButtonText: '<i class="fa-regular fa-trash-can mr-1"></i> Hapus',
                        cancelButtonText: 'Tutup',
                        reverseButtons: true,
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return fetch(`/holidays/${eventId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Gagal menghapus data');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    return data;
                                } else {
                                    throw new Error(data.message || 'Gagal menghapus');
                                }
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: result.value.message || 'Libur berhasil dihapus.',
                                icon: 'success',
                                confirmButtonColor: '#2D5128'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });

            calendar.render();
            
            // Trigger window resize untuk set initial aspect ratio
            window.dispatchEvent(new Event('resize'));
        });
    </script>
</x-app-layout>