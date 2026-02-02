<x-app-layout>
    <!-- LOAD SELECT2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Container Style */
        .form-content { width: 100%; max-width: 1000px; margin: 0 auto; }
        .form-header { margin-bottom: 40px; }
        .form-title { color: #4a6741; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .form-subtitle { color: #64748b; font-size: 15px; line-height: 1.5; }

        /* Form Layout */
        .form-layout { display: flex; flex-direction: column; gap: 24px; width: 100%; }

        /* Grid Row untuk Jam */
        .input-grid-row { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 20px; 
            width: 100%; 
        }

        /* Input Styling */
        .form-group { width: 100%; }
        .form-label { display: block; color: #4a6741; font-size: 15px; font-weight: 600; margin-bottom: 10px; }
        
        .form-input {
            width: 100%;
            height: 48px;
            padding: 12px 16px;
            font-size: 15px;
            color: #1e293b;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.2s ease;
            outline: none;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            margin-top: 10px;
            padding-top: 32px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-cancel {
            color: #64748b;
            font-weight: 500;
            padding: 12px 32px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            background: white;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-submit {
            background: #4a6741;
            color: white;
            font-weight: 600;
            padding: 12px 40px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #3d5535;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* --- CUSTOM STYLE SELECT2 (BIAR COCOK SAMA TEMA HIJAU) --- */
        .select2-container .select2-selection--single {
            height: 48px !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 10px !important;
            display: flex; align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important; font-size: 15px; padding-left: 16px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important; right: 10px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4a6741 !important; color: white !important;
        }
        /* Fokus State */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4a6741 !important;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }

        @media (max-width: 640px) {
            .input-grid-row { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column-reverse; align-items: stretch; }
        }
    </style>

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Edit Jadwal Pelajaran</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Update Jadwal</div>
                    <div class="form-subtitle">Sesuaikan kembali waktu, pengajar, atau kelas untuk jadwal yang sudah ada.</div>
                </div>

                <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-layout">
                        <!-- HARI (Select Biasa) -->
                        <div class="form-group">
                            <label class="form-label">Hari</label>
                            <select name="day" class="form-input">
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $day)
                                    <option value="{{ $day }}" {{ $schedule->day == $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-grid-row">
                            <div class="form-group">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="start_time" value="{{ $schedule->start_time }}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="end_time" value="{{ $schedule->end_time }}" class="form-input" required>
                            </div>
                        </div>

                        <!-- GURU (SEARCHABLE) -->
                        <div class="form-group">
                            <label class="form-label">Guru Pengajar</label>
                            <select name="teacher_id" class="select2 w-full" required>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}" {{ $schedule->teacher_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->nip_nis }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- MAPEL (SEARCHABLE) -->
                        <div class="form-group">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="subject_id" class="select2 w-full" required>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}" {{ $schedule->subject_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KELAS (SEARCHABLE) -->
                        <div class="form-group">
                            <label class="form-label">Kelas</label>
                            <select name="classroom_id" class="select2 w-full" required>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}" {{ $schedule->classroom_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('schedules.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-rotate"></i> Update Jadwal
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- LOAD JQUERY & SELECT2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Aktifkan Select2
            $('.select2').select2({
                width: '100%',
                placeholder: function(){
                    $(this).data('placeholder');
                }
            });
        });
    </script>
</x-app-layout>