<x-app-layout>
    <!-- Style CSS kamu tetap sama -->
    <style>
        /* ... Style yang sama persis seperti sebelumnya ... */
        /* Saya persingkat disini biar gak kepanjangan, copy style dari pesanmu sebelumnya */
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
        .form-input { width: 100%; height: 48px; padding: 14px 18px; font-size: 16px; color: #1e293b; background: white; border: 1.5px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; outline: none; box-sizing: border-box; }
        .form-input:focus { border-color: #4a6741; box-shadow: 0 0 0 4px rgba(74, 103, 65, 0.1); }
        .info-box { background: #f0f7ed; border-left: 4px solid #4a6741; border-radius: 8px; padding: 16px; margin-bottom: 15px; font-size: 14px; color: #3d5535; }
        #map { border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-actions { display: flex; justify-content: flex-start; align-items: center; gap: 20px; margin-top: 10px; padding-top: 32px; border-top: 1px solid #f1f5f9; }
        .btn-cancel { color: #64748b; font-weight: 500; padding: 12px 32px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: white; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s; }
        .btn-submit { background: #4a6741; color: white; font-weight: 600; padding: 12px 40px; border: none; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s; }
        .btn-submit:hover { background: #3d5535; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2); }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Tambah Kelas Baru</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Konfigurasi Kelas & Lokasi</div>
                    <div class="form-subtitle">Atur identitas kelas, wali kelas, dan titik lokasi presensi siswa.</div>
                </div>

                <form action="{{ route('classrooms.store') }}" method="POST">
                    @csrf

                    <div class="form-layout">
                        <!-- BARIS INPUT: Tingkat, Nama, Wali Kelas -->
                        <div class="input-grid-row">
                            <!-- Tingkat (Angka) -->
                            <div class="form-group">
                                <label class="form-label">Tingkat (10-12)</label>
                                <input type="number" name="grade_level" class="form-input" placeholder="10" min="1" max="12" required>
                            </div>

                            <!-- Nama Kelas -->
                            <div class="form-group">
                                <label class="form-label">Nama Kelas</label>
                                <input type="text" name="name" class="form-input" placeholder="Contoh: X-RPL-1" required>
                            </div>

                            <!-- Wali Kelas (Dropdown) -->
                            <div class="form-group">
                                <label class="form-label">Wali Kelas</label>
                                <select name="homeroom_teacher_id" class="form-input">
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->nip_nis }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Radius -->
                        <div class="form-group">
                            <label class="form-label">Radius Absen (Meter)</label>
                            <input type="number" name="radius_meters" value="50" class="form-input" required>
                        </div>

                        <!-- MAP -->
                        <div class="form-group">
                            <label class="form-label">Tentukan Titik Presensi</label>
                            <div class="info-box">
                                <div style="display: flex; gap: 10px; align-items: start;">
                                    <i class="fa-solid fa-lightbulb" style="margin-top: 3px;"></i>
                                    <span><strong>Tips:</strong> Geser pin biru ke lokasi kelas/sekolah.</span>
                                </div>
                            </div>
                            <div id="map" style="height: 450px; width: 100%; z-index: 1;"></div>
                        </div>

                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        <div class="form-actions">
                            <a href="{{ route('classrooms.index') }}" class="btn-cancel">Batal</a>
                            <button type="submit" class="btn-submit">Simpan Data Kelas</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Map sama persis seperti sebelumnya -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        var map = L.map('map').setView([-6.1753924, 106.8271528], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
        var marker = L.marker([-6.1753924, 106.8271528], { draggable: true }).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }
        updateInputs(-6.1753924, 106.8271528);

        L.Control.geocoder({ defaultMarkGeocode: false })
            .on('markgeocode', function(e) {
                var center = e.geocode.center;
                marker.setLatLng(center);
                map.fitBounds(e.geocode.bbox);
                updateInputs(center.lat, center.lng);
            })
            .addTo(map);

        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
    </script>
</x-app-layout>