<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Container & Header Style */
        .form-content { width: 100%; max-width: 100%; margin: 0; }
        .form-header { margin-bottom: 40px; }
        .form-title { color: #4a6741; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .form-subtitle { color: #64748b; font-size: 15px; line-height: 1.5; }

        /* Form Layout */
        .form-layout { display: flex; flex-direction: column; gap: 28px; width: 100%; }
        
        /* Grid 2 kolom khusus Siswa (NIP + Kelas) */
        .input-grid-row { display: grid; grid-template-columns: 1fr; gap: 28px; width: 100%; }
        @media (min-width: 768px) {
            .input-grid-row { grid-template-columns: repeat(2, 1fr); }
        }

        /* Input Styling */
        .form-group { width: 100%; }
        .form-label { display: block; color: #4a6741; font-size: 15px; font-weight: 600; margin-bottom: 10px; }
        
        .form-input, .form-select {
            width: 100%;
            height: 48px;
            padding: 14px 18px;
            font-size: 16px;
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

        /* Select arrow styling */
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
        }

        /* Info/Alert Box */
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
            font-size: 14px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
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

        @media (min-width: 1200px) {
            .form-content { max-width: 1000px; margin: 0 auto; }
        }
    </style>

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">
                    Edit Data {{ $user->role == 'teacher' ? 'Guru' : 'Siswa' }}
                </span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Update Profil</div>
                    <div class="form-subtitle">Perbarui informasi data {{ $user->role == 'teacher' ? 'guru' : 'siswa' }} yang terdaftar di sistem</div>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-layout">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-input" required>
                        </div>

                        @if($user->role == 'student')
                            <div class="input-grid-row">
                                <div class="form-group">
                                    <label class="form-label">NIS</label>
                                    <input type="number" name="nip_nis" value="{{ $user->nip_nis }}" class="form-input" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kelas</label>
                                    <select name="classroom_id" class="form-select" required>
                                        @foreach($classrooms as $c)
                                            <option value="{{ $c->id }}" {{ $user->classroom_id == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label class="form-label">NIP</label>
                                <input type="number" name="nip_nis" value="{{ $user->nip_nis }}" class="form-input" required>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Ganti Password</label>
                            <input type="password" name="password" class="form-input" placeholder="Isi hanya jika ingin mengganti password">
                            <div class="info-box">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Biarkan kosong jika tidak ingin mengubah kata sandi saat ini.</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('users.index', ['type' => $user->role]) }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-rotate"></i> Update Data
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>