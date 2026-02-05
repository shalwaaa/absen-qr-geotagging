<x-app-layout>
    <!-- LOAD SELECT2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* Style Dasar (Sama seperti sebelumnya) */
        .form-content { width: 100%; max-width: 1000px; margin: 0 auto; }
        .input-grid-row { display: grid; grid-template-columns: 1fr; gap: 28px; width: 100%; }
        @media (min-width: 768px) { .input-grid-row { grid-template-columns: 1fr 2fr 2fr; } }
        
        .form-label { display: block; color: #4a6741; font-size: 15px; font-weight: 600; margin-bottom: 10px; }
        .form-input { 
            width: 100%; 
            height: 48px; 
            padding: 14px 18px; 
            border: 1.5px solid #e2e8f0; 
            border-radius: 10px; 
            outline: none; 
            transition: 0.2s; 
            font-size: 15px;
            color: #1e293b;
            background: white;
        }
        .form-input:focus { border-color: #4a6741; box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1); }
        
        .btn-submit { 
            background: #4a6741; 
            color: white; 
            padding: 12px 40px; 
            border-radius: 10px; 
            cursor: pointer; 
            border: none; 
            font-weight: 600; 
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-submit:hover { 
            background: #3d5535; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2);
        }

        /* Info Box untuk Petunjuk */
        .info-box {
            background: #f0f7ed;
            border-left: 4px solid #4a6741;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #3d5535;
            display: flex; 
            align-items: center; 
            gap: 10px;
        }

        /* Tombol Cancel */
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
            margin-right: 15px;
        }
        
        .btn-cancel:hover {
            background: #f8fafc;
        }

        .form-actions { 
            display: flex; 
            justify-content: flex-start; 
            align-items: center; 
            gap: 20px; 
            margin-top: 10px; 
            padding-top: 32px; 
            border-top: 1px solid #f1f5f9; 
        }

        /* --- CUSTOM STYLE SELECT2 --- */
        .select2-container .select2-selection--single {
            height: 48px !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 10px !important;
            display: flex; 
            align-items: center;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important; 
            font-size: 15px; 
            padding-left: 16px;
            line-height: 46px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important; 
            right: 10px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #64748b !important;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4a6741 !important; 
            color: white !important;
        }
        
        /* Fokus State */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4a6741 !important;
            box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1);
        }
        
        /* Dropdown styling */
        .select2-container--default .select2-results > .select2-results__options {
            max-height: 300px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
        }

        /* Animasi */
        @keyframes fadeInUp { 
            from { opacity: 0; transform: translateY(10px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }
    </style>

    <!-- 1. Load CSS Leaflet & Geocoder (Wajib ada) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Edit Kelas</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; flex-direction: column; gap: 28px;">
                        
                        <div class="input-grid-row">
                            <div>
                                <label class="form-label">Tingkat</label>
                                <input type="number" name="grade_level" value="{{ $classroom->grade_level }}" 
                                       class="form-input" min="1" max="12" required>
                            </div>
                            <div>
                                <label class="form-label">Nama Kelas</label>
                                <input type="text" name="name" value="{{ $classroom->name }}" 
                                       class="form-input" placeholder="Contoh: X-RPL-1" required>
                            </div>
                            <div>
                                <label class="form-label">Wali Kelas</label>
                                <!-- GANTI MENJADI SELECT2 DENGAN SEARCH -->
                                <select name="homeroom_teacher_id" class="select2 w-full" id="teacher-select">
                                    <option value="">Cari Nama Wali Kelas...</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}" {{ $classroom->homeroom_teacher_id == $t->id ? 'selected' : '' }}>
                                            {{ $t->name }} ({{ $t->nip_nis }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Radius (Meter)</label>
                            <input type="number" name="radius_meters" value="{{ $classroom->radius_meters }}" 
                                   class="form-input" required>
                        </div>

                        <div>
                            <label class="form-label">Lokasi Sekolah</label>
                            
                            <!-- Petunjuk -->
                            <div class="info-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span>Gunakan tombol <strong>Cari (🔍)</strong> di pojok kanan atas peta untuk menemukan lokasi sekolah dengan cepat.</span>
                            </div>

                            <div id="map" style="height: 450px; width: 100%; border-radius: 12px; border: 1.5px solid #e2e8f0; overflow: hidden; z-index: 1;"></div>
                        </div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ $classroom->latitude }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $classroom->longitude }}">

                        <div class="form-actions">
                            <a href="{{ route('classrooms.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-save"></i> Update Data Kelas
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
    
    <!-- 2. Load Script JS Leaflet & Geocoder -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        // Inisialisasi Select2 untuk dropdown wali kelas
        $(document).ready(function() {
            $('#teacher-select').select2({
                width: '100%',
                placeholder: 'Cari Nama Wali Kelas...',
                allowClear: true,
                minimumInputLength: 0,
                language: {
                    noResults: function() {
                        return "Tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        });

        // Ambil data Lat/Long dari database
        var savedLat = {{ $classroom->latitude }};
        var savedLng = {{ $classroom->longitude }};

        // Inisialisasi Peta
        var map = L.map('map').setView([savedLat, savedLng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
            attribution: '&copy; OpenStreetMap' 
        }).addTo(map);
        
        // Marker Awal
        var marker = L.marker([savedLat, savedLng], { draggable: true }).addTo(map);

        // Fungsi Update Input Hidden
        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        // --- FITUR PENCARIAN (GEOCODER) ---
        L.Control.geocoder({
            defaultMarkGeocode: false // Jangan buat marker ganda otomatis
        })
        .on('markgeocode', function(e) {
            var center = e.geocode.center;
            
            // 1. Pindahkan Marker
            marker.setLatLng(center);
            
            // 2. Pindahkan Pandangan Peta
            map.setView(center, 17);
            
            // 3. Update Input Hidden
            updateInputs(center.lat, center.lng);
        })
        .addTo(map);

        // Event saat Marker Digeser Manual
        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });
        
        // Event saat Peta Diklik
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
    </script>
</x-app-layout>