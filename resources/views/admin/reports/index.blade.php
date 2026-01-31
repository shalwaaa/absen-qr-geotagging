<x-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --cream:      #FAFAF5;
        }

        .card-filter {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0fdf4;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-label {
            display: block;
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--fern);
            box-shadow: 0 0 0 4px rgba(83, 123, 47, 0.1);
        }

        .btn-print {
            background: linear-gradient(135deg, var(--cal-poly), var(--fern));
            color: white;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(45, 81, 40, 0.3);
        }
    </style>

    <div class="py-12 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-[#142C14]">Laporan Kehadiran</h1>
            <p class="text-gray-500 mt-1">Unduh rekap presensi kelas dalam format PDF.</p>
        </div>

        <div class="card-filter">
            <!-- TARGET BLANK AGAR PDF BUKA DI TAB BARU -->
            <form action="{{ route('reports.generate') }}" method="POST" target="_blank">
                @csrf

                <!-- PILIH KELAS -->
                <div class="mb-6">
                    <label class="form-label">Pilih Kelas</label>
                    <select name="classroom_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <!-- Perhatikan: variable $classrooms (jamak) -->
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PILIH TANGGAL -->
                <div class="grid grid-cols-2 gap-4 mb-2">
                    <div>
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-input" required>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-6">*Pilih rentang waktu laporan (Misal: 1 Bulan).</p>

                <!-- TOMBOL CETAK -->
                <button type="submit" class="btn-print">
                    <i class="fa-solid fa-file-pdf text-xl"></i> CETAK LAPORAN PDF
                </button>
            </form>
        </div>

    </div>
</x-app-layout>