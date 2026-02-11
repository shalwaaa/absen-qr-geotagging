<x-app-layout>
    <style>
        /* Animasi sederhana */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.4s ease-out;
        }

        /* Form lebih lebar */
        .form-content {
            width: 100%;
            max-width: 100%;
            margin: 0;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-title {
            color: #4a6741;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #64748b;
            font-size: 15px;
            line-height: 1.5;
        }

        /* Form layout */
        .form-layout {
            display: flex;
            flex-direction: column;
            gap: 28px;
            width: 100%;
        }

        /* Grid 2 kolom hanya untuk input pendek (digunakan saat role = student) */
        .input-grid-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
            width: 100%;
        }

        @media (min-width: 768px) {
            .input-grid-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .form-group {
            width: 100%;
        }

        .form-label {
            display: block;
            color: #4a6741;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-input {
            width: 100%;
            padding: 14px 18px;
            font-size: 16px;
            color: #1e293b;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.2s ease;
            outline: none;
            box-sizing: border-box;
            height: 48px;
        }

        .form-input:focus {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 14px 18px;
            font-size: 16px;
            color: #1e293b;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.2s ease;
            outline: none;
            cursor: pointer;
            box-sizing: border-box;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
            height: 48px;
        }

        .info-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            padding: 20px;
            font-size: 15px;
            color: #92400e;
            width: 100%;
            box-sizing: border-box;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .info-box strong {
            background: rgba(217, 119, 6, 0.1);
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 700;
        }

        .form-actions {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
            padding-top: 32px;
            border-top: 1px solid #f1f5f9;
            width: 100%;
        }

        .btn-cancel {
            color: #64748b;
            font-weight: 500;
            font-size: 15px;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 10px;
            transition: all 0.2s ease;
            border: 1.5px solid #e2e8f0;
            background: white;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-submit {
            background: #4a6741;
            color: white;
            font-weight: 600;
            font-size: 15px;
            padding: 12px 40px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: #3d5535;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* Responsif */
        @media (max-width: 767px) {
            .form-actions {
                flex-direction: column-reverse;
                gap: 16px;
                align-items: stretch;
            }
            .btn-cancel, .btn-submit {
                width: 100%;
                justify-content: center;
            }
        }

        @media (min-width: 1200px) {
            .form-content {
                max-width: 1000px;
                margin: 0 auto;
            }
        }
    </style>

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">
                    Tambah {{ $type == 'teacher' ? 'Guru' : 'Siswa' }} Baru
                </span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Data {{ $type == 'teacher' ? 'Guru' : 'Siswa' }}</div>
                    <div class="form-subtitle">Lengkapi informasi {{ $type == 'teacher' ? 'guru' : 'siswa' }} baru yang akan ditambahkan ke sistem</div>
                </div>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="{{ $type }}">

                    <div class="form-layout">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-input" placeholder="Masukkan nama lengkap..." required autofocus>
                        </div>

                        @if($type == 'student')
                            <div class="input-grid-row">
                                <div class="form-group">
                                    <label class="form-label">NIS</label>
                                    <input type="number" name="nip_nis" class="form-input" placeholder="Masukkan NIS" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kelas</label>
                                    <select name="classroom_id" class="form-select" required>
                                        <option value=""></option>
                                        @foreach($classrooms as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label class="form-label">NIP</label>
                                <input type="number" name="nip_nis" class="form-input" placeholder="Masukkan NIP" required>
                            </div>
                        @endif

                        @if($type == 'teacher')
                        <p class="text-xs text-gray-500">*Guru Piket bisa membuka kelas guru lain.</p>
                        <div class="mb-4 p-3 bg-yellow-50  border-yellow-200 rounded-lg flex items-center">
                            <input id="is_piket" type="checkbox" name="is_piket" value="1" 
                                   class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer">
                            <label for="is_piket" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                                Tugaskan sebagai Guru Piket / Pengganti?
                            </label>
                        </div>
                        @endif

                        <div class="info-box">
                            <i class="fa-solid fa-key"></i> 
                            <span>Password default untuk akun baru adalah: <strong>smakzie123</strong></span>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('users.index', ['type' => $type]) }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-save"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
</x-app-layout>