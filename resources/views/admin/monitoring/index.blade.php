<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    <span class=" font-extrabold tracking-tight" style="color: #E4EB9C">Monitoring Guru</span>
                </h2>
                <p class="text-gray-500 text-sm mt-1" style="color: #E4EB9C">
                    Pemantauan KBM Real-time ({{ $today }})
                </p>
            </div>
        </div>
    </x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --medium-green: #2D5128;
            --light-green: #537B2F;
            --cream: #E4EB9C;
            --bg-cream: #FDFDF9;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .search-container {
            display: flex;
            gap: 1rem;
            align-items: center;
            width: 100%;
            max-width: 100%;
        }

        /* IMPROVED SEARCH BAR */
        .search-wrapper {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box {
            width: 100%;
            height: 48px;
            padding: 0 52px 0 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            color: #334155;
            background: white;
            transition: all 0.3s ease;
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            font-weight: 500;
        }

        .search-box:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 4px rgba(83, 123, 47, 0.15);
        }

        .search-box::placeholder {
            color: #94a3b8;
            font-weight: normal;
        }

        .search-btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 48px;
            width: 48px;
            background: var(--medium-green);
            border: none;
            border-radius: 0 12px 12px 0;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: var(--light-green);
        }

        .search-clear {
            position: absolute;
            right: 56px;
            top: 50%;
            transform: translateY(-50%);
            background: #f1f5f9;
            border: none;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            color: #64748b;
            transition: all 0.2s ease;
            opacity: 0.8;
        }

        .search-clear:hover {
            background: #e2e8f0;
            color: #475569;
            opacity: 1;
        }

        .search-stats {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
            padding-left: 4px;
        }

        .search-count {
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 600;
            color: var(--medium-green);
        }

        /* IMPROVED MONITOR CARD */
        .monitor-card {
            background: white;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .monitor-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        }

        .table-monitor {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-monitor th {
            text-align: left;
            padding: 18px 24px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .table-monitor td {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }

        .table-monitor tbody tr:hover {
            background-color: #fafafa;
        }

        /* Baris Urgent */
        .row-urgent {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .row-urgent:hover {
            background-color: #fee2e2;
        }

        /* IMPROVED BADGE STATUS */
        .badge-status {
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
            display: inline-block;
            min-width: 110px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .badge-hadir { background-color: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .badge-piket { background-color: #fef9c3; color: #854d0e; border-color: #fef08a; }
        .badge-belum { background-color: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
        .badge-terlambat { background-color: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .badge-menunggu { background-color: #f3f4f6; color: #4b5563; border-color: #e5e7eb; }

        /* IMPROVED BUTTONS */
        .btn-refresh {
            background: var(--medium-green);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            height: 48px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(45, 81, 40, 0.1);
        }

        .btn-refresh:hover {
            background: var(--light-green);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 81, 40, 0.15);
        }

        .btn-panggil {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 2px solid;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-panggil-piket {
            background: white;
            color: #64748b;
            border-color: #e2e8f0;
        }

        .btn-panggil-piket:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .btn-batal-panggil {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
        }

        .btn-batal-panggil:hover {
            background: #b91c1c;
            border-color: #b91c1c;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        /* IMPROVED TIME DISPLAY */
        .jam-pelajaran {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--medium-green);
            background: #f0fdf4;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
            border: 1px solid #dcfce7;
        }

        /* IMPROVED LEGEND */
        .legend-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 2rem;
            padding: 20px;
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: #f8fafc;
            transition: background-color 0.2s ease;
        }

        .legend-item:hover {
            background: #f1f5f9;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .legend-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        /* AVATAR IMPROVEMENT */
        .teacher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--medium-green), var(--light-green));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* CLASS BADGE */
        .class-badge {
            background: #f8fafc;
            color: #475569;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            margin-top: 6px;
            display: inline-block;
        }

        /* NO DATA STATE */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 16px;
            color: #e2e8f0;
        }

        .no-data-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #64748b;
        }

        .no-data-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1024px) {
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-wrapper {
                min-width: 100%;
            }
            
            .btn-refresh {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }
            
            .table-monitor th,
            .table-monitor td {
                padding: 14px 16px;
                font-size: 0.85rem;
            }
            
            .badge-status {
                min-width: 90px;
                padding: 6px 12px;
                font-size: 0.7rem;
            }
            
            .jam-pelajaran {
                font-size: 0.85rem;
                padding: 4px 10px;
            }
            
            .legend-container {
                grid-template-columns: 1fr;
            }
            
            .teacher-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .search-box {
                height: 44px;
                font-size: 14px;
                padding: 0 48px 0 16px;
            }
            
            .search-btn {
                height: 44px;
                width: 44px;
            }
            
            .search-clear {
                right: 48px;
            }
            
            .btn-refresh {
                height: 44px;
                font-size: 13px;
                padding: 10px 20px;
            }
            
            .table-monitor {
                display: block;
                overflow-x: auto;
            }
        }
    </style>

    <div class="py-10 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <!-- HEADER & SEARCH -->
            <div class="page-header">
                <div class="search-container">
                    <!-- SEARCH BAR -->
                    <form method="GET" action="{{ route('monitoring.index') }}" class="search-wrapper" id="searchForm">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari guru, mata pelajaran, atau kelas..." 
                            class="search-box"
                            id="searchInput"
                        >
                        
                        @if(request('search'))
                            <button 
                                type="button" 
                                class="search-clear"
                                onclick="clearSearch()"
                                title="Hapus pencarian"
                            >
                                ✕
                            </button>
                        @endif
                        
                        <button type="submit" class="search-btn" title="Cari">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                    
                    <!-- REFRESH BUTTON -->
                    <button onclick="window.location.reload()" class="btn-refresh">
                        <i class="fa-solid fa-rotate-right"></i> Refresh Data
                    </button>
                </div>
                
                <!-- SEARCH STATS -->
                @if(request('search'))
                    <div class="search-stats">
                        <span>
                            Hasil pencarian untuk "<span class="font-medium text-[var(--medium-green)]">{{ request('search') }}</span>"
                        </span>
                        <span class="search-count">
                            {{ $monitoringData->count() }} data ditemukan
                        </span>
                    </div>
                @endif
            </div>

            <!-- MONITORING TABLE -->
            <div class="monitor-card">
                <div class="overflow-x-auto">
                    <table class="table-monitor">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Waktu</th>
                                <th style="width: 25%;">Guru Pengajar</th>
                                <th style="width: 25%;">Mapel & Kelas</th>
                                <th style="width: 15%;" class="text-center">Status</th>
                                <th style="width: 20%;">Keterangan / Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monitoringData as $data)
                                <tr class="{{ $data->is_urgent ? 'row-urgent' : '' }}">
                                    
                                    <!-- WAKTU -->
                                    <td>
                                        <div class="jam-pelajaran">
                                            {{ \Carbon\Carbon::parse($data->schedule->start_time)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($data->schedule->end_time)->format('H:i') }}
                                        </div>
                                    </td>

                                    <!-- GURU -->
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="teacher-avatar">
                                                {{ substr($data->schedule->teacher->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 {{ $data->is_urgent ? 'text-red-700' : '' }}">
                                                    {{ $data->schedule->teacher->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    NIP: {{ $data->schedule->teacher->nip_nis ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- MAPEL & KELAS -->
                                    <td>
                                        <div class="font-bold text-gray-800 mb-1">
                                            {{ $data->schedule->subject->name }}
                                        </div>
                                        <div class="class-badge">
                                            {{ $data->schedule->classroom->name }}
                                        </div>
                                    </td>

                                    <!-- STATUS -->
                                    <td class="text-center">
                                        <span class="badge-status {{ $data->badge_class }}">
                                            {{ $data->status }}
                                        </span>
                                    </td>

                                    <!-- AKSI -->
                                    <td>
                                        @if($data->status == 'Terlambat' || $data->status == 'Belum Masuk')
                                            <form action="{{ route('monitoring.panggil', $data->schedule->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @if($data->schedule->request_piket)
                                                    <button type="submit" class="btn-panggil btn-batal-panggil">
                                                        <i class="fa-solid fa-bell-slash"></i> BATALKAN
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn-panggil btn-panggil-piket">
                                                        <i class="fa-solid fa-bell"></i> PANGGIL PIKET
                                                    </button>
                                                @endif
                                            </form>
                                        @elseif($data->is_urgent)
                                            <span class="text-xs font-bold text-red-600">
                                                {{ $data->keterangan }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">
                                                {{ $data->keterangan }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="no-data">
                                            <div class="no-data-icon">
                                                <i class="fa-solid fa-clipboard-list"></i>
                                            </div>
                                            @if(request('search'))
                                                <div class="no-data-title">
                                                    Tidak ditemukan data
                                                </div>
                                                <div class="no-data-subtitle">
                                                    Tidak ada data yang cocok dengan "<span class="font-medium">{{ request('search') }}</span>"
                                                </div>
                                                <button onclick="clearSearch()" class="btn-refresh" style="width: auto; margin-top: 16px;">
                                                    <i class="fa-solid fa-xmark mr-1"></i> Hapus Pencarian
                                                </button>
                                            @else
                                                <div class="no-data-title">
                                                    Tidak ada jadwal hari ini
                                                </div>
                                                <div class="no-data-subtitle">
                                                    Tidak ada jadwal pelajaran yang dijadwalkan untuk hari ini.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- LEGEND -->
            <div class="legend-container">
                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #22c55e;"></span>
                    <span class="legend-text">Hadir Mengajar</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #eab308;"></span>
                    <span class="legend-text">Digantikan Piket</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #3b82f6;"></span>
                    <span class="legend-text">Belum Masuk (Wajar)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot animate-pulse" style="background-color: #ef4444;"></span>
                    <span class="legend-text">Terlambat (>15 Menit)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background-color: #9ca3af;"></span>
                    <span class="legend-text">Belum Waktunya</span>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            
            if (searchInput) {
                let searchTimeout;
                
                // Auto submit dengan debounce
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    
                    searchTimeout = setTimeout(function() {
                        searchForm.submit();
                    }, 500);
                });
                
                // Enter untuk submit
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchForm.submit();
                    }
                });
                
                // Auto focus
                searchInput.focus();
                searchInput.select();
                
                // Highlight search text
                if (searchInput.value) {
                    searchInput.select();
                }
            }
            
            // Add animation to urgent rows
            const urgentRows = document.querySelectorAll('.row-urgent');
            urgentRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(4px)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
        
        function clearSearch() {
            const url = new URL(window.location.href);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        }
    </script>
</x-app-layout>