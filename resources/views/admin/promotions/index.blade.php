<x-app-layout>
    <x-slot name="header"></x-slot>

    <!-- Gunakan Style Hijau Kamu -->
    <style>
        /* (Copy style hijau dari dashboard sebelumnya di sini biar seragam) */
        .card { background: white; border-radius: 16px; padding: 24px; border: 1px solid #f0fdf4; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .btn-primary { background-color: #2D5128; color: white; padding: 10px 20px; border-radius: 8px; font-weight: bold; width: 100%; transition: 0.3s; }
        .btn-primary:hover { background-color: #537B2F; }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[#142C14]">Kenaikan Kelas & Kelulusan (Rombel)</h1>
                <p class="text-sm text-gray-500">Pindahkan siswa antar kelas secara massal.</p>
            </div>

            <div class="card">
                <form action="{{ route('promotions.process') }}" method="POST">
                    @csrf

                    <!-- 1. PILIH KELAS ASAL & TUJUAN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        
                        <!-- KELAS ASAL -->
                        <div>
                            <label class="block text-sm font-bold text-[#537B2F] mb-2">Dari Kelas (Sumber)</label>
                            <select name="from_classroom_id" id="from_class" class="w-full rounded-lg border-gray-300" onchange="loadStudents()">
                                <option value="">-- Pilih Kelas Asal --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- AKSI & TUJUAN -->
                        <div>
                            <label class="block text-sm font-bold text-[#537B2F] mb-2">Aksi / Tujuan</label>
                            <div class="flex gap-2 mb-2">
                                <label class="flex items-center gap-2 cursor-pointer bg-gray-100 px-3 py-2 rounded-lg">
                                    <input type="radio" name="action" value="promote" checked onchange="toggleTarget(true)">
                                    <span class="text-sm">Pindah/Naik Kelas</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-red-50 px-3 py-2 rounded-lg text-red-700">
                                    <input type="radio" name="action" value="graduate" onchange="toggleTarget(false)">
                                    <span class="text-sm">Luluskan (Alumni)</span>
                                </label>
                            </div>

                            <select name="to_classroom_id" id="to_class" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Pilih Kelas Tujuan --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 2. DAFTAR SISWA (CHECKBOX) -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-[#537B2F]">Daftar Siswa</label>
                            <div class="text-xs text-gray-500">
                                <button type="button" onclick="checkAll(true)" class="underline text-blue-600">Pilih Semua</button> / 
                                <button type="button" onclick="checkAll(false)" class="underline text-red-600">Batal Pilih</button>
                            </div>
                        </div>
                        
                        <div id="student_list_container" class="border rounded-lg p-4 h-64 overflow-y-auto bg-gray-50 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <p class="text-gray-400 text-sm text-center col-span-2 py-10">Pilih kelas asal terlebih dahulu...</p>
                        </div>
                        <p class="text-xs text-red-500 mt-2">*Hilangkan centang jika siswa tinggal kelas.</p>
                    </div>

                    <button type="submit" class="btn-primary" onclick="return confirm('Yakin ingin memindahkan siswa yang dipilih?')">
                        PROSES SEKARANG
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT UNTUK LOAD DATA -->
    <script>
        function toggleTarget(show) {
            const select = document.getElementById('to_class');
            if(show) {
                select.style.display = 'block';
                select.required = true;
            } else {
                select.style.display = 'none';
                select.required = false;
            }
        }

        async function loadStudents() {
            const classId = document.getElementById('from_class').value;
            const container = document.getElementById('student_list_container');
            
            if(!classId) {
                container.innerHTML = '<p class="text-gray-400 text-sm text-center col-span-2 py-10">Pilih kelas asal...</p>';
                return;
            }

            container.innerHTML = '<p class="text-center col-span-2">Memuat data...</p>';

            try {
                const response = await fetch('/api/students/' + classId);
                const students = await response.json();

                if(students.length === 0) {
                    container.innerHTML = '<p class="text-red-400 text-sm text-center col-span-2 py-10">Kelas ini kosong.</p>';
                    return;
                }

                let html = '';
                students.forEach(s => {
                    html += `
                        <label class="flex items-center gap-3 p-2 bg-white border rounded hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="student_ids[]" value="${s.id}" checked class="rounded text-green-600 focus:ring-green-500">
                            <div>
                                <p class="text-sm font-bold text-gray-800">${s.name}</p>
                                <p class="text-xs text-gray-500">NIS: ${s.nip_nis ?? '-'}</p>
                            </div>
                        </label>
                    `;
                });
                container.innerHTML = html;

            } catch (error) {
                console.error(error);
                container.innerHTML = '<p class="text-red-500 text-center col-span-2">Gagal memuat data.</p>';
            }
        }

        function checkAll(checked) {
            const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
            checkboxes.forEach(cb => cb.checked = checked);
        }
    </script>
</x-app-layout>