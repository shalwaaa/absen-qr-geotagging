<x-app-layout>
    <x-slot name="header">
        <div style="margin-bottom: 2rem;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Sinkronisasi Data Pusat</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Integrasi data dengan sistem pusat sekolah</p>
        </div>
    </x-slot>
    
    <style>
        /* Main Card */
        .sync-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .sync-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
        
        /* Header */
        .sync-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .sync-title {
            color: #142C14;
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #2D5128 0%, #537B2F 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .sync-subtitle {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 1.75rem;
        }
        
        .form-label {
            display: block;
            color: #4a6741;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            letter-spacing: 0.02em;
        }
        
        .select-wrapper {
            position: relative;
        }
        
        .form-select {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #334155;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            outline: none;
        }
        
        .form-select:focus {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }
        
        .select-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
        }
        
        .form-note {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 0.5rem;
            padding-left: 0.25rem;
        }
        
        /* Alert Boxes */
        .alert {
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            font-size: 0.9rem;
            border: 1px solid;
        }
        
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-color: #dcfce7;
        }
        
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fee2e2;
        }
        
        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border-color: #fef3c7;
        }
        
        .alert-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }
        
        /* Action Buttons */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
        }
        
        .btn-cleanup {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-cleanup:hover {
            background: #fecaca;
            transform: translateY(-2px);
        }
        
        .btn-cleanup:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Sync Button */
        .btn-sync {
            width: 100%;
            background: linear-gradient(135deg, #2D5128 0%, #537B2F 100%);
            color: white;
            border: none;
            padding: 1.25rem;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 15px rgba(45, 81, 40, 0.2);
        }
        
        .btn-sync:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(45, 81, 40, 0.3);
        }
        
        .btn-sync:active:not(:disabled) {
            transform: translateY(-1px);
        }
        
        .btn-sync:disabled {
            background: linear-gradient(135deg, #9ca3af 0%, #94a3b8 100%);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-icon {
            font-size: 1.1rem;
        }
        
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Progress Bar */
        .progress-container {
            margin-top: 1.5rem;
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            display: none;
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .progress-label {
            color: #475569;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .progress-text {
            color: #4a6741;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4a6741 0%, #8DA750 100%);
            border-radius: 10px;
            transition: width 0.5s ease;
            width: 0%;
        }
        
        .progress-fill.complete {
            background: #059669;
        }
        
        .progress-fill.error {
            background: #dc2626;
        }
        
        /* Steps List */
        .sync-steps {
            margin-top: 1.25rem;
            padding: 0;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .step-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            flex-shrink: 0;
        }
        
        .step-icon.pending {
            background: #f1f5f9;
            color: #94a3b8;
        }
        
        .step-icon.active {
            background: #4a6741;
            color: white;
        }
        
        .step-icon.complete {
            background: #059669;
            color: white;
        }
        
        .step-icon.error {
            background: #dc2626;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sync-container {
                padding: 0 1rem;
            }
            
            .sync-card {
                padding: 1.75rem;
            }
            
            .sync-title {
                font-size: 1.5rem;
            }
            
            .action-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .btn-cleanup {
                align-self: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .sync-card {
                padding: 1.5rem;
            }
            
            .alert {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="sync-container">
            <div class="sync-card">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <div class="alert-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <div class="alert-icon">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <div class="alert-icon">
                            <i class="fa-solid fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <strong>Terdapat kesalahan:</strong>
                            <ul class="mt-1 ml-4 list-disc">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Action Bar -->
                <div class="action-bar">
                    <button onclick="hapusTahunKosong()" id="cleanupBtn" class="btn-cleanup">
                        <i class="fa-solid fa-trash-can"></i>
                        Hapus Tahun Kosong
                    </button>
                    
                    <div class="text-sm text-slate-500">
                        <i class="fa-solid fa-database mr-1"></i>
                        {{ $years->count() }} Tahun Ajaran Tersedia
                    </div>
                </div>

                <!-- Warning Info -->
                <div class="alert alert-warning">
                    <div class="alert-icon">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div>
                        <strong>Informasi Penting:</strong>
                        <ul class="mt-1 ml-4 list-disc">
                            <li>Pastikan koneksi internet stabil (Proses memakan waktu 30-60 menit lebih)</li>
                            <li>Data guru akan diperbarui/ditambah tanpa menghapus data lama</li>
                            <li>Data siswa akan dipetakan ke tahun ajaran yang dipilih</li>
                        </ul>
                    </div>
                </div>

                <!-- Sync Form -->
                <form action="{{ route('sync.data') }}" method="POST" id="syncForm">
                    @csrf
                    
                    <!-- Year Selection -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-solid fa-folder mr-2"></i>
                            Tahun Ajaran Target
                        </label>
                        <div class="select-wrapper">
                            <select name="academic_year_id" required class="form-select">
                                @foreach($years as $y)
                                    <option value="{{ $y->id }}" {{ old('academic_year_id') == $y->id ? 'selected' : '' }}>
                                        {{ $y->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="select-icon">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                        </div>
                        <p class="form-note">
                            *Tahun ajar otomatis digenerate dari 2020 s.d {{ date('Y') + 1 }}
                        </p>
                    </div>

                    <!-- Sync Button -->
                    <button type="submit" id="syncBtn" class="btn-sync">
                        <span id="btnText">
                            <i class="fa-solid fa-cloud-arrow-down btn-icon"></i>
                            TARIK DATA SEKARANG
                        </span>
                        <div id="btnSpinner" class="spinner"></div>
                    </button>
                </form>

                <!-- Progress Section -->
                <div id="progressContainer" class="progress-container">
                    <div class="progress-header">
                        <span class="progress-label">Proses Sinkronisasi</span>
                        <span id="progressText" class="progress-text">Menyiapkan...</span>
                    </div>
                    <div class="progress-bar">
                        <div id="progressFill" class="progress-fill"></div>
                    </div>
                    
                    <!-- Steps Indicator -->
                    <div class="sync-steps">
                        <div class="step-item">
                            <div id="step1Icon" class="step-icon pending">
                                <i class="fa-solid fa-1"></i>
                            </div>
                            <span id="step1Text">Mengambil data guru</span>
                        </div>
                        <div class="step-item">
                            <div id="step2Icon" class="step-icon pending">
                                <i class="fa-solid fa-2"></i>
                            </div>
                            <span id="step2Text">Mengambil data siswa</span>
                        </div>
                        <div class="step-item">
                            <div id="step3Icon" class="step-icon pending">
                                <i class="fa-solid fa-3"></i>
                            </div>
                            <span id="step3Text">Memetakan ke tahun ajaran</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let syncInProgress = false;
        const steps = ['guru', 'siswa', 'pemetaan'];
        const stepNames = {
            'guru': 'Mengambil data guru',
            'siswa': 'Mengambil data siswa',
            'pemetaan': 'Memetakan ke tahun ajaran'
        };

        document.getElementById('syncForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (syncInProgress) {
                alert('Sinkronisasi sedang berjalan, harap tunggu...');
                return;
            }
            
            const form = this;
            const syncBtn = document.getElementById('syncBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const progressContainer = document.getElementById('progressContainer');
            const progressFill = document.getElementById('progressFill');
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
            syncBtn.disabled = true;
            btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin btn-icon"></i> MEMPROSES...';
            progressContainer.style.display = 'block';
            progressFill.style.width = '5%';
            progressText.textContent = 'Memulai sinkronisasi...';
            
            // Reset step indicators
            steps.forEach((_, index) => {
                const stepIcon = document.getElementById(`step${index + 1}Icon`);
                const stepText = document.getElementById(`step${index + 1}Text`);
                stepIcon.className = 'step-icon pending';
                stepIcon.innerHTML = `<i class="fa-solid fa-${index + 1}"></i>`;
                stepText.textContent = stepNames[steps[index]] || `Langkah ${index + 1}`;
            });
            
            // Start sync process
            await executeSyncStep(form, steps[0], progressFill, progressText);
        });

        async function executeSyncStep(form, step, progressFill, progressText) {
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Add step parameter
            formData.append('step', step);
            
            try {
                // Update step UI
                const stepIndex = steps.indexOf(step);
                if (stepIndex >= 0) {
                    const stepIcon = document.getElementById(`step${stepIndex + 1}Icon`);
                    stepIcon.className = 'step-icon active';
                    stepIcon.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                }
                
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
                    throw new Error(data.message || 'Langkah gagal');
                }
                
                // Update progress
                progressFill.style.width = data.progress + '%';
                progressText.textContent = data.message;
                
                // Mark step as complete
                if (stepIndex >= 0) {
                    const stepIcon = document.getElementById(`step${stepIndex + 1}Icon`);
                    stepIcon.className = 'step-icon complete';
                    stepIcon.innerHTML = '<i class="fa-solid fa-check"></i>';
                }
                
                // Continue to next step
                if (data.step && data.step !== 'complete') {
                    await new Promise(resolve => setTimeout(resolve, 800));
                    await executeSyncStep(form, data.step, progressFill, progressText);
                } else {
                    // Complete
                    progressFill.style.width = '100%';
                    progressFill.classList.add('complete');
                    progressText.textContent = '✅ Sinkronisasi berhasil!';
                    
                    // Update button
                    setTimeout(() => {
                        syncInProgress = false;
                        document.getElementById('syncBtn').disabled = false;
                        document.getElementById('btnText').innerHTML = '<i class="fa-solid fa-check btn-icon"></i> SELESAI';
                        
                        // Reload page after 2 seconds
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }, 1500);
                }
                
            } catch (error) {
                console.error('Error:', error);
                progressFill.style.width = '0%';
                progressFill.classList.add('error');
                progressText.textContent = '❌ Error: ' + error.message;
                
                // Mark failed step
                const stepIndex = steps.indexOf(step);
                if (stepIndex >= 0) {
                    const stepIcon = document.getElementById(`step${stepIndex + 1}Icon`);
                    stepIcon.className = 'step-icon error';
                    stepIcon.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                }
                
                resetSyncButton();
            }
        }

        function resetSyncButton() {
            setTimeout(() => {
                syncInProgress = false;
                const syncBtn = document.getElementById('syncBtn');
                syncBtn.disabled = false;
                document.getElementById('btnText').innerHTML = '<i class="fa-solid fa-cloud-arrow-down btn-icon"></i> COBA LAGI';
            }, 3000);
        }

        // Cleanup function
        function hapusTahunKosong() {
            if (!confirm('Yakin ingin menghapus tahun ajaran yang kosong?\n\nTahun ajaran tanpa data siswa akan dihapus permanen.')) {
                return;
            }
            
            const btn = document.getElementById('cleanupBtn');
            const originalHtml = btn.innerHTML;
            
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;
            
            fetch('{{ route("sync.cleanup") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }
    </script>
</x-app-layout>