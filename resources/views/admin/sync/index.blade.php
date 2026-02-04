<x-app-layout>
    <x-slot name="header"></x-slot>
    
    <style>
        .card-sync { background: white; border-radius: 16px; padding: 30px; border: 1px solid #f0fdf4; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
        .btn-sync { background: #2D5128; color: white; width: 100%; padding: 12px; border-radius: 10px; font-weight: bold; transition: 0.2s; }
        .btn-sync:hover { background: #537B2F; transform: translateY(-2px); }
        .btn-sync:disabled { background: #9ca3af; cursor: not-allowed; transform: none; }
        /* Animasi spin */
        .spinner { display: none; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top: 3px solid #fff; width: 18px; height: 18px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    <div class="py-12 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-[#142C14]">Sinkronisasi Data Pusat</h1>
            <p class="text-gray-500 mt-2">Tarik data API (Guru, Kelas, Siswa) dan masukkan ke Folder Tahun Ajar.</p>
        </div>

        <div class="card-sync">
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 text-sm font-bold border border-green-200 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 text-sm font-bold border border-red-200 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 text-sm font-bold border border-red-200">
                    <ul class="list-disc ml-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <button
                    onclick="hapusTahunKosong()"
                    class="text-xs font-bold text-red-700 bg-red-50 px-3 py-2 rounded-lg hover:bg-red-100 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Hapus Tahun Kosong
                </button>
            </div>

            {{-- HAPUS FORM cleanup yang lama karena route academic-years.cleanup tidak ada --}}
            {{-- <form id="cleanup-form" action="{{ route('academic-years.cleanup') }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form> --}}

            <form action="{{ route('sync.data') }}" method="POST" id="syncForm">
                @csrf
                
                <div class="mb-6">
                    <label class="block font-bold text-[#142C14] mb-2">Pilih Folder Tahun Ajaran (Target)</label>
                    <div class="relative">
                        <select name="academic_year_id" required class="w-full border-2 border-gray-200 rounded-xl p-3 pl-10 focus:border-[#537B2F] outline-none appearance-none bg-white">
                            @foreach($years as $y)
                                <option value="{{ $y->id }}" {{ old('academic_year_id') == $y->id ? 'selected' : '' }}>
                                    {{ $y->name }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="w-5 h-5 absolute left-4 top-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 ml-1">
                        *Tahun ajar otomatis digenerate dari 2020 s.d {{ date('Y') + 1 }}.
                    </p>
                </div>

                <div class="bg-yellow-50 p-4 rounded-xl mb-8 border border-yellow-100">
                    <h4 class="font-bold text-yellow-800 text-sm mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Info Sinkronisasi:
                    </h4>
                    <ul class="text-xs text-yellow-700 list-disc ml-5 space-y-1">
                        <li>Pastikan koneksi internet stabil (Proses bisa memakan waktu 1-3 menit).</li>
                        <li>Data guru akan diperbarui/ditambah tanpa menghapus data lama.</li>
                        <li>Data Siswa akan dipetakan ke tahun ajaran yang dipilih.</li>
                    </ul>
                </div>

                <button type="submit" id="btnSubmit" class="btn-sync flex justify-center items-center gap-2">
                    <span id="btnText" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        TARIK DATA SEKARANG
                    </span>
                    <div id="btnLoader" class="spinner"></div>
                </button>
            </form>

            <!-- Progress Bar -->
            <div id="progressContainer" class="mt-6 hidden">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Status:</span>
                    <span id="progressText">Menyiapkan...</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-[#537B2F] h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

<script>
let syncInProgress = false;
let currentStep = 'start';

document.getElementById('syncForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (syncInProgress) {
        alert('Sync sedang berjalan, tunggu sebentar...');
        return;
    }
    
    const form = this;
    const btn = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    // Validasi
    const yearSelect = form.querySelector('select[name="academic_year_id"]');
    if (!yearSelect.value) {
        alert('Pilih tahun ajaran terlebih dahulu!');
        yearSelect.focus();
        return;
    }
    
    // Setup UI
    syncInProgress = true;
    btn.disabled = true;
    btnText.innerHTML = `
        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        MEMPROSES...
    `;
    
    progressContainer.classList.remove('hidden');
    progressBar.style.width = '5%';
    progressText.textContent = 'Memulai sinkronisasi...';
    
    // Mulai proses sync dengan step-by-step
    await executeSyncStep(form, 'guru', progressBar, progressText);
});

async function executeSyncStep(form, step, progressBar, progressText) {
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Tambahkan step parameter ke formData
    formData.append('step', step);
    
    try {
        const response = await fetch('{{ route("sync.data") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Step gagal');
        }
        
        // Update progress berdasarkan response dari server
        progressBar.style.width = data.progress + '%';
        progressText.textContent = data.message;
        
        // Jika ada next step, lanjutkan
        if (data.step && data.step !== 'complete') {
            // Delay kecil sebelum step berikutnya
            await new Promise(resolve => setTimeout(resolve, 500));
            await executeSyncStep(form, data.step, progressBar, progressText);
        } else {
            // Selesai
            progressBar.style.width = '100%';
            progressBar.style.backgroundColor = '#10b981';
            progressText.textContent = '✅ Sync berhasil! Mengalihkan...';
            
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
        
    } catch (error) {
        console.error('Sync step failed:', error);
        progressBar.style.width = '0%';
        progressText.textContent = '❌ Error: ' + error.message;
        progressBar.style.backgroundColor = '#ef4444';
        resetSyncButton();
    }
}

function resetSyncButton() {
    const btn = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    
    setTimeout(() => {
        syncInProgress = false;
        btn.disabled = false;
        btnText.innerHTML = `
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            COBA LAGI
        `;
    }, 3000);
}

// Fungsi untuk hapus tahun kosong
function hapusTahunKosong() {
    if (!confirm('Apakah Anda yakin ingin menghapus tahun ajaran yang kosong?\n\nTahun ajaran yang tidak memiliki siswa aktif akan dihapus.')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    // Tampilkan loading
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="currentColor" viewBox="0 0 20 20">
            <path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
        Memproses...
    `;
    btn.disabled = true;
    
    // Kirim request AJAX ke route sync.cleanup
    fetch('{{ route("sync.cleanup") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload(); // Refresh halaman
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan'));
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
</x-app-layout>