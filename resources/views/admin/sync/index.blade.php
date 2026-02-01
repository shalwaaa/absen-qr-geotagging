<x-app-layout>
    <x-slot name="header"></x-slot>
    
    <style>
        .card-sync { background: white; border-radius: 16px; padding: 30px; border: 1px solid #f0fdf4; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
        .btn-sync { background: #2D5128; color: white; width: 100%; padding: 12px; border-radius: 10px; font-weight: bold; transition: 0.2s; }
        .btn-sync:hover { background: #537B2F; transform: translateY(-2px); }
    </style>

    <div class="py-12 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-[#142C14]">Sinkronisasi Data Pusat</h1>
            <p class="text-gray-500 mt-2">Tarik data API (Guru, Kelas, Siswa) dan masukkan ke Folder Tahun Ajar.</p>
        </div>

        <div class="card-sync">
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 text-sm font-bold border border-green-200 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 text-sm font-bold border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('sync.process') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block font-bold text-[#142C14] mb-2">Pilih Folder Tahun Ajaran (Target)</label>
                    <div class="relative">
                        <select name="academic_year_id" class="w-full border-2 border-gray-200 rounded-xl p-3 pl-10 focus:border-[#537B2F] outline-none appearance-none bg-white">
                            @foreach($years as $y)
                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-folder absolute left-4 top-4 text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 ml-1">
                        *Tahun ajar otomatis digenerate dari 2020 s.d Tahun Depan.
                    </p>
                </div>

                <div class="bg-yellow-50 p-4 rounded-xl mb-8 border border-yellow-100">
                    <h4 class="font-bold text-yellow-800 text-sm mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-server"></i> Info Sinkronisasi:
                    </h4>
                    <ul class="text-xs text-yellow-700 list-disc ml-5 space-y-1">
                        <li>Data akan diambil dari API Sekolah berdasarkan 4 digit tahun awal.</li>
                        <li>Contoh: Pilih <strong>2025/2026</strong> -> API param <strong>?tahun=2025</strong>.</li>
                        <li>Data Siswa akan disimpan sebagai <strong>History</strong> di tahun tersebut.</li>
                    </ul>
                </div>

                <button type="submit" class="btn-sync flex justify-center items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-down"></i> TARIK DATA SEKARANG
                </button>
            </form>
        </div>
    </div>
</x-app-layout>