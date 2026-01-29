<x-app-layout>
    <style>
        /* Animasi */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease-out; }

        /* Scanner Container */
        #reader { 
            border: none !important; 
            overflow: hidden; 
            border-radius: 20px;
            background: #000;
        }
        
        /* Overriding Html5Qrcode CSS */
        #reader__dashboard_section_csr button {
            background-color: #4a6741 !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-weight: 600 !important;
            border: none !important;
        }

        .scan-frame {
            position: relative;
            background: white;
            padding: 10px;
            border-radius: 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .status-loading { background: #fef3c7; color: #92400e; }
        .status-success { background: #f0f7ed; color: #4a6741; }
        .status-error { background: #fee2e2; color: #b91c1c; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-weight: 600;
            transition: color 0.2s;
        }
        .btn-back:hover { color: #1e293b; }
    </style>

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight animate-fade-in">
            <span style="color: #4a6741;">Presensi:</span> Scan QR Code
        </h2>
    </x-slot>

    <div class="py-8 animate-fade-in">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl p-6 border border-gray-100">
                
                <div class="text-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Arahkan Kamera</h3>
                    <p class="text-sm text-gray-500 mt-1">Posisikan kode QR tepat di dalam kotak pemindai</p>
                </div>

                <div class="scan-frame mb-6">
                    <div id="reader" style="width: 100%;"></div>
                </div>

                <div class="space-y-4">
                    <div class="text-center">
                        <div id="location-status-container" class="status-badge status-loading">
                            <span id="location-status" class="flex items-center gap-2">
                                <i class="fa-solid fa-location-crosshairs animate-spin"></i>
                                Mencari GPS...
                            </span>
                        </div>
                    </div>

                    <input type="hidden" id="latitude">
                    <input type="hidden" id="longitude">

                    <div class="bg-blue-50 p-4 rounded-2xl flex gap-3 items-start">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Pastikan Anda berada di lingkungan sekolah dan izin lokasi (GPS) sudah diaktifkan agar absensi dapat diproses.
                        </p>
                    </div>
                </div>

                <div class="mt-8 text-center border-t border-gray-50 pt-6">
                    <a href="{{ route('dashboard') }}" class="btn-back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

            </div>

            <p class="text-center text-gray-400 text-xs mt-6">
                &copy; 2026 ClockIn Presence • Digital Geofencing
            </p>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const statusContainer = document.getElementById('location-status-container');
        const statusText = document.getElementById('location-status');

        // A. Cek Izin Lokasi
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successLocation, errorLocation, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        } else {
            Swal.fire("Incompatible", "Browser Anda tidak mendukung fitur lokasi.", "error");
        }

        function successLocation(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            
            // Update UI Status
            statusContainer.classList.remove('status-loading');
            statusContainer.classList.add('status-success');
            statusText.innerHTML = '<i class="fa-solid fa-location-dot"></i> Lokasi Terkunci';
            
            // Nyalakan kamera setelah lokasi dapat
            startScanner();
        }

        function errorLocation(err) {
            statusContainer.classList.remove('status-loading');
            statusContainer.classList.add('status-error');
            statusText.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Gagal Mengakses GPS';
            
            let msg = "Pastikan GPS aktif dan izin lokasi diberikan.";
            if(err.code == 1) msg = "Izin lokasi ditolak oleh pengguna.";
            
            Swal.fire({
                title: "Akses Lokasi Dibutuhkan",
                text: msg,
                icon: "warning",
                confirmButtonColor: "#4a6741"
            });
        }

        // B. Kamera
        function startScanner() {
            const html5QrCode = new Html5Qrcode("reader");
            const config = { 
                fps: 15, 
                qrbox: { width: 220, height: 220 },
                aspectRatio: 1.0
            };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    html5QrCode.stop();
                    // Feedback getaran jika di HP
                    if (navigator.vibrate) navigator.vibrate(100);
                    processAttendance(decodedText);
                },
                (errorMessage) => {}
            ).catch(err => {
                statusText.innerText = "Kamera Error: " + err;
            });
        }

        // C. Kirim Data
        function processAttendance(token) {
            let lat = document.getElementById('latitude').value;
            let lng = document.getElementById('longitude').value;
            
            Swal.fire({ 
                title: 'Mencatat Kehadiran...', 
                html: 'Sistem sedang memvalidasi lokasi Anda.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading() 
            });

            fetch("{{ route('attendance.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    qr_token: token,
                    latitude: lat,
                    longitude: lng
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || "Terjadi kesalahan sistem.");
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: "Berhasil Hadir!",
                    text: data.message,
                    icon: "success",
                    confirmButtonColor: "#4a6741"
                }).then(() => window.location.href = "{{ route('dashboard') }}");
            })
            .catch(error => {
                Swal.fire({
                    title: "Gagal Absen",
                    text: error.message,
                    icon: "error",
                    confirmButtonText: "Coba Lagi",
                    confirmButtonColor: "#dc2626"
                }).then(() => location.reload());
            });
        }
    </script>
</x-app-layout>