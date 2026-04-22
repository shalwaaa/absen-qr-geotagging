<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Evaluasi Karakter Siswa</span>
                </h2>
                <p style="color: #E4EB9C">Kelas {{ $classroom->name }} • Periode {{ $monthName }}</p>
            </div>
        </div>
    </x-slot>

    <!-- LOAD CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { 
            --dark-green: #142C14; 
            --cal-poly: #2D5128; 
            --fern: #537B2F; 
            --asparagus: #8DA750; 
            --mindaro: #E4EB9C; 
            --cream-bg: #FAFAF5;
        }
        
        /* Container Utama */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }

        /* Progress Container */
        .progress-container { 
            background: white; 
            border-radius: 20px; 
            padding: 1.5rem; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            margin-bottom: 1.5rem; 
            border: 1px solid #e2e8f0;
        }
        .progress-bar-bg { 
            width: 100%; 
            height: 12px; 
            background-color: #f1f5f9; 
            border-radius: 10px; 
            overflow: hidden; 
            margin-top: 12px; 
        }
        .progress-bar-fill { 
            height: 100%; 
            background: linear-gradient(90deg, var(--fern), var(--asparagus)); 
            transition: width 1s ease-out; 
        }

        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .chart-wrapper {
            width: 100%;
            height: 300px;
            position: relative;
        }

        /* Grid Siswa */
        .students-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .students-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1.25rem;
            }
        }
        @media (min-width: 1024px) {
            .students-grid {
                grid-template-columns: repeat(5, 1fr);
                gap: 1.5rem;
            }
        }

        /* Student Card */
        .student-card { 
            background: white; 
            border-radius: 16px; 
            padding: 1.25rem; 
            border: 1px solid #f1f5f9; 
            transition: 0.3s; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            text-align: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            min-height: 200px; 
            justify-content: space-between; 
        }
        .student-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(45,81,40,0.08); 
            border-color: var(--mindaro); 
        }
        
        .student-card h4 {
            font-size: 0.875rem; 
            font-weight: bold;
            color: #1f2937;
            width: 100%;
            word-break: break-word;
            hyphens: auto;
            overflow: visible;
            white-space: normal;
            line-height: 1.4;
            margin-bottom: 0.25rem;
        }

        .avatar { 
            width: 70px; 
            height: 70px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: white; 
            margin-bottom: 12px; 
        }
        .avatar.done { background: var(--fern); }
        .avatar.pending { background: #cbd5e1; }

        /* Buttons */
        .btn-grade, .btn-edit { 
            width: 100%; 
            padding: 0.625rem; 
            border-radius: 10px; 
            font-weight: 700; 
            font-size: 0.85rem; 
            border: none; 
            cursor: pointer; 
            transition: 0.2s; 
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-grade { 
            background: var(--cal-poly); 
            color: white; 
        }
        .btn-grade:hover { 
            background: var(--dark-green); 
        }
        .btn-edit { 
            background: #f8fafc; 
            color: var(--cal-poly); 
            border: 1px solid #e2e8f0; 
        }
        .btn-edit:hover { 
            background: var(--mindaro); 
            border-color: var(--fern); 
        }

        /* Action Buttons Row */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        .action-btn {
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: 0.2s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-print {
            background-color: #2563eb;
            color: white;
        }
        .btn-print:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }
        .btn-back {
            background-color: #2D5128;
            color: white;
        }
        .btn-back:hover {
            background-color: var(--fern);
            transform: translateY(-1px);
        }

        /* Alert */
        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            border: 1px solid #bbf7d0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Modal Styles (tetap sama) */
        .modal-overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
            z-index: 1000; 
            align-items: center; 
            justify-content: center; 
            backdrop-filter: blur(3px); 
            padding: 16px; 
            box-sizing: border-box; 
        }
        .modal-content { 
            background: white; 
            border-radius: 24px; 
            width: 100%; 
            max-width: 500px; 
            padding: 30px; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); 
            animation: modalPop 0.3s ease-out; 
            max-height: 90vh; 
            overflow-y: auto; 
        }
        @keyframes modalPop { 
            from { transform: scale(0.95); opacity: 0; } 
            to { transform: scale(1); opacity: 1; } 
        }

        .rating-group { 
            display: flex; 
            flex-direction: row-reverse; 
            justify-content: flex-end; 
            gap: 8px; 
        }
        .rating-group input { display: none; }
        .rating-group label { 
            cursor: pointer; 
            font-size: 2rem; 
            color: #e2e8f0; 
            transition: color 0.2s; 
            line-height: 1; 
        }
        @media (max-width: 480px) {
            .rating-group label { font-size: 1.5rem; }
        }
        .rating-group label:hover, 
        .rating-group label:hover ~ label, 
        .rating-group input:checked ~ label { 
            color: #f59e0b; 
        }
        
        .cat-box { 
            background: #f8fafc; 
            padding: 16px; 
            border-radius: 12px; 
            margin-bottom: 12px; 
            border: 1px solid #f1f5f9; 
        }
        .textarea-custom { 
            width: 100%; 
            border: 2px solid #e2e8f0; 
            border-radius: 12px; 
            padding: 12px; 
            outline: none; 
            font-size: 0.9rem; 
            transition: 0.3s; 
        }
        .textarea-custom:focus { 
            border-color: var(--fern); 
        }
    </style>

    <div class="main-container">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('teacher.assessments.print') }}" target="_blank" class="action-btn btn-print">
                <i class="fa-solid fa-print"></i> Cetak PDF
            </a>
            <a href="{{ route('homeroom.index') }}" class="action-btn btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <!-- PROGRESS BAR -->
        <div class="progress-container">
            <div class="flex justify-between items-center mb-2 flex-wrap gap-2">
                <h3 class="font-bold text-gray-800 text-base sm:text-lg">Progress Penilaian Bulan Ini</h3>
                <span class="font-black text-xl sm:text-2xl text-[var(--dark-green)]">{{ $progress }}%</span>
            </div>
            <p class="text-xs text-gray-500 mb-2">Anda telah menilai {{ $gradedCount }} dari {{ $totalStudents }} siswa.</p>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- CHART RATA-RATA KELAS -->
        @if(!empty($chartData) && count($chartData) > 0)
        <div class="chart-container">
            <h3 class="font-bold text-lg text-[var(--dark-green)] mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-[var(--fern)]"></i> 
                Rata-rata Nilai Kelas per Indikator
            </h3>
            <div class="chart-wrapper">
                <canvas id="classAverageChart"></canvas>
            </div>
        </div>
        @endif

        <!-- GRID SISWA -->
        <div class="students-grid">
            @foreach($students as $s)
                @php 
                    $isGraded = isset($assessmentsByKey[$s->id]); 
                    $existingData = $isGraded ? json_encode($assessmentsByKey[$s->id]) : 'null';
                @endphp
                
                <div class="student-card">
                    <div class="avatar {{ $isGraded ? 'done' : 'pending' }}">
                        @if($isGraded) <i class="fa-solid fa-check"></i> @else {{ substr($s->name, 0, 1) }} @endif
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm w-full" title="{{ $s->name }}">{{ $s->name }}</h4>
                    <p class="text-[10px] text-gray-400 font-mono mt-1">{{ $s->nip_nis }}</p>

                    @if($isGraded)
                        <button onclick="openModal({{ $s->id }}, '{{ addslashes($s->name) }}', {{ $existingData }})" class="btn-edit">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                    @else
                        <button onclick="openModal({{ $s->id }}, '{{ addslashes($s->name) }}', null)" class="btn-grade">
                            <i class="fa-regular fa-star"></i> Nilai
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL PENILAIAN (tetap sama) -->
    <div id="assessmentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-black text-[var(--dark-green)]">Form Evaluasi</h2>
                    <p class="text-sm font-bold text-gray-500" id="modalStudentName">Nama Siswa</p>
                </div>
                <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Form Hapus -->
            <form id="deleteForm" method="POST" class="hidden mb-4" onsubmit="return confirm('Yakin ingin menghapus / reset penilaian siswa ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-50 text-red-600 border border-red-200 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> Hapus / Reset Penilaian
                </button>
            </form>

            <form action="{{ route('teacher.assessments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="evaluatee_id" id="evaluatee_id">
                <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                <input type="hidden" name="period_month" value="{{ $periodMonth }}">

                <div class="space-y-4 mb-6">
                    @foreach($categories as $cat)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <h4 class="font-bold text-sm text-[var(--dark-green)]">{{ $cat->name }}</h4>
                            </div>
                            
                            <div class="p-4 space-y-4">
                                @foreach($cat->questions as $q)
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 border-b border-dashed border-gray-100 pb-3 last:border-0 last:pb-0">
                                        <p class="text-sm text-gray-700 flex-1">{{ $q->question }}</p>
                                        
                                        <div class="rating-group flex-shrink-0">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="scores[{{ $q->id }}]" id="q_{{ $q->id }}_star_{{ $i }}" value="{{ $i }}" required>
                                                <label for="q_{{ $q->id }}_star_{{ $i }}" title="{{ $i }} Bintang">★</label>
                                            @endfor
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-6">
                    <label class="block font-bold text-sm text-gray-800 mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="general_notes" id="general_notes" rows="3" class="textarea-custom" placeholder="Tuliskan apresiasi atau saran..."></textarea>
                </div>

                <button type="submit" class="btn-grade" style="margin-top: 0; padding: 14px; font-size: 1rem;">
                    <i class="fa-solid fa-save mr-2"></i> SIMPAN PENILAIAN
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPT LOGIKA MODAL & CHART -->
    <script>
        // Fungsi Modal
        function openModal(studentId, studentName, existingData) {
            document.getElementById('evaluatee_id').value = studentId;
            document.getElementById('modalStudentName').innerText = studentName;
            
            document.getElementById('general_notes').value = '';
            document.querySelectorAll('.rating-group input[type="radio"]').forEach(radio => {
                radio.checked = false;
            });
            
            const deleteForm = document.getElementById('deleteForm');

            if (existingData && existingData.id) {
                deleteForm.classList.remove('hidden');
                deleteForm.action = `/homeroom/assessments/${existingData.id}`;
                document.getElementById('general_notes').value = existingData.general_notes || '';
                
                if (existingData.details) {
                    existingData.details.forEach(detail => {
                        let radioId = `q_${detail.question_id}_star_${detail.score}`;
                        let radioBtn = document.getElementById(radioId);
                        if (radioBtn) radioBtn.checked = true;
                    });
                }
            } else {
                deleteForm.classList.add('hidden');
            }

            document.getElementById('assessmentModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('assessmentModal').style.display = 'none';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('assessmentModal');
            if (event.target == modal) closeModal();
        }

        // Chart
     // LOGIKA CHART RATA-RATA KATEGORI
        @if(!empty($chartData))
        document.addEventListener("DOMContentLoaded", function() {
            const ctxClass = document.getElementById('classAverageChart').getContext('2d');
            
            const labels = @json($chartLabels);
            const dataValues = @json($chartData);

            new Chart(ctxClass, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets:[{
                        label: 'Rata-rata Nilai',
                        data: dataValues,
                        backgroundColor: 'rgba(83, 123, 47, 0.7)', // Fern Green Transparan
                        borderColor: '#2D5128',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            stepSize: 1,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            title: {
                                display: true,
                                text: 'Nilai (Skala 5)',
                                color: '#64748b',
                                font: { weight: 'bold', size: 12 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 12, weight: 'bold', family: 'Poppins' },
                                color: '#1e293b'
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#142C14',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: (context) => `Rata-rata: ${context.raw.toFixed(2)} / 5 Bintang`
                            }
                        }
                    }
                }
            });
        });
        @endif
    </script>
</x-app-layout>