<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Manajemen Tahun Ajar</span>
                </h2>
            </div>
        </div>
    </x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly: #2D5128;
            --fern: #537B2F;
            --mindaro: #E4EB9C;
            --cream-bg: #FDFDF9;
        }

        /* Card Utama */
        .card-custom {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: 0 12px 25px -5px rgba(45, 81, 40, 0.08);
        }

        /* Tabel Modern */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table thead {
            background: #f8fafc;
            border-bottom: 2px solid var(--mindaro);
        }

        .modern-table th {
            color: #64748b;
            padding: 16px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modern-table td {
            padding: 16px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .modern-table tbody tr:hover {
            background: #fcfdfa;
        }

        /* Badge Status */
        .badge-active {
            background: #dcfce7;
            color: #166534;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 800;
            border: 1px solid #bbf7d0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .badge-inactive {
            background: #f1f5f9;
            color: #64748b;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        /* Tombol Set Aktif */
        .btn-set {
            background: white;
            color: var(--cal-poly);
            border: 2px solid var(--cal-poly);
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-set:hover {
            background: var(--cal-poly);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(45, 81, 40, 0.2);
        }

        /* Pagination Custom */
        .pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 24px;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            background: #f8fafc;
            border-color: var(--cal-poly);
            color: var(--cal-poly);
            transform: translateY(-1px);
        }

        .pagination .active .page-link {
            background: var(--cal-poly);
            border-color: var(--cal-poly);
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(45, 81, 40, 0.2);
        }

        .pagination .disabled .page-link {
            opacity: 0.5;
            pointer-events: none;
            background: #f1f5f9;
        }

        /* Responsif */
        @media (max-width: 640px) {
            .modern-table th,
            .modern-table td {
                padding: 12px 10px;
                font-size: 0.8rem;
            }

            .badge-active,
            .badge-inactive {
                padding: 4px 10px;
                font-size: 0.7rem;
            }

            .btn-set {
                padding: 6px 12px;
                font-size: 0.7rem;
            }

            .pagination .page-link {
                min-width: 34px;
                height: 34px;
                font-size: 0.8rem;
            }
        }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-4xl mx-auto">

            <!-- Notifikasi Sukses -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl text-sm font-bold border border-green-200 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Card Utama -->
            <div class="card-custom">
                <div class="overflow-x-auto">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="100">Status</th>
                                <th>Tahun Ajaran</th>
                                <th>Periode Tanggal</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($years as $y)
                                <tr>
                                    <td>
                                        @if($y->is_active)
                                            <span class="badge-active">
                                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> AKTIF
                                            </span>
                                        @else
                                            <span class="badge-inactive">
                                                <i class="fa-regular fa-clock"></i> Arsip
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-bold text-gray-800">{{ $y->name }}</span>
                                    </td>
                                    <td class="text-sm text-gray-500 font-mono">
                                        {{ \Carbon\Carbon::parse($y->start_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($y->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if(!$y->is_active)
                                            <form action="{{ route('academic-years.set-active', $y->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-set">
                                                    <i class="fa-regular fa-circle-check"></i> Jadikan Aktif
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-gray-400">
                                        <i class="fa-regular fa-calendar-xmark text-4xl mb-3 block opacity-30"></i>
                                        <p>Belum ada data tahun ajaran.</p>
                                        <p class="text-xs mt-1">Lakukan Sync API untuk mengenerate tahun.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($years->hasPages())
                    <div class="mt-6">
                        {{ $years->links() }}
                    </div>
                @endif
            </div>

            <!-- Informasi Penting -->
            <div class="mt-6 p-5 bg-yellow-50/80 rounded-xl border border-yellow-200 text-sm text-yellow-800 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-yellow-600 mt-0.5"></i>
                <div>
                    <strong class="font-bold">Penting:</strong> Tahun Ajaran Aktif digunakan sebagai acuan untuk mencatat Penilaian Karakter Siswa dan Laporan lainnya. Hanya boleh ada 1 tahun yang aktif.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>