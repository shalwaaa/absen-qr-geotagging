<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Container Style */
        .form-content { width: 100%; max-width: 100%; margin: 0; }
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
        
        .form-input, .form-select {
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

        .form-input:focus, .form-select:focus {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }

        /* Select Arrow */
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 18px;
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

        @media (max-width: 640px) {
            .input-grid-row { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column-reverse; align-items: stretch; }
            .btn-submit, .btn-cancel { justify-content: center; }
        }

        @media (min-width: 1200px) {
            .form-content { max-width: 1000px; margin: 0 auto; }
        }
    </style>

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Buat Jadwal Baru</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Atur Jadwal Pelajaran</div>
                    <div class="form-subtitle">Tentukan waktu, pengajar, dan kelas untuk sesi pembelajaran baru.</div>
                </div>

                <form action="{{ route('schedules.store') }}" method="POST">
                    @csrf

                    <div class="form-layout">
                        <div class="form-group">
                            <label class="form-label">Hari</label>
                            <select name="day" class="form-select" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>

                        <div class="input-grid-row">
                            <div class="form-group">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="start_time" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="end_time" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Guru Pengajar</label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->nip_nis }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kelas</label>
                            <select name="classroom_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('schedules.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-calendar-plus"></i> Simpan Jadwal
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>