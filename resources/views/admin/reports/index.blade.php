<x-app-layout>
    <x-slot name="header">
        <div style="margin-bottom: 2rem;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Laporan Rekapitulasi Kehadiran Tiap kelas</span>
            </h2>
        </div>
    </x-slot>

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

        /* STYLE UNTUK SEARCH KELAS */
        .class-search-container {
            position: relative;
            margin-bottom: 6px;
        }

        .class-search-wrapper {
            position: relative;
        }

        .class-search-input {
            width: 100%;
            padding: 12px 45px 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s;
            outline: none;
            background: white;
        }

        .class-search-input:focus {
            border-color: var(--fern);
            box-shadow: 0 0 0 4px rgba(83, 123, 47, 0.1);
        }

        .class-search-btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 44px;
            background: transparent;
            border: none;
            color: var(--fern);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0 12px 12px 0;
        }

        .class-search-btn:hover {
            background: rgba(83, 123, 47, 0.05);
        }

        .class-search-clear {
            position: absolute;
            right: 44px;
            top: 50%;
            transform: translateY(-50%);
            background: #e2e8f0;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .class-search-clear:hover {
            background: #cbd5e1;
            color: #475569;
        }

        /* DROPDOWN HASIL PENCARIAN */
        .class-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            margin-top: 4px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        .class-dropdown.active {
            display: block;
        }

        .class-option {
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .class-option:hover {
            background: #f0fdf4;
            color: var(--fern);
        }

        .class-option:last-child {
            border-bottom: none;
        }

        .class-option.selected {
            background: #f0fdf4;
            color: var(--fern);
            font-weight: 600;
        }

        .selected-class-info {
            background: #f0fdf4;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 12px;
            border: 2px solid #bbf7d0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .remove-class-btn {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: 0.2s;
        }

        .remove-class-btn:hover {
            background: #dc2626;
        }

        .search-hint {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 4px;
            padding-left: 4px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .card-filter {
                padding: 24px 20px;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
    </style>

    <div class="py-12 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-[#142C14]">Laporan Kehadiran</h1>
            <p class="text-gray-500 mt-1">Unduh rekap presensi kelas dalam format PDF.</p>
        </div>

        <div class="card-filter">
            <!-- TARGET BLANK AGAR PDF BUKA DI TAB BARU -->
            <form action="{{ route('reports.generate') }}" method="POST" target="_blank" id="reportForm">
                @csrf

                <!-- INPUT HIDDEN UNTUK KELAS YANG DIPILIH -->
                <input type="hidden" name="classroom_id" id="selectedClassId" required>

                <!-- CARI KELAS (MENGANTI DROPDOWN) -->
                <div class="mb-6">
                    <label class="form-label">Cari Kelas</label>
                    
                    <!-- CONTAINER SEARCH -->
                    <div class="class-search-container">
                        <div class="class-search-wrapper">
                            <input 
                                type="text" 
                                id="classSearchInput" 
                                placeholder="Ketik nama kelas..." 
                                class="class-search-input"
                                autocomplete="off"
                            >
                            <button type="button" class="class-search-btn">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            
                            <!-- TOMBOL CLEAR -->
                            <button type="button" class="class-search-clear" id="clearSearchBtn" style="display: none;">
                                ✕
                            </button>
                        </div>
                        
                        <!-- DROPDOWN HASIL PENCARIAN -->
                        <div class="class-dropdown" id="classDropdown">
                            <!-- Hasil pencarian akan muncul di sini via JavaScript -->
                        </div>
                        
                        
                    
                    <!-- INFO KELAS YANG DIPILIH -->
                    <div id="selectedClassContainer" class="selected-class-info" style="display: none;">
                        <div>
                            <div class="font-bold text-[#142C14]" id="selectedClassName"></div>
                            <div class="text-xs text-gray-500 mt-1">Kelas terpilih untuk laporan</div>
                        </div>
                        <button type="button" class="remove-class-btn" id="removeClassBtn">
                            <i class="fa-solid fa-xmark"></i> Hapus
                        </button>
                    </div>
                </div>

                <!-- PILIH TANGGAL -->
                <div class="grid grid-cols-2 gap-4 mb-2">
                    <div>
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-input" required id="startDate">
                    </div>
                    <div>
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-input" required id="endDate">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mb-6">*Pilih rentang waktu laporan (Misal: 1 Bulan).</p>

                <!-- TOMBOL CETAK -->
                <button type="submit" class="btn-print" id="printBtn">
                    <i class="fa-solid fa-file-pdf text-xl"></i> CETAK LAPORAN PDF
                </button>
            </form>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSearchInput = document.getElementById('classSearchInput');
            const classDropdown = document.getElementById('classDropdown');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const selectedClassId = document.getElementById('selectedClassId');
            const selectedClassContainer = document.getElementById('selectedClassContainer');
            const selectedClassName = document.getElementById('selectedClassName');
            const removeClassBtn = document.getElementById('removeClassBtn');
            const printBtn = document.getElementById('printBtn');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            
            // Data kelas dari backend (konversi ke JavaScript array)
            const classrooms = @json($classrooms);
            
            // Set tanggal default (hari ini dan awal bulan)
            const today = new Date();
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            
            startDate.value = firstDayOfMonth.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
            
            let selectedClass = null;
            let searchTimeout = null;
            
            // Fungsi untuk mencari kelas
            function searchClasses(query) {
                if (!query.trim()) {
                    classDropdown.innerHTML = '';
                    classDropdown.classList.remove('active');
                    return;
                }
                
                const lowerQuery = query.toLowerCase();
                const filteredClasses = classrooms.filter(c => 
                    c.name.toLowerCase().includes(lowerQuery)
                );
                
                // Tampilkan hasil
                if (filteredClasses.length > 0) {
                    let html = '';
                    filteredClasses.forEach(c => {
                        const isSelected = selectedClass && selectedClass.id === c.id;
                        html += `
                            <div class="class-option ${isSelected ? 'selected' : ''}" 
                                 data-id="${c.id}" 
                                 data-name="${c.name}"
                                 onclick="selectClass(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                                ${c.name}
                            </div>
                        `;
                    });
                    classDropdown.innerHTML = html;
                    classDropdown.classList.add('active');
                } else {
                    classDropdown.innerHTML = `
                        <div class="class-option" style="color: #94a3b8; font-style: italic;">
                            Tidak ditemukan kelas dengan kata kunci "${query}"
                        </div>
                    `;
                    classDropdown.classList.add('active');
                }
            }
            
            // Event listener untuk input pencarian
            classSearchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const query = e.target.value;
                
                // Tampilkan tombol clear jika ada teks
                clearSearchBtn.style.display = query ? 'block' : 'none';
                
                // Debounce search
                searchTimeout = setTimeout(() => {
                    searchClasses(query);
                }, 300);
            });
            
            // Event listener untuk tombol clear
            clearSearchBtn.addEventListener('click', function() {
                classSearchInput.value = '';
                classSearchInput.focus();
                clearSearchBtn.style.display = 'none';
                classDropdown.classList.remove('active');
            });
            
            // Event listener untuk klik di luar dropdown
            document.addEventListener('click', function(e) {
                if (!classSearchInput.contains(e.target) && 
                    !classDropdown.contains(e.target) && 
                    !clearSearchBtn.contains(e.target)) {
                    classDropdown.classList.remove('active');
                }
            });
            
            // Fungsi untuk memilih kelas (harus global)
            window.selectClass = function(id, name) {
                selectedClass = { id, name };
                selectedClassId.value = id;
                selectedClassName.textContent = name;
                selectedClassContainer.style.display = 'flex';
                
                // Update dropdown untuk menandai yang dipilih
                const options = classDropdown.querySelectorAll('.class-option');
                options.forEach(opt => {
                    if (parseInt(opt.dataset.id) === id) {
                        opt.classList.add('selected');
                    } else {
                        opt.classList.remove('selected');
                    }
                });
                
                // Sembunyikan dropdown
                classDropdown.classList.remove('active');
                classSearchInput.value = '';
                clearSearchBtn.style.display = 'none';
                
                // Validasi form
                validateForm();
            };
            
            // Fungsi untuk menghapus kelas yang dipilih
            removeClassBtn.addEventListener('click', function() {
                selectedClass = null;
                selectedClassId.value = '';
                selectedClassContainer.style.display = 'none';
                
                // Reset dropdown
                const options = classDropdown.querySelectorAll('.class-option');
                options.forEach(opt => opt.classList.remove('selected'));
                
                // Validasi form
                validateForm();
            });
            
            // Validasi form sebelum submit
            function validateForm() {
                const isClassSelected = selectedClassId.value !== '';
                const isDatesValid = startDate.value && endDate.value;
                
                if (isClassSelected && isDatesValid) {
                    printBtn.disabled = false;
                    printBtn.style.opacity = '1';
                    printBtn.style.cursor = 'pointer';
                } else {
                    printBtn.disabled = true;
                    printBtn.style.opacity = '0.6';
                    printBtn.style.cursor = 'not-allowed';
                }
            }
            
            // Event listener untuk validasi tanggal
            startDate.addEventListener('change', validateForm);
            endDate.addEventListener('change', validateForm);
            
            // Validasi saat halaman pertama kali dimuat
            validateForm();
            
            // Fokus ke input pencarian
            classSearchInput.focus();
        });
    </script>
</x-app-layout>