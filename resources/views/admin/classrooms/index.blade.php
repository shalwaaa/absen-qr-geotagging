<x-app-layout>
    <style>
        /* 1. Animasi Standar Dashboard */
        @keyframes fadeInStandard { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-standard { animation: fadeInStandard 0.4s ease-out forwards; }

        /* 2. Style Header & Card */
        .custom-card { border-radius: 16px !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0 !important; overflow: hidden; background: white; }

        /* 3. Button */
        .btn-primary { background: #4a6741; color: white; border-radius: 12px; font-weight: 600; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 20px; height: 48px; font-size: 14px; border: none; }
        .btn-primary:hover { background: #3d5535; transform: translateY(-1px); }

        /* 4. Table Styling */
        .modern-table { width: 100%; border-collapse: collapse; }
        .modern-table thead { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .modern-table thead th { color: #64748b; font-weight: 600; text-transform: uppercase; padding: 16px; font-size: 11px; letter-spacing: 0.05em; }
        .modern-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.2s ease; }
        .modern-table tbody tr:hover { background: #f8fafc; }
        .modern-table tbody td { padding: 16px; color: #334155; font-size: 14px; }

        /* 5. Badge & Action Buttons */
        .badge-geo { display: inline-block; padding: 4px 10px; background: #f1f5f9; color: #475569; font-family: monospace; font-size: 12px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .badge-radius { display: inline-block; padding: 4px 10px; background: #ecfdf5; color: #059669; font-weight: 700; font-size: 12px; border-radius: 6px; }
        
        /* Badge Wali Kelas */
        .badge-teacher { 
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 10px; background: #fffbeb; color: #b45309; 
            font-size: 12px; font-weight: 600; border-radius: 20px; border: 1px solid #fef3c7;
        }

        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; border: 1px solid #e2e8f0; background: white; font-size: 14px; }
        .btn-edit { color: #d97706; } .btn-edit:hover { background: #fef3c7; border-color: #fbbf24; }
        .btn-detail { color: #2563eb; } .btn-detail:hover { background: #dbeafe; border-color: #60a5fa; }
        .btn-delete { color: #dc2626; } .btn-delete:hover { background: #fee2e2; border-color: #fca5a5; }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Manajemen Kelas & Lokasi</span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola area absensi dan koordinat ruang kelas</p>
            </div>
            <div class="hidden sm:block">
                <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200">
                    GEO-FENCING ACTIVE
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-standard">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex justify-end mb-6">
                <a href="{{ route('classrooms.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Kelas Baru</span>
                </a>
            </div>

            <div class="custom-card bg-white">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th class="text-left">Nama Kelas</th>
                                <th class="text-left">Wali Kelas</th> <!-- Kolom Baru -->
                                <th class="text-left">Titik Koordinat</th>
                                <th class="text-center">Radius</th>
                                <th class="text-center" style="width: 150px;">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classrooms as $index => $c)
                            <tr>
                                <td class="text-center text-slate-400 font-medium">
                                    {{ $index + 1 }}
                                </td>
                                
                                <!-- Nama Kelas -->
                                <td>
                                    <span class="font-bold text-slate-800">{{ $c->name }}</span>
                                    <div class="text-xs text-slate-400 mt-1">Tingkat {{ $c->grade_level }}</div>
                                </td>

                                <!-- Wali Kelas (Baru) -->
                                <td>
                                    @if($c->homeroomTeacher)
                                        <span class="badge-teacher">
                                            <i class="fa-solid fa-chalkboard-user"></i>
                                            {{ $c->homeroomTeacher->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum diset</span>
                                    @endif
                                </td>

                                <!-- Koordinat -->
                                <td>
                                    <span class="badge-geo">
                                        <i class="fa-solid fa-location-dot mr-1 opacity-50"></i>
                                        {{ number_format($c->latitude, 5) }}, {{ number_format($c->longitude, 5) }}
                                    </span>
                                </td>

                                <!-- Radius -->
                                <td class="text-center">
                                    <span class="badge-radius">{{ $c->radius_meters }}m</span>
                                </td>

                                <!-- Aksi -->
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('classrooms.show', $c->id) }}" class="btn-action btn-detail" title="Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('classrooms.edit', $c->id) }}" class="btn-action btn-edit" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('classrooms.destroy', $c->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus kelas ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-folder-open text-4xl mb-3 block opacity-20"></i>
                                    Belum ada data kelas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>