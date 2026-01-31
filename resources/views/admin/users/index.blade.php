<x-app-layout>
    <style>
        /* 1. Animasi Standar Dashboard */
        @keyframes fadeInStandard {
            from { 
                opacity: 0; 
                transform: translateY(10px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .animate-standard {
            animation: fadeInStandard 0.4s ease-out forwards;
        }

        /* 2. Style Header & Card */
        .header-section {
            animation: fadeInStandard 0.4s ease-out;
        }

        .custom-card {
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0 !important;
            overflow: hidden;
            background: white;
            animation: fadeInStandard 0.5s ease-out forwards;
        }

        /* 3. Search Bar */
        .search-wrapper {
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            padding: 4px;
            height: 48px;
        }

        .search-wrapper:focus-within {
            border-color: #4a6741;
            ring: 2px rgba(74, 103, 65, 0.2);
        }

        .search-input {
            border: none;
            background: transparent;
            padding-left: 2.5rem;
            width: 100%;
            font-size: 14px;
        }

        .search-input:focus { outline: none; box-shadow: none; }

        .search-icon {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
        }

        /* 4. Buttons */
        .btn-primary {
            background: #4a6741;
            color: white;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 20px;
            height: 48px;
            font-size: 14px;
            border: none;
        }

        .btn-primary:hover {
            background: #3d5535;
            transform: translateY(-1px);
        }

        /* 5. Import Zone */
        .import-zone {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            height: 48px;
            overflow: hidden;
        }

        .file-input {
            font-size: 12px;
            padding: 0 10px;
            cursor: pointer;
        }

        /* 6. Table Styling */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table thead {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table thead th {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            padding: 16px;
            font-size: 11px;
            letter-spacing: 0.05em;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background: #f1f5f9;
        }

        .modern-table tbody td {
            padding: 16px;
            color: #334155;
            font-size: 14px;
        }

        /* 7. Badges */
        .badge-id {
            display: inline-block;
            padding: 4px 12px;
            background: #f1f5f9;
            color: #475569;
            font-family: monospace;
            font-size: 12px;
            border-radius: 6px;
        }

        .badge-class {
            display: inline-block;
            padding: 4px 12px;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 600;
            font-size: 11px;
            border-radius: 6px;
        }

        /* 8. Action Buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            background: white;
        }

        .btn-edit { color: #d97706; }
        .btn-edit:hover { background: #fef3c7; }
        .btn-delete { color: #dc2626; }
        .btn-delete:hover { background: #fee2e2; }

        @media (max-width: 768px) {
            .flex-col-mobile { flex-direction: column; width: 100%; }
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">
                        {{ $type == 'teacher' ? 'Manajemen Guru' : 'Manajemen Siswa' }}
                    </span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $users->total() }} {{ $type == 'teacher' ? 'guru' : 'siswa' }} terdaftar
                </p>
            </div>
            {{-- <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full border border-gray-200">
                {{ $type == 'teacher' ? 'STAFF' : 'STUDENT' }}
            </span> --}}
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-standard">
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-6">
                
                <form method="GET" action="{{ route('users.index') }}" class="w-full lg:flex-1">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="relative search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Cari..." class="search-input">
                    </div>
                </form>

                <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="import-zone flex-1 lg:flex-none">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="file" name="file" class="file-input" required>
                        <button type="submit" class="btn-primary" style="height: 100%; border-radius: 0;">
                            <i class="fa-solid fa-upload"></i> Import
                        </button>
                    </form>

                    <a href="{{ route('users.create', ['type' => $type]) }}" class="btn-primary flex-1 lg:flex-none">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="custom-card">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th class="text-left">Nama</th>
                                <th class="text-center">{{ $type == 'teacher' ? 'NIP' : 'NIS' }}</th>
                                @if($type == 'student') <th class="text-center">Kelas</th> @endif
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $u)
                            <tr>
                                <td class="text-center text-slate-400">
                                    {{ $index + 1 + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>
                                <td class="font-medium text-slate-800">{{ $u->name }}
                                @if($u->is_piket)
                                    <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-bold px-2 py-0.5 rounded mt-1 border border-orange-200">
                                        <i class="fa-solid fa-shield-halved mr-1"></i> PIKET
                                    </span>
                                 @endif
                                </td>
                                <td class="text-center"><span class="badge-id">{{ $u->nip_nis ?? '-' }}</span></td>
                                @if($type == 'student')
                                    <td class="text-center">
                                        <span class="badge-class">{{ $u->classroom->name ?? '-' }}</span>
                                    </td>
                                @endif
                                <td class="text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('users.edit', ['user' => $u->id, 'type' => $type]) }}" class="btn-action btn-edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus data?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="type" value="{{ $type }}">
                                            <button type="submit" class="btn-action btn-delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">Data tidak ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->appends(['type' => $type, 'search' => $search])->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>