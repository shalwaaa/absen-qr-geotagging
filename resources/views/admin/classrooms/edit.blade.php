<x-app-layout>
    <!-- LOAD SELECT2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        /* Animasi */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }
        
        .form-content { width: 100%; max-width: 1000px; margin: 0 auto; }
        .form-header { margin-bottom: 40px; }
        .form-title { color: #4a6741; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .form-subtitle { color: #64748b; font-size: 15px; line-height: 1.5; }
        .form-layout { display: flex; flex-direction: column; gap: 28px; width: 100%; }
        
        /* Grid disesuaikan jadi 3 kolom */
        .input-grid-row { display: grid; grid-template-columns: 1fr; gap: 28px; width: 100%; }
        @media (min-width: 768px) { .input-grid-row { grid-template-columns: 1fr 2fr 2fr; } }

        .form-group { width: 100%; }
        .form-label { display: block; color: #4a6741; font-size: 15px; font-weight: 600; margin-bottom: 10px; }
        
        .form-input {
            width: 100%; height: 48px; padding: 12px 16px; font-size: 15px;
            color: #1e293b; background: white; border: 1.5px solid #e2e8f0;
            border-radius: 10px; outline: none; box-sizing: border-box;
        }
        .form-input:focus { border-color: #4a6741; box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1); }
        
        .info-box { 
            background: #f0f7ed; 
            border-left: 4px solid #4a6741; 
            border-radius: 8px; 
            padding: 16px; 
            margin-bottom: 15px; 
            font-size: 14px; 
            color: #3d5535; 
        }
        
        /* Style untuk dua map */
        .map-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .map-item {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .map-item .map-label {
            background: #f8fafc;
            padding: 10px 16px;
            font-weight: 600;
            color: #4a6741;
            border-bottom: 1px solid #e2e8f0;
        }
        .map-item .map-frame {
            height: 300px;
            width: 100%;
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
        
        @media (max-width: 768px) {
            .map-container { grid-template-columns: 1fr; }
        }
    </style>

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
                
                <div class="form-header">
                    <div class="form-title">Update Data Kelas</div>
                    <div class="form-subtitle">Perbarui informasi kelas, wali kelas, dan titik lokasi presensi (Geofencing).</div>
                </div>

                <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-layout">
                        <!-- BARIS INPUT: Tingkat, Nama, Wali Kelas -->
                        <div class="input-grid-row">
                            <!-- Tingkat (Angka) -->
                            <div class="form-group">
                                <label class="form-label">Tingkat (10-12)</label>
                                <input type="number" name="grade_level" value="{{ $classroom->grade_level }}" class="form-input" min="1" max="12" required>
                            </div>

                            <!-- Nama Kelas -->
                            <div class="form-group">
                                <label class="form-label">Nama Kelas</label>
                                <input type="text" name="name" value="{{ $classroom->name }}" class="form-input" required>
                            </div>

                            <!-- Wali Kelas (SELECT2 DENGAN SEARCH) -->
                            <div class="form-group">
                                <label class="form-label">Wali Kelas</label>
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

                        <!-- Radius -->
                        <div class="form-group">
                            <label class="form-label">Radius Absen (Meter) - Berlaku untuk kedua lokasi</label>
                            <input type="number" name="radius_meters" value="{{ $classroom->radius_meters }}" class="form-input" required>
                        </div>

                        <!-- MAP UNTUK DUA LOKASI -->
                        <div class="form-group">
                            <label class="form-label">Titik Lokasi Presensi</label>
                            <div class="info-box">
                                <div style="display: flex; gap: 10px; align-items: start;">
                                    <i class="fa-solid fa-lightbulb" style="margin-top: 3px;"></i>
                                    <span><strong>Tips:</strong> Geser pin biru di masing-masing peta untuk menyesuaikan dua lokasi berbeda. Gunakan tombol cari (🔍) untuk menemukan lokasi dengan cepat.</span>
                                </div>
                            </div>
                            
                            <!-- Container untuk dua map -->
                            <div class="map-container">
                                <!-- Map 1: Lokasi Utama -->
                                <div class="map-item">
                                    <div class="map-label">Lokasi Utama</div>
                                    <div id="map1" class="map-frame"></div>
                                </div>
                                <!-- Map 2: Lokasi Alternatif -->
                                <div class="map-item">
                                    <div class="map-label">Lokasi Alternatif</div>
                                    <div id="map2" class="map-frame"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs untuk menyimpan koordinat kedua lokasi -->
                        <input type="hidden" name="latitude" id="latitude" value="{{ $classroom->latitude }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $classroom->longitude }}">
                        <input type="hidden" name="latitude2" id="latitude2" value="{{ $classroom->latitude2 }}">
                        <input type="hidden" name="longitude2" id="longitude2" value="{{ $classroom->longitude2 }}">

                        <div class="form-actions">
                            <a href="{{ route('classrooms.index') }}" class="btn-cancel">
                                <i class="fa-solid fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-rotate"></i> Update Data Kelas
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
    
    <!-- Script Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    
    <script>
        // Inisialisasi Select2
        $(document).ready(function() {
            $('#teacher-select').select2({
                width: '100%',
                placeholder: 'Cari Nama Wali Kelas...',
                allowClear: true,
                language: { noResults: () => "Tidak ditemukan", searching: () => "Mencari..." }
            });
        });
        
        // Ambil Data dari DB
        var lat1 = {{ $classroom->latitude }};
        var lng1 = {{ $classroom->longitude }};
        
        // Cek apakah lokasi 2 ada? Jika tidak, defaultkan ke lokasi 1 agar map tidak error
        var lat2 = {{ $classroom->latitude2 ?? $classroom->latitude }};
        var lng2 = {{ $classroom->longitude2 ?? $classroom->longitude }};
        
        // --- MAP 1 (UTAMA) ---
        var map1 = L.map('map1').setView([lat1, lng1], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map1);
        var marker1 = L.marker([lat1, lng1], { draggable: true }).addTo(map1);

        // --- MAP 2 (ALTERNATIF) ---
        var map2 = L.map('map2').setView([lat2, lng2], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map2);
        
        // Icon Merah
        var redIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        var marker2 = L.marker([lat2, lng2], { draggable: true, icon: redIcon }).addTo(map2);

        // --- FUNGSI UPDATE INPUTS ---
        function updateInputs() {
            var pos1 = marker1.getLatLng();
            document.getElementById('latitude').value = pos1.lat;
            document.getElementById('longitude').value = pos1.lng;
            
            var pos2 = marker2.getLatLng();
            document.getElementById('latitude2').value = pos2.lat;
            document.getElementById('longitude2').value = pos2.lng;
        }
        
        // Geocoder Map 1
        L.Control.geocoder({ defaultMarkGeocode: false }).on('markgeocode', function(e) {
            var center = e.geocode.center;
            marker1.setLatLng(center); map1.setView(center, 16); updateInputs();
        }).addTo(map1);

        // Geocoder Map 2
        L.Control.geocoder({ defaultMarkGeocode: false }).on('markgeocode', function(e) {
            var center = e.geocode.center;
            marker2.setLatLng(center); map2.setView(center, 16); updateInputs();
        }).addTo(map2);

        // Event Listeners
        marker1.on('dragend', updateInputs);
        map1.on('click', function(e) { marker1.setLatLng(e.latlng); updateInputs(); });

        marker2.on('dragend', updateInputs);
        map2.on('click', function(e) { marker2.setLatLng(e.latlng); updateInputs(); });
        
        // Init Input Value
        updateInputs();
    </script>
</x-app-layout>