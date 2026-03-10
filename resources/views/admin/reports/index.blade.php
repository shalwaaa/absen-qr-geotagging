<x-app-layout>
        <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Laporan & Rekapitulasi</span>
            </h2>
        </div>
    </x-slot>

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --fern: #537B2F; }
        .card-filter {
            background: white; border-radius: 20px; padding: 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #f0fdf4;
            max-width: 600px; margin: 0 auto;
        }
        .form-label { display: block; font-weight: 700; color: var(--dark-green); margin-bottom: 8px; font-size: 0.9rem; }
        .form-input, .form-select { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; outline: none; }
        .form-input:focus, .form-select:focus { border-color: var(--fern); }
        .btn-print { background: linear-gradient(135deg, var(--cal-poly), var(--fern)); color: white; width: 100%; padding: 14px; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(45, 81, 40, 0.3); }
        
        /* Radio Button Style */
        .type-selector { display: flex; gap: 15px; margin-bottom: 20px; }
        .type-label { flex: 1; cursor: pointer; }
        .type-radio { display: none; }
        .type-box { 
            border: 2px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; font-weight: bold; color: #64748b; transition: 0.2s;
        }
        .type-radio:checked + .type-box {
            border-color: var(--fern); background: #f0fdf4; color: var(--dark-green);
        }
    </style>

    <div class="py-12 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-[#142C14]">Laporan & Rekapitulasi</h1>
            <p class="text-gray-500 mt-1">Cetak laporan kehadiran siswa atau aktivitas mengajar guru.</p>
        </div>

        <div class="card-filter">
            <form action="{{ route('reports.generate') }}" method="POST" target="_blank">
                @csrf

                <!-- PILIH TIPE -->
                <div class="mb-6">
                    <label class="form-label">Jenis Laporan</label>
                    <div class="type-selector">
                        <label class="type-label">
                            <input type="radio" name="type" value="student" class="type-radio" checked onchange="toggleForm('student')">
                            <div class="type-box"><i class="fa-solid fa-users"></i> Laporan Siswa</div>
                        </label>
                        <label class="type-label">
                            <input type="radio" name="type" value="teacher" class="type-radio" onchange="toggleForm('teacher')">
                            <div class="type-box"><i class="fa-solid fa-chalkboard-user"></i> Laporan Guru</div>
                        </label>
                    </div>
                </div>

                <!-- PILIH KELAS (Hanya Muncul Jika Siswa) -->
                <div class="mb-6" id="classroom-wrapper">
                    <label class="form-label">Pilih Kelas</label>
                    <select name="classroom_id" class="form-select">
                        <option value="">-- Pilih Kelas --</option>
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

                <button type="submit" class="btn-print">
                    <i class="fa-solid fa-print"></i> EXPORT PDF
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleForm(type) {
            const classWrapper = document.getElementById('classroom-wrapper');
            if (type === 'teacher') {
                classWrapper.style.display = 'none';
            } else {
                classWrapper.style.display = 'block';
            }
        }
    </script>
</x-app-layout>