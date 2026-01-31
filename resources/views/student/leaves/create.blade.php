<x-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly:   #2D5128;
            --fern:       #537B2F;
            --asparagus:  #8DA750;
            --cream:      #FAFAF5;
        }

        /* Card Style */
        .form-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            border: 1px solid #f0fdf4;
            box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
        }

        /* Input Style */
        .form-label {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--dark-green);
            margin-bottom: 8px;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            border: 2px solid #eef2eb;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.95rem;
            transition: 0.3s;
            outline: none;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--fern);
            background-color: #fcfdfa;
        }

        /* Button Submit */
        .btn-submit {
            background: var(--cal-poly);
            color: white;
            width: 100%;
            padding: 14px;
            border-radius: 16px;
            font-weight: 800;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: var(--fern);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45,81,40,0.2);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark-green);
            margin-bottom: 5px;
        }

        /* --- STYLE BARU UNTUK PILIHAN RADIO --- */
        .selection-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            border: 2px solid #e2e8f0; /* Abu-abu default */
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            color: #64748b; /* Text abu */
        }

        /* Efek Hover (Sebelum diklik) */
        .selection-box:hover {
            border-color: var(--asparagus);
            background-color: #f7fee7; /* Hijau sangat muda */
            color: var(--dark-green);
            transform: translateY(-2px);
        }

        /* Efek Checked (Setelah diklik) - MENYALA */
        input[type="radio"]:checked + .selection-box {
            background-color: var(--fern); /* Hijau Pekat */
            border-color: var(--fern);
            color: white; /* Teks jadi Putih */
            box-shadow: 0 8px 15px -3px rgba(83, 123, 47, 0.4);
            transform: translateY(-2px);
        }

        /* Icon didalam box */
        .selection-box i {
            font-size: 1.5rem;
            margin-bottom: 8px;
            transition: 0.3s;
        }
        
        /* Saat checked, icon sedikit membesar */
        input[type="radio"]:checked + .selection-box i {
            transform: scale(1.1);
        }
    </style>

    <div class="py-8 px-4 sm:px-6 lg:px-8 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-md mx-auto">
            
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="page-title">Ajukan Izin</h1>
                    <p class="text-sm text-gray-500">Isi formulir di bawah ini dengan benar.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>

            <div class="form-card">
                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Jenis Izin (STYLE BARU) -->
                    <div class="mb-6">
                        <label class="form-label">Pilih Jenis Pengajuan</label>
                        <div class="grid grid-cols-2 gap-4">
                            
                            <!-- Opsi Sakit -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="type" value="sick" class="peer sr-only" required>
                                <div class="selection-box">
                                    <i class="fa-solid fa-stethoscope"></i>
                                    <span class="font-bold text-sm">Sakit</span>
                                </div>
                            </label>

                            <!-- Opsi Izin -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="type" value="permission" class="peer sr-only" required>
                                <div class="selection-box">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                    <span class="font-bold text-sm">Izin / Keperluan</span>
                                </div>
                            </label>

                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-input" required>
                        </div>
                    </div>

                    <!-- Alasan -->
                    <div class="mb-5">
                        <label class="form-label">Alasan / Keterangan</label>
                        <textarea name="reason" rows="3" class="form-textarea" placeholder="Contoh: Demam tinggi, ada acara keluarga..." required></textarea>
                    </div>

                    <!-- Upload File -->
                    <div class="mb-6">
                        <label class="form-label">Bukti Foto / Surat (Opsional)</label>
                        <div class="relative">
                            <input type="file" name="attachment" accept="image/*" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-xs file:font-bold
                                file:bg-[#f0fdf4] file:text-[var(--fern)]
                                hover:file:bg-[#dcfce7]
                                file:cursor-pointer cursor-pointer
                            "/>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-circle-info"></i> Maksimal 2MB (JPG/PNG)
                        </p>
                    </div>

                    <button type="submit" class="btn-submit">
                        KIRIM PENGAJUAN <i class="fa-solid fa-paper-plane ml-2"></i>
                    </button>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>