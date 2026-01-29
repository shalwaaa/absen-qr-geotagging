<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Typography & Detail Info */
        .detail-label { color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; margin-bottom: 4px; display: block; }
        .detail-value { color: #1e293b; font-size: 18px; font-weight: 700; margin-bottom: 24px; display: block; }
        .detail-badge { background: #f1f5f9; color: #475569; font-family: ui-monospace, monospace; padding: 4px 10px; border-radius: 6px; font-size: 14px; }
        
        .radius-info {
            background: #f0f7ed;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid rgba(74, 103, 65, 0.1);
        }
        .radius-number { font-size: 28px; font-weight: 800; color: #4a6741; line-height: 1; }
        .radius-text { font-size: 14px; color: #3d5535; line-height: 1.4; }

        /* Map Styling */
        #map { 
            border-radius: 16px; 
            overflow: hidden; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            z-index: 1; /* Memastikan peta tidak menimpa dropdown menu */
        }

        /* Buttons */
        .btn-secondary {
            background: white;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-secondary:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

        .btn-edit {
            background: #4a6741;
            color: white;
            padding: 10px 28px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-edit:hover { background: #3d5535; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(74, 103, 65, 0.2); }

        .section-title { color: #4a6741; font-weight: 800; font-size: 18px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
    </style>

    <x-slot name="header">
        <div class="animate-fade-in flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Detail Kelas: {{ $classroom->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12 animate-fade-in">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl">
                <div class="p-8">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                        
                        <div class="lg:col-span-4">
                            <h3 class="section-title">
                                <i class="fa-solid fa-circle-info"></i> Ringkasan Data
                            </h3>

                            <div>
                                <span class="detail-label">Nama Kelas</span>
                                <span class="detail-value text-2xl" style="color: #4a6741;">{{ $classroom->name }}</span>

                                <span class="detail-label">Kode Unik Sistem</span>
                                <div class="mb-6">
                                    <span class="detail-badge">UID-{{ str_pad($classroom->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>

                                <span class="detail-label">Geofencing Radius</span>
                                <div class="radius-info mb-6">
                                    <div class="radius-number">{{ $classroom->radius_meters }}m</div>
                                    <div class="radius-text">
                                        Jangkauan absensi siswa dari titik pusat lokasi.
                                    </div>
                                </div>

                                <span class="detail-label">Koordinat GPS</span>
                                <span class="detail-value font-mono text-sm">{{ $classroom->latitude }}, {{ $classroom->longitude }}</span>
                            </div>

                            <div class="mt-10 flex flex-wrap gap-3">
                                <a href="{{ route('classrooms.index') }}" class="btn-secondary">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                </a>
                                <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Kelas
                                </a>
                            </div>
                        </div>

                        <div class="lg:col-span-8">
                            <h3 class="section-title">
                                <i class="fa-solid fa-map-location-dot"></i> Visualisasi Jangkauan Absensi
                            </h3>
                            <div id="map" style="height: 500px; width: 100%;"></div>
                        </div>

                    </div> 
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Data dari Laravel
        var lat = {{ $classroom->latitude }};
        var lng = {{ $classroom->longitude }};
        var radius = {{ $classroom->radius_meters }};

        // Inisialisasi Peta
        var map = L.map('map', {
            center: [lat, lng],
            zoom: 17,
            dragging: true,    
            scrollWheelZoom: false 
        });

        // Memuat Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Tambah Marker
        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>Titik Pusat {{ $classroom->name }}</b>")
            .openPopup();

        // Tambah Lingkaran Radius (Geofence)
        L.circle([lat, lng], {
            color: '#4a6741',       
            fillColor: '#4a6741', 
            fillOpacity: 0.15,     
            weight: 2,
            radius: radius        
        }).addTo(map);

        // 2. PERBAIKAN KEDUA: Memastikan peta menghitung ulang ukuran setelah div muncul (mencegah kotak-kotak)
        setTimeout(function() {
            map.invalidateSize();
        }, 500);
    </script>
</x-app-layout>