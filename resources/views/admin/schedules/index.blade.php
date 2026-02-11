<x-app-layout>
    <style>
        /* TAB FOLDER (TINGKAT) */
        .grade-tabs {
            display: flex; gap: 8px; margin-bottom: 12px; overflow-x: auto; padding-bottom: 4px;
        }
        .grade-tab {
            padding: 10px 24px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .grade-tab:hover { background: #f8fafc; color: #1e293b; }
        .grade-tab.active {
            background: #4a6741; color: white; border-color: #4a6741;
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* TAB HARI (STYLE BEDA DIKIT) */
        .day-tabs {
            display: flex; gap: 6px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 4px;
        }
        .day-tab {
            padding: 6px 16px;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .day-tab:hover { background: #e2e8f0; }
        .day-tab.active {
            background: #dcfce7; color: #166534; border-color: #bbf7d0;
        }

        /* Search Bar */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 16px; flex-wrap: wrap; }
        .search-box { position: relative; flex: 1; max-width: 300px; }
        .search-input { width: 100%; padding-left: 36px; border-radius: 10px; border: 1px solid #e2e8f0; height: 42px; outline: none; }
        .search-input:focus { border-color: #4a6741; }
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }

        /* ... Sisa style lama ... */
        .custom-card { border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; background: white; }
        .btn-primary { background: #4a6741; color: white; border-radius: 12px; font-weight: 600; padding: 0 20px; height: 42px; font-size: 14px; border: none; display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-primary:hover { background: #3d5535; transform: translateY(-1px); }
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table th { color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px; font-size: 11px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .modern-table td { padding: 16px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .time-badge { background: #f0fdf4; color: #166534; padding: 4px 8px; border-radius: 8px; font-weight: 600; font-size: 13px; border: 1px solid #dcfce7; }
        .classroom-badge { display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #dbeafe; }
        .btn-action { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: white; margin-right: 4px; }
        .btn-edit:hover { background: #fef3c7; border-color: #fbbf24; color: #d97706; }
        .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Manajemen Jadwal Pelajaran</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- TOOLBAR: SEARCH & ADD -->
            <div class="toolbar">
                <form method="GET" action="{{ route('schedules.index') }}" class="search-box">
                    @if($grade) <input type="hidden" name="grade" value="{{ $grade }}"> @endif
                    @if($day) <input type="hidden" name="day" value="{{ $day }}"> @endif
                    
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Guru / Mapel / Kelas..." class="search-input">
                </form>

                <a href="{{ route('schedules.create') }}" class="btn-primary shadow-sm">
                    <i class="fa-solid fa-calendar-plus text-xs"></i>
                    <span>Buat Jadwal</span>
                </a>
            </div>

            <!-- FILTER LEVEL 1: TINGKAT KELAS -->
            <div class="grade-tabs">
                <a href="{{ route('schedules.index', ['day' => $day]) }}" 
                   class="grade-tab {{ !$grade ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Semua Tingkat
                </a>
                <a href="{{ route('schedules.index', ['grade' => 10, 'day' => $day]) }}" 
                   class="grade-tab {{ $grade == 10 ? 'active' : '' }}">
                    <i class="fa-solid fa-folder"></i> Kelas 10
                </a>
                <a href="{{ route('schedules.index', ['grade' => 11, 'day' => $day]) }}" 
                   class="grade-tab {{ $grade == 11 ? 'active' : '' }}">
                    <i class="fa-solid fa-folder"></i> Kelas 11
                </a>
                <a href="{{ route('schedules.index', ['grade' => 12, 'day' => $day]) }}" 
                   class="grade-tab {{ $grade == 12 ? 'active' : '' }}">
                    <i class="fa-solid fa-folder"></i> Kelas 12
                </a>
            </div>

            <!-- FILTER LEVEL 2: HARI -->
            <div class="day-tabs">
                <a href="{{ route('schedules.index', ['grade' => $grade]) }}" 
                   class="day-tab {{ !$day ? 'active' : '' }}">
                   Semua Hari
                </a>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $d)
                    <a href="{{ route('schedules.index', ['grade' => $grade, 'day' => $d]) }}" 
                       class="day-tab {{ $day == $d ? 'active' : '' }}">
                        {{ $d }}
                    </a>
                @endforeach
            </div>

            <div class="custom-card">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" width="100">Hari</th>
                                <th class="text-center" width="140">Waktu</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengajar</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schedules as $s)
                            <tr>
                                <td class="text-center font-bold text-[#4a6741] uppercase text-xs">
                                    {{ $s->day }}
                                </td>
                                <td class="text-center">
                                    <span class="time-badge">
                                        {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-700">{{ $s->subject->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-1">{{ $s->subject->code ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                            {{ substr($s->teacher->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium">{{ $s->teacher->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="classroom-badge">
                                        {{ $s->classroom->name }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('schedules.edit', $s->id) }}" class="btn-action btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <form action="{{ route('schedules.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center text-slate-400">
                                    <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
                                    <p>Tidak ada jadwal pelajaran.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($schedules->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $schedules->appends(['grade' => $grade, 'day' => $day, 'search' => $search])->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>