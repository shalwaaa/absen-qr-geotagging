<x-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --fern: #537B2F; --asparagus: #8DA750; --mindaro: #E4EB9C; --cream: #FAFAF5; }
        
        .card-custom { background: white; border-radius: 16px; border: 1px solid #f0fdf4; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); height: 100%; }
        
        /* Form Inputs */
        .form-input { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px; outline: none; transition: 0.2s; font-size: 0.9rem;}
        .form-input:focus { border-color: var(--fern); }
        .btn-submit { background: var(--cal-poly); color: white; width: 100%; padding: 12px; border-radius: 10px; font-weight: bold; border: none; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { background: var(--fern); transform: translateY(-2px); }

        /* Accordion Kategori */
        .category-group { border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 16px; background: white; overflow: hidden; transition: 0.3s; }
        .category-group:hover { border-color: var(--asparagus); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        
        .category-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #FAFCF8; cursor: pointer; user-select: none; }
        .category-header h4 { font-weight: 800; color: var(--dark-green); font-size: 1.1rem; display: flex; align-items: center; gap: 10px; }
        
        /* Pertanyaan List */
        .questions-container { padding: 0 20px 20px 20px; border-top: 1px dashed #e2e8f0; display: none; }
        .questions-container.active { display: block; animation: fadeIn 0.3s ease-out; }
        
        .question-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #334155; }
        .question-item:last-child { border-bottom: none; }
        .question-item:hover { background: #f8fafc; border-radius: 8px; }

        /* Action Buttons */
        .btn-icon { width: 30px; height: 30px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; background: white; color: #64748b; transition: 0.2s; cursor: pointer; }
        .btn-icon:hover { background: #fef2f2; color: #ef4444; border-color: #fca5a5; } /* Merah utk hapus */
        .btn-edit-icon:hover { background: #fef3c7; color: #d97706; border-color: #fcd34d; } /* Kuning utk edit */

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-6xl mx-auto">

            <div class="mb-8">
                <h1 class="text-2xl font-black text-[#142C14]">Manajemen Kategori & Pertanyaan</h1>
                <p class="text-gray-500">Atur indikator karakter beserta pertanyaan detail untuk form penilaian Wali Kelas.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl text-sm font-bold border border-green-200">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KIRI: FORM TAMBAH KATEGORI (JUDUL BESAR) -->
                <div class="lg:col-span-1">
                    <div class="card-custom sticky top-6">
                        <h3 class="font-bold text-lg mb-6 text-[#142C14] flex items-center gap-2">
                            <i class="fa-solid fa-folder-plus text-[#537B2F]"></i> Buat Kategori Baru
                        </h3>

                        <form action="{{ route('assessment-categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="name" placeholder="Cth: Kedisiplinan, Sikap Sosial" class="form-input" required>
                            </div>
                            <button type="submit" class="btn-submit">
                                SIMPAN KATEGORI
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                            Setelah kategori dibuat, Anda bisa menambahkan daftar pertanyaan (indikator) di panel sebelah kanan.
                        </p>
                    </div>
                </div>

                <!-- KANAN: DAFTAR KATEGORI & PERTANYAAN -->
                <div class="lg:col-span-2">
                    @forelse($categories as $cat)
                        <div class="category-group">
                            <!-- HEADER KATEGORI (Bisa di-klik untuk buka/tutup) -->
                            <div class="category-header" onclick="toggleAccordion('cat-{{ $cat->id }}')">
                                <h4>
                                    <i class="fa-solid fa-caret-right text-[var(--asparagus)] transition-transform" id="icon-{{ $cat->id }}"></i> 
                                    {{ $cat->name }}
                                </h4>
                                <div class="flex items-center gap-2" onclick="event.stopPropagation();">
                                    <!-- Label Jumlah Soal -->
                                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded border border-green-200">
                                        {{ $cat->questions->count() }} Pertanyaan
                                    </span>
                                    
                                    <!-- Tombol Hapus Kategori -->
                                    <form action="{{ route('assessment-categories.destroy', $cat->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Hapus Kategori" onclick="return confirm('Hapus kategori ini beserta SELURUH PERTANYAAN di dalamnya?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- KONTEN: DAFTAR PERTANYAAN & FORM TAMBAH -->
                            <div class="questions-container" id="cat-{{ $cat->id }}">
                                
                                <!-- List Pertanyaan -->
                                <div class="mt-4 mb-4">
                                    @forelse($cat->questions as $q)
                                        <div class="question-item">
                                            <div class="flex gap-3">
                                                <i class="fa-regular fa-star text-[var(--asparagus)] mt-1"></i>
                                                <span>{{ $q->question }}</span>
                                            </div>
                                            
                                            <!-- Tombol Hapus Pertanyaan -->
                                            <form action="{{ route('assessment-questions.destroy', $q->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 px-2" title="Hapus Pertanyaan">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400 italic py-2">Belum ada pertanyaan/indikator di kategori ini.</p>
                                    @endforelse
                                </div>

                                <!-- Form Tambah Pertanyaan Cepat -->
                                <form action="{{ route('assessment-questions.store') }}" method="POST" class="flex gap-2 mt-4 bg-gray-50 p-2 rounded-lg border border-gray-200">
                                    @csrf
                                    <input type="hidden" name="category_id" value="{{ $cat->id }}">
                                    <input type="text" name="question" placeholder="Ketik indikator baru & tekan Enter..." class="w-full bg-transparent border-none outline-none text-sm px-2 text-gray-700" required>
                                    <button type="submit" class="bg-[var(--cal-poly)] text-white px-3 py-1 rounded text-xs font-bold hover:bg-[var(--fern)] whitespace-nowrap">
                                        + Tambah
                                    </button>
                                </form>

                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-white rounded-2xl border border-dashed border-gray-300">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p class="text-gray-500">Belum ada kategori yang dibuat.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <!-- Script Accordion -->
    <script>
        function toggleAccordion(id) {
            const container = document.getElementById(id);
            const icon = document.getElementById('icon-' + id.split('-')[1]);
            
            if (container.classList.contains('active')) {
                container.classList.remove('active');
                icon.style.transform = 'rotate(0deg)';
            } else {
                // Opsional: Tutup semua accordion lain dulu biar rapi

                container.classList.add('active');
                icon.style.transform = 'rotate(90deg)';
            }
        }
    </script>
</x-app-layout>