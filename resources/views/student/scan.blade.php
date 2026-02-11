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
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Presensi QR Code</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8 animate-fade-in">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl p-6 border border-gray-100">
                
                <div class="text-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Arahkan Kamera</h3>
                    <p class="text-sm text-gray-500 mt-1">Jangan berpindah aplikasi saat proses scan.</p>
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
                    <input type="hidden" id="accuracy">

                    <div class="bg-yellow-50 p-4 rounded-2xl flex gap-3 items-start">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-600 mt-1"></i>
                        <p class="text-xs text-yellow-800 leading-relaxed">
                            <strong>Peringatan:</strong> Jangan tutup browser atau pindah ke aplikasi lain (termasuk Fake GPS/VPN) saat di halaman ini, atau proses akan diulang.
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
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const statusContainer = document.getElementById('location-status-container');
        const statusText = document.getElementById('location-status');
        let html5QrCode;

        // --- 1. FITUR ANTI-CHEAT (TAB SWITCH) ---
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                // Jika user pindah tab/aplikasi, matikan kamera dan paksa reload
                if(html5QrCode) {
                    html5QrCode.stop().catch(err => {});
                }
                Swal.fire({
                    title: "Terdeteksi Berpindah Aplikasi!",
                    text: "Sistem mendeteksi Anda meninggalkan halaman ini. Untuk mencegah kecurangan, halaman akan dimuat ulang.",
                    icon: "warning",
                    confirmButtonColor: "#dc2626",
                    confirmButtonText: "Muat Ulang",
                    allowOutsideClick: false
                }).then(() => {
                    location.reload();
                });
            }
        });

        // --- 2. GET LOCATION DENGAN HIGH ACCURACY ---
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successLocation, errorLocation, {
                enableHighAccuracy: true, // Wajib GPS asli
                timeout: 10000,
                maximumAge: 0 // Jangan pakai cache lokasi lama
            });
        } else {
            Swal.fire("Error", "Browser Anda tidak mendukung fitur lokasi.", "error");
        }

        function successLocation(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const acc = position.coords.accuracy;

            // Validasi Akurasi (Fake GPS seringkali punya akurasi aneh atau terlalu presisi)
            // GPS asli biasanya punya akurasi 10-50 meter.
            // Jika akurasi > 100 meter, anggap sinyal jelek/palsu.
            if (acc > 200) {
                statusText.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Akurasi GPS Buruk (' + Math.round(acc) + 'm)';
                statusContainer.classList.remove('status-loading');
                statusContainer.classList.add('status-error');
                
                Swal.fire({
                    title: "Sinyal GPS Lemah",
                    text: "Akurasi lokasi Anda " + Math.round(acc) + " meter. Cobalah pindah ke area terbuka.",
                    icon: "warning"
                });
                return; 
            }

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('accuracy').value = acc;
            
            // Update UI Status
            statusContainer.classList.remove('status-loading');
            statusContainer.classList.add('status-success');
            statusText.innerHTML = '<i class="fa-solid fa-location-dot"></i> Lokasi Terkunci (Akurasi: ' + Math.round(acc) + 'm)';
            
            // Nyalakan kamera
            startScanner();
        }

        function errorLocation(err) {
            statusContainer.classList.remove('status-loading');
            statusContainer.classList.add('status-error');
            statusText.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Gagal Mengakses GPS';
            Swal.fire("Akses Lokasi Ditolak", "Wajib mengizinkan akses lokasi untuk absen.", "error");
        }

        // --- 3. SCANNER ---
        function startScanner() {
            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    html5QrCode.stop();
                    // Cegah klik ganda / scan ganda
                    if (navigator.vibrate) navigator.vibrate(200);
                    processAttendance(decodedText);
                },
                (errorMessage) => {}
            ).catch(err => {
                statusText.innerText = "Kamera Error: " + err;
            });
        }

        // --- 4. KIRIM DATA ---
        function processAttendance(token) {
            let lat = document.getElementById('latitude').value;
            let lng = document.getElementById('longitude').value;
            
            // Cek lagi sebelum kirim
            if(!lat || !lng) {
                Swal.fire("Lokasi Hilang", "Mohon refresh halaman.", "error");
                return;
            }

            Swal.fire({ 
                title: 'Memproses...', 
                html: 'Jangan tutup halaman ini.',
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
                if (!response.ok) throw new Error(data.message || "Terjadi kesalahan.");
                return data;
            })
            .then(data => {
                Swal.fire({
                    title: "Berhasil!",
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
                    confirmButtonColor: "#dc2626"
                }).then(() => location.reload());
            });
        }
    </script>
</x-app-layout>