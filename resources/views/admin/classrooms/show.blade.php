<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Kartu */
        .info-card { background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
        
        /* Label & Value */
        .info-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .info-value { font-size: 16px; font-weight: 600; color: #1e293b; }
        .info-group { margin-bottom: 24px; }

        /* Table Siswa */
        .student-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .student-table th { text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; font-weight: 700; }
        .student-table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
        .student-table tr:hover { background: #fcfdfa; }

        /* Badge Wali Kelas */
        .badge-teacher { background: #fffbeb; color: #b45309; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #fcd34d; }

        .back-btn { display: inline-flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .back-btn:hover { color: #4a6741; transform: translateX(-3px); }

        /* Lokasi badge */
        .location-badge {
            background: #f0f7ed;
            border-left: 4px solid #4a6741;
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 8px;
            font-size: 13px;
            color: #2d4a2d;
            display: flex; align-items: center; gap: 8px;
        }

        /* Container untuk dua peta */
        .maps-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 10px;
            height: 100%;
            min-height: 400px;
        }
        .map-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }
        .map-header {
            background: #f8fafc;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 13px;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; gap: 6px;
        }
        .map-frame {
            flex: 1;
            min-height: 300px;
            width: 100%;
        }
        .map-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: #f8fafc;
            color: #94a3b8;
            font-style: italic;
            padding: 20px;
            text-align: center;
            flex-direction: column;
            gap: 10px;
        }
        
        @media (max-width: 768px) {
            .maps-container { grid-template-columns: 1fr; }
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <x-slot name="header">
        <div class="flex justify-between items-center animate-fade-in">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C;">Detail Kelas: {{ $classroom->name }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- BARIS ATAS: INFO KELAS (KIRI) & PETA (KANAN) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KOLOM 1: INFO DETAIL -->
                <div class="lg:col-span-1">
                    <div class="info-card flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-[#4a6741] mb-6 border-b pb-2 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info"></i> Informasi Umum
                            </h3>
                            
                            <div class="info-group">
                                <div class="info-label">Nama Kelas</div>
                                <div class="info-value text-xl">{{ $classroom->name }}</div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Tingkat Pendidikan</div>
                                <div class="info-value">Kelas {{ $classroom->grade_level }}</div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Wali Kelas</div>
                                @if($classroom->homeroomTeacher)
                                    <div class="badge-teacher mt-1">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                        {{ $classroom->homeroomTeacher->name }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Belum ditentukan</span>
                                @endif
                            </div>

                            <div class="info-group">
                                <div class="info-label">Radius Absensi</div>
                                <div class="info-value">{{ $classroom->radius_meters }} Meter</div>
                            </div>

                            <!-- Lokasi Utama -->
                            <div class="info-group">
                                <div class="info-label">Lokasi Utama</div>
                                <div class="location-badge">
                                    <i class="fa-solid fa-location-dot" style="color: #2D5128;"></i>
                                    <span>{{ number_format($classroom->latitude, 6) }}, {{ number_format($classroom->longitude, 6) }}</span>
                                </div>
                            </div>

                            <!-- Lokasi Alternatif (jika ada) -->
                            @if($classroom->latitude2 && $classroom->longitude2)
                                <div class="info-group">
                                    <div class="info-label">Lokasi Alternatif</div>
                                    <div class="location-badge" style="border-left-color: #d97706; background: #fffbeb; color: #92400e;">
                                        <i class="fa-solid fa-location-dot" style="color: #d97706;"></i>
                                        <span>{{ number_format($classroom->latitude2, 6) }}, {{ number_format($classroom->longitude2, 6) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t mt-4">
                            <a href="{{ route('classrooms.edit', $classroom->id) }}" class="block w-full text-center py-3 rounded-lg bg-yellow-50 text-yellow-700 font-bold hover:bg-yellow-100 transition border border-yellow-200">
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Data Kelas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- KOLOM 2: DUA PETA LOKASI -->
                <div class="lg:col-span-2">
                    <div class="info-card p-0 overflow-hidden relative">
                        <div class="absolute top-4 left-4 z-[400] bg-white px-3 py-1 rounded-md shadow-md border border-gray-200 text-xs font-bold text-gray-600">
                            <i class="fa-solid fa-map-location-dot mr-1"></i> Peta Lokasi
                        </div>
                        
                        <!-- Container dua peta -->
                        <div class="maps-container">
                            <!-- Peta 1: Lokasi Utama -->
                            <div class="map-item">
                                <div class="map-header" style="color: #2D5128;">
                                    <i class="fa-solid fa-location-dot"></i> Lokasi Utama
                                </div>
                                <div id="map1" class="map-frame"></div>
                            </div>
                            
                            <!-- Peta 2: Lokasi Alternatif -->
                            <div class="map-item">
                                <div class="map-header" style="color: #d97706;">
                                    <i class="fa-solid fa-location-dot"></i> Lokasi Alternatif
                                </div>
                                <div id="map2" class="map-frame"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BARIS BAWAH: DAFTAR SISWA (FULL WIDTH) -->
            <div class="info-card">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-50 rounded-lg text-green-700">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-[#4a6741]">Daftar Siswa (Rombel)</h3>
                            <p class="text-xs text-gray-400">Siswa aktif yang terdaftar di kelas ini.</p>
                        </div>
                    </div>
                    
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-4 py-2 rounded-full border border-green-200">
                        Total: {{ $classroom->students->where('role', 'student')->count() }} Siswa
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="student-table">
                        <thead>
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Nama Lengkap</th>
                                <th>NIS / NISN</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classroom->students->where('role', 'student')->sortBy('name') as $index => $student)
                                <tr>
                                    <td class="text-center text-gray-400 font-mono">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 border border-gray-200">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <span class="font-bold text-gray-700">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono text-gray-500">{{ $student->nip_nis ?? '-' }}</td>
                                    <td>
                                        <span class="text-[10px] uppercase font-bold text-green-600 bg-green-50 px-2 py-1 rounded border border-green-100">
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('users.edit', $student->id) }}" class="text-xs font-bold text-blue-600 hover:underline">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center opacity-40">
                                            <i class="fa-solid fa-users-slash text-4xl mb-3 text-gray-300"></i>
                                            <p class="text-gray-600 font-medium">Belum ada siswa di kelas ini.</p>
                                            <p class="text-xs text-gray-400 mt-1">Gunakan menu 'User' atau 'Kenaikan Kelas' untuk menambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Peta -->
    <!-- Script Peta -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var lat = {{ $classroom->latitude }};
        var lng = {{ $classroom->longitude }};
        
        // Pastikan nilai float, bukan string kosong
        var lat2 = {{ $classroom->latitude2 ?? 'null' }};
        var lng2 = {{ $classroom->longitude2 ?? 'null' }};
        
        // Default radius 50m jika kosong
        var radius = {{ $classroom->radius_meters ?? 50 }};

        // --- MAP 1 (UTAMA) ---
        var map1 = L.map('map1').setView([lat, lng], 18); // Zoom 18 biar radius kelihatan
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map1);
        
        var marker1 = L.marker([lat, lng]).addTo(map1)
            .bindPopup("<b>Lokasi Utama</b><br>" + lat.toFixed(6) + ", " + lng.toFixed(6)).openPopup();
        
        var circle1 = L.circle([lat, lng], {
            color: '#2D5128',
            fillColor: '#2D5128',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(map1).bindPopup("Radius Absen: " + radius + " meter");
        
        // Fit Bounds agar lingkaran terlihat full
        map1.fitBounds(circle1.getBounds());

        // --- MAP 2 (ALTERNATIF) ---
        var map2El = document.getElementById('map2');
        
        if (lat2 !== null && lng2 !== null) {
            var map2 = L.map('map2').setView([lat2, lng2], 18);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map2);
            
            var redIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var marker2 = L.marker([lat2, lng2], {icon: redIcon}).addTo(map2)
                .bindPopup("<b>Lokasi Alternatif</b><br>" + lat2.toFixed(6) + ", " + lng2.toFixed(6)).openPopup();
            
            var circle2 = L.circle([lat2, lng2], {
                color: '#d97706',
                fillColor: '#d97706',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(map2).bindPopup("Radius Absen: " + radius + " meter");

            // Fit Bounds agar lingkaran kedua juga terlihat full
            map2.fitBounds(circle2.getBounds());

        } else {
            // Placeholder
            map2El.innerHTML = `
                <div class="map-placeholder">
                    <i class="fa-solid fa-map-location-dot text-4xl text-gray-300"></i>
                    <p>Lokasi alternatif belum diatur.</p>
                </div>
            `;
            map2El.classList.remove('map-frame');
            map2El.style.height = '100%';
        }
    </script>
</x-app-layout>