<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Container & Header */
        .form-content { width: 100%; max-width: 100%; margin: 0; }
        .form-header { margin-bottom: 40px; }
        .form-title { color: #4a6741; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .form-subtitle { color: #64748b; font-size: 15px; line-height: 1.5; }

        /* Layout */
        .form-layout { display: flex; flex-direction: column; gap: 28px; width: 100%; }
        
        /* Grid Nama & Radius */
        .input-grid-row { display: grid; grid-template-columns: 1fr; gap: 28px; width: 100%; }
        @media (min-width: 768px) {
            .input-grid-row { grid-template-columns: 2fr 1fr; }
        }

        /* Input Styling */
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

        /* Info Box Tips (Versi Kuning Lembut untuk Edit) */
        .info-box-edit {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #92400e;
        }

        /* Map Styling */
        #map { 
            border: 1.5px solid #e2e8f0; 
            border-radius: 12px; 
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
            transition: all 0.2s;
            text-decoration: none;
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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <x-slot name="header">
        <div class="animate-fade-in">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Edit Kelas: {{ $classroom->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <div class="form-content">
                
                <div class="form-header">
                    <div class="form-title">Pembaruan Data Kelas</div>
                    <div class="form-subtitle">Sesuaikan nama, radius, atau pindahkan koordinat presensi kelas ini.</div>
                </div>

                <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-layout">
                        <div class="input-grid-row">
                            <div class="form-group">
                                <label class="form-label">Nama Kelas</label>
                                <input type="text" name="name" value="{{ $classroom->name }}" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Radius (Meter)</label>
                                <input type="number" name="radius_meters" value="{{ $classroom->radius_meters }}" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Lokasi Presensi Saat Ini</label>
                            
                            <div class="info-box-edit">
                                <div style="display: flex; gap: 10px; align-items: start;">
                                    <i class="fa-solid fa-circle-info" style="margin-top: 3px;"></i>
                                    <span>
                                        Geser <strong>pin biru</strong> atau klik di area peta jika ingin mengubah titik pusat presensi untuk kelas ini.
                                    </span>
                                </div>
                            </div>

                            <div id="map" style="height: 450px; width: 100%; z-index: 1;"></div>
                        </div>

                        <input type="hidden" name="latitude" id="latitude" value="{{ $classroom->latitude }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $classroom->longitude }}">

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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        // Ambil koordinat lama dari database
        var savedLat = {{ $classroom->latitude }};
        var savedLng = {{ $classroom->longitude }};

        // Inisialisasi peta fokus ke lokasi yang tersimpan
        var map = L.map('map').setView([savedLat, savedLng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker diletakkan di lokasi tersimpan
        var marker = L.marker([savedLat, savedLng], {
            draggable: true
        }).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        // Search Control
        L.Control.geocoder({ defaultMarkGeocode: false })
            .on('markgeocode', function(e) {
                var center = e.geocode.center;
                marker.setLatLng(center);
                map.setView(center, 16);
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