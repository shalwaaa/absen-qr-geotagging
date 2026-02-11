<x-app-layout>
    <style>
        /* Animasi masuk */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Style Container */
        .form-content { width: 100%; max-width: 100%; margin: 0; }
        .form-header { margin-bottom: 40px; }
        .form-title { color: #4a6741; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .form-subtitle { color: #64748b; font-size: 15px; line-height: 1.5; }

        /* Layout Form */
        .form-layout { display: flex; flex-direction: column; gap: 28px; width: 100%; }

        /* Grup Input */
        .form-group { width: 100%; }
        .form-label { display: block; color: #4a6741; font-size: 15px; font-weight: 600; margin-bottom: 10px; }
        
        .form-input {
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

        .form-input:focus {
            border-color: #4a6741;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }

        /* Action Buttons */
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
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #f8fafc;
            color: #4a6741;
            border-color: #cbd5e1;
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
                    Tambah Mata Pelajaran
                </span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Data Mata Pelajaran</div>
                    <div class="form-subtitle">Tambahkan mata pelajaran baru untuk kurikulum sekolah</div>
                </div>

                <form action="{{ route('subjects.store') }}" method="POST">
                    @csrf

                    <div class="form-layout">
                        <div class="form-group">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="name" class="form-input" placeholder="Contoh: Matematika Wajib" required autofocus>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kode Mapel (Opsional)</label>
                            <input type="text" name="code" class="form-input" placeholder="Contoh: MTK-01">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tingkat Kelas</label>
                            <select name="grade_level" class="form-input">
                                <option value="10">Kelas 10</option>
                                <option value="11">Kelas 11</option>
                                <option value="12">Kelas 12</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('subjects.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-save"></i> Simpan Mata Pelajaran
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>