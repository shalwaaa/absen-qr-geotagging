<x-app-layout>
    <style>
        /* Style Dasar (Sama seperti sebelumnya) */
        .form-content { width: 100%; max-width: 1000px; margin: 0 auto; }
        .input-grid-row { display: grid; grid-template-columns: 1fr; gap: 28px; width: 100%; }
        @media (min-width: 768px) { .input-grid-row { grid-template-columns: 1fr 2fr 2fr; } }
        
        .form-label { display: block; color: #4a6741; font-size: 15px; font-weight: 600; margin-bottom: 10px; }
        .form-input { width: 100%; height: 48px; padding: 14px 18px; border: 1.5px solid #e2e8f0; border-radius: 10px; outline: none; transition: 0.2s; }
        .form-input:focus { border-color: #4a6741; box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1); }
        
        .btn-submit { background: #4a6741; color: white; padding: 12px 40px; border-radius: 10px; cursor: pointer; border: none; font-weight: 600; transition: 0.2s; }
        .btn-submit:hover { background: #3d5535; transform: translateY(-2px); }

        /* Info Box untuk Petunjuk */
        .info-box {
            background: #f0f7ed;
            border-left: 4px solid #4a6741;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #3d5535;
            display: flex; align-items: center; gap: 10px;
        }
    </style>

    <!-- 1. Load CSS Leaflet & Geocoder (Wajib ada) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Kelas</h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; flex-direction: column; gap: 28px;">
                        
                        <div class="input-grid-row">
                            <div>
                                <label class="block font-bold mb-2 text-[#4a6741]">Tingkat</label>
                                <input type="number" name="grade_level" value="{{ $classroom->grade_level }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="block font-bold mb-2 text-[#4a6741]">Nama Kelas</label>
                                <input type="text" name="name" value="{{ $classroom->name }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="block font-bold mb-2 text-[#4a6741]">Wali Kelas</label>
                                <select name="homeroom_teacher_id" class="form-input">
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}" {{ $classroom->homeroom_teacher_id == $t->id ? 'selected' : '' }}>
                                            {{ $t->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold mb-2 text-[#4a6741]">Radius (Meter)</label>
                            <input type="number" name="radius_meters" value="{{ $classroom->radius_meters }}" class="form-input" required>
                        </div>

                        <div>
                            <label class="block font-bold mb-2 text-[#4a6741]">Lokasi Sekolah</label>
                            
                            <!-- Petunjuk -->
                            <div class="info-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span>Gunakan tombol <strong>Cari (🔍)</strong> di pojok kanan atas peta untuk menemukan lokasi sekolah dengan cepat.</span>
                            </div>

                            <div id="map" style="height: 450px; width: 100%; border-radius: 12px; z-index: 1;"></div>
                        </div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ $classroom->latitude }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $classroom->longitude }}">

                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn-submit">Update Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Load Script JS Leaflet & Geocoder -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        // Ambil data Lat/Long dari database
        var savedLat = {{ $classroom->latitude }};
        var savedLng = {{ $classroom->longitude }};

        // Inisialisasi Peta
        var map = L.map('map').setView([savedLat, savedLng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
        
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