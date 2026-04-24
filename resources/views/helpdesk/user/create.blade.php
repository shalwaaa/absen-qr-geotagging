<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <span style="color: #8DA750; font-weight: 800;">Pusat Bantuan:</span>
            <span class="text-[#2D5128]">Buat Tiket Baru</span>
        </h2>
    </x-slot>

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --fern: #537B2F; --mindaro: #E4EB9C; }

        .form-card { background: white; border-radius: 24px; padding: 40px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); }
        .form-label { font-weight: 900; color: var(--dark-green); margin-bottom: 8px; display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        .form-input, .form-textarea, .form-select { width: 100%; padding: 16px 20px; border: 2px solid #f1f5f9; border-radius: 16px; outline: none; transition: 0.3s; font-size: 0.95rem; background: #FDFDF9; }
        .form-input:focus, .form-textarea:focus, .form-select:focus { border-color: var(--cal-poly); background: white; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }

        /* Style untuk Kotak Peringatan Duplikat */
        .duplicate-alert {
            display: none; /* Disembunyikan secara default */
            background: #fffbeb;
            border: 2px solid #fde68a;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            animation: slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .similar-item {
            background: white;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #fef08a;
            margin-top: 12px;
            transition: 0.2s;
        }
        .similar-item:hover { transform: translateX(5px); border-color: #f59e0b; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1); }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen pb-24">
        <div class="max-w-3xl mx-auto">

            <div class="mb-8">
                <h1 class="text-3xl font-black text-[var(--dark-green)]">Formulir Aduan</h1>
                <p class="text-gray-500 font-medium mt-2">Ceritakan masalah operasional atau teknis yang Anda alami secara detail.</p>
            </div>

            <!-- KOTAK PERINGATAN FITUR ANTI-DUPLIKAT (Akan muncul otomatis via JS) -->
            <div id="duplicateAlert" class="duplicate-alert shadow-lg shadow-yellow-900/5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-400 text-yellow-900 rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-yellow-800 text-lg uppercase tracking-widest">Solusi Mungkin Sudah Ada!</h4>
                        <p class="text-sm text-yellow-700 font-medium mt-1">Kami menemukan kendala serupa yang sedang atau sudah ditangani oleh tim Helpdesk. Apakah masalah Anda sama dengan tiket di bawah ini?</p>

                        <div id="similarList" class="space-y-3 mt-4">
                            <!-- Daftar tiket duplikat akan dirender di sini oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM INPUT TIKET -->
            <div class="form-card">
                <form action="{{ route('user.tickets.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="form-label">Kategori Kendala</label>
                            <select name="category" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Gagal Scan QR">Gagal Scan QR</option>
                                <option value="Lokasi Tidak Akurat (GPS)">Lokasi Tidak Akurat (GPS)</option>
                                <option value="Aplikasi Error / Bug">Aplikasi Error / Bug</option>
                                <option value="Perubahan Data Profil">Perubahan Data Profil</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Prioritas Masalah</label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Rendah (Hanya Bertanya)</option>
                                <option value="mid" selected>Sedang (Kendala Penggunaan)</option>
                                <option value="high">Tinggi (Sistem Darurat / Error Fatal)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="form-label">Subjek / Judul Masalah</label>
                        <input type="text" name="subject" id="subjectInput" class="form-input" placeholder="Contoh: Kamera menjadi hitam saat mau scan QR" required>
                    </div>

                    <div class="mb-10">
                        <label class="form-label">Deskripsi Lengkap</label>
                        <textarea name="description" id="descInput" rows="6" class="form-textarea" placeholder="Jelaskan kronologi masalahnya secara detail..." required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[var(--cal-poly)] hover:bg-[var(--dark-green)] text-white px-8 py-5 rounded-2xl font-black transition-all active:scale-95 flex justify-center items-center gap-3 uppercase text-sm tracking-widest shadow-xl shadow-green-900/20">
                        Kirim Laporan Sekarang <i class="fa-solid fa-paper-plane text-[var(--mindaro)]"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT FITUR CERDAS: ANTI-DUPLIKAT (AJAX FULLTEXT SEARCH) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectInput = document.getElementById('subjectInput');
            const descInput = document.getElementById('descInput');
            const duplicateAlert = document.getElementById('duplicateAlert');
            const similarList = document.getElementById('similarList');

            let typingTimer;
            const doneTypingInterval = 800; // Delay 0.8 detik setelah user berhenti ngetik

            // Jalankan fungsi saat user mengetik
            subjectInput.addEventListener('keyup', handleTyping);
            descInput.addEventListener('keyup', handleTyping);

            function handleTyping() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(searchSimilar, doneTypingInterval);
            }

            function searchSimilar() {
                const keyword = subjectInput.value + ' ' + descInput.value;

                // Jika ketikan masih terlalu pendek, jangan cari
                if (keyword.length < 15) {
                    duplicateAlert.style.display = 'none';
                    return;
                }

                // Kirim request AJAX ke controller
                fetch(`{{ route('user.tickets.search') }}?q=${encodeURIComponent(keyword)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        similarList.innerHTML = ''; // Bersihkan list sebelumnya

                        data.forEach(item => {
                            // Render tiket yang mirip
                            similarList.innerHTML += `
                                <a href="/user/my-tickets/${item.id}" target="_blank" class="similar-item block cursor-pointer">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-black text-yellow-600 uppercase tracking-widest">${item.ticket_code}</span>
                                        <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 border border-yellow-200">
                                            ${item.status.replace('_', ' ')}
                                        </span>
                                    </div>
                                    <h5 class="text-sm font-bold text-yellow-900">${item.subject}</h5>
                                </a>
                            `;
                        });

                        // Tampilkan kotak peringatan
                        duplicateAlert.style.display = 'block';
                    } else {
                        duplicateAlert.style.display = 'none';
                    }
                })
                .catch(err => console.error("Error Anti-Duplicate:", err));
            }
        });
    </script>
</x-app-layout>
