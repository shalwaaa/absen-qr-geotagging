<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Gamifikasi & Dompet Integritas</span>
            </h2>
        </div>
        <p style="color: #E4EB9C;">Atur logika penambahan poin dan item reward di Marketplace</p>
    </x-slot>

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --fern: #537B2F; --asparagus: #8DA750; --mindaro: #E4EB9C; --cream: #FAFAF5; }
        
        .card-custom { background: white; border-radius: 20px; border: 1px solid #f0fdf4; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); height: 100%; }

        /* TABS STYLING */
        .tab-container { background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 2rem; }
        .tab-header { display: flex; border-bottom: 2px solid #f1f5f9; background: #fafafa; }
        .tab-button { flex: 1; padding: 16px; font-weight: 800; color: #64748b; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; border: none; background: transparent;}
        .tab-button.active { color: var(--cal-poly); background: white; border-bottom: 3px solid var(--fern); }
        .tab-pane { padding: 32px; display: none; animation: fadeIn 0.4s ease; }
        .tab-pane.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* STATEMENT BUILDER UI (PENTING UNTUK UJIAN) */
        .statement-builder {
            display: flex; flex-wrap: wrap; align-items: center; gap: 12px;
            background: #f8fafc; padding: 20px; border-radius: 16px; border: 2px dashed #cbd5e1;
            font-size: 1.1rem; font-weight: 700; color: var(--dark-green);
        }
        .sb-select, .sb-input {
            background: white; border: 2px solid var(--asparagus); color: var(--cal-poly);
            padding: 8px 12px; border-radius: 10px; font-weight: 700; outline: none;
            box-shadow: 0 4px 6px rgba(141, 167, 80, 0.1); transition: 0.2s;
        }
        .sb-select:focus, .sb-input:focus { border-color: var(--cal-poly); box-shadow: 0 4px 10px rgba(45, 81, 40, 0.2); }
        .sb-input-number { width: 80px; text-align: center; }
        
        .btn-submit { background: var(--cal-poly); color: white; padding: 12px 24px; border-radius: 10px; font-weight: bold; border: none; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit:hover { background: var(--fern); transform: translateY(-2px); }

        /* List Items */
        .rule-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 12px; border-left: 5px solid var(--cal-poly);}
        .item-card { border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; text-align: center; position: relative; overflow: hidden; transition: 0.3s; }
        .item-card:hover { border-color: var(--asparagus); transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .coin-badge { background: #fef3c7; color: #b45309; padding: 4px 12px; border-radius: 20px; font-weight: 900; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px; border: 1px solid #fde68a;}
        
        /* Leaderboard */
        .rank-1 { background: linear-gradient(135deg, #fef08a, #f59e0b); color: white; }
        .rank-2 { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: white; }
        .rank-3 { background: linear-gradient(135deg, #fed7aa, #ea580c); color: white; }
        .rank-other { background: #f1f5f9; color: #475569; }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-6xl mx-auto">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl text-sm font-bold border border-green-200">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="tab-container shadow-lg">
                <!-- TAB HEADERS -->
                <div class="tab-header">
                    <button class="tab-button active" onclick="switchTab('rules')"><i class="fa-solid fa-code-branch"></i> Rule Engine</button>
                    <button class="tab-button" onclick="switchTab('marketplace')"><i class="fa-solid fa-store"></i> Marketplace</button>
                    <button class="tab-button" onclick="switchTab('leaderboard')"><i class="fa-solid fa-trophy"></i> Leaderboard</button>
                </div>

                <!-- TAB 1: RULE ENGINE -->
                <div id="tab-rules" class="tab-pane active">
                    <h3 class="text-xl font-bold text-[#142C14] mb-4">Buat Aturan Poin Baru</h3>
                    
                    <form action="{{ route('gamification.rules.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="rule_name" placeholder="Nama Aturan " class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-[var(--fern)] font-bold" required>
                        </div>

                        <!-- STATEMENT BUILDER (DYNAMIC DOM) -->
                        <div class="statement-builder mb-6">
                            <span>JIKA</span>
                            <select name="target_role" class="sb-select">
                                <option value="student">Siswa</option>
                                <option value="teacher">Guru</option>
                                <option value="all">Semua User</option>
                            </select>

                            <span>MELAKUKAN</span>
                            <select name="condition_type" id="condition_type" class="sb-select" onchange="updateStatementUI()">
                                <option value="check_in_time">Absen Pada Jam</option>
                                <option value="status">Mendapat Status</option>
                            </select>

                            <select name="condition_operator" id="condition_operator" class="sb-select">
                                <option value="<">Kurang Dari (<)</option>
                                <option value=">">Lebih Dari (>)</option>
                                <option value="=">Sama Dengan (=)</option>
                            </select>

                            <input type="time" name="condition_value" id="condition_value_time" class="sb-input">
                            
                            <select name="condition_value_status" id="condition_value_status" class="sb-select" style="display: none;" disabled>
                                <option value="present">Hadir Tepat Waktu</option>
                                <option value="late">Terlambat</option>
                                <option value="alpha">Alpha / Bolos</option>
                            </select>

                            <span>MAKA POIN</span>
                            <input type="number" name="point_modifier" class="sb-input sb-input-number" placeholder="+ / -" required>
                        </div>

                        <button type="submit" class="btn-submit"><i class="fa-solid fa-robot"></i> Simpan Rule</button>
                    </form>

                    <div class="mt-10">
                        <h4 class="font-bold text-gray-500 uppercase tracking-widest text-xs mb-4">Aturan Aktif</h4>
                        @foreach($rules as $rule)
                            <div class="rule-item">
                                <div>
                                    <h4 class="font-bold text-lg text-[#142C14]">{{ $rule->rule_name }}</h4>
                                    
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-black text-xl {{ $rule->point_modifier > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $rule->point_modifier > 0 ? '+'.$rule->point_modifier : $rule->point_modifier }} PT
                                    </span>
                                    <form action="{{ route('gamification.rules.destroy', $rule->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" onclick="return confirm('Hapus rule ini?')"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- TAB 2: MARKETPLACE -->
                <div id="tab-marketplace" class="tab-pane">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <h3 class="text-xl font-bold text-[#142C14] mb-4">Buat Item Reward</h3>
                            <form action="{{ route('gamification.items.store') }}" method="POST" class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Item</label>
                                    <input type="text" name="item_name" placeholder="Cth: Kartu Bebas Telat" class="w-full p-2 border border-gray-300 rounded-lg outline-none focus:border-[var(--fern)]" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Fungsi Sistem (Tipe)</label>
                                    <select name="item_type" class="w-full p-2 border border-gray-300 rounded-lg outline-none focus:border-[var(--fern)]">
                                        <option value="late_pass">Kompensasi Terlambat (Menit)</option>
                                        <option value="permission_pass">Kupon Izin 1 Hari</option>
                                        <option value="custom">Reward Bebas (Cth: Snack)</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Value (Menit)</label>
                                        <input type="number" name="value_minutes" placeholder="Cth: 15" class="w-full p-2 border border-gray-300 rounded-lg outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-yellow-600 mb-1">Harga (Poin)</label>
                                        <input type="number" name="point_cost" placeholder="Cth: 50" class="w-full p-2 border border-yellow-400 bg-yellow-50 rounded-lg outline-none font-bold" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn-submit w-full justify-center mt-2"><i class="fa-solid fa-plus"></i> Tambah Katalog</button>
                            </form>
                        </div>

                        <div class="lg:col-span-2">
                            <h3 class="text-xl font-bold text-[#142C14] mb-4">Katalog Aktif</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($items as $item)
                                    <div class="item-card">
                                        <div class="text-4xl text-[var(--fern)] mb-3"><i class="fa-solid {{ $item->icon }}"></i></div>
                                        <h4 class="font-bold text-lg text-gray-800">{{ $item->item_name }}</h4>
                                        <p class="text-xs text-gray-500 mt-1 mb-4">{{ $item->item_type == 'late_pass' ? 'Potongan Telat '.$item->value_minutes.' Menit' : 'Spesial Reward' }}</p>
                                        <div class="coin-badge mb-4"><i class="fa-solid fa-coins"></i> {{ $item->point_cost }} Pts</div>
                                        
                                        <form action="{{ route('gamification.items.destroy', $item->id) }}" method="POST" class="absolute top-2 right-2">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-2" onclick="return confirm('Hapus item ini?')"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: LEADERBOARD -->
                <div id="tab-leaderboard" class="tab-pane">
                    <div class="text-center mb-8">
                        <i class="fa-solid fa-crown text-6xl text-yellow-400 mb-4 drop-shadow-md"></i>
                        <h2 class="text-2xl font-black text-[#142C14]">Top 10 Siswa Berintegritas</h2>
                        <p class="text-gray-500">Berdasarkan akumulasi poin terbanyak.</p>
                    </div>

                    <div class="max-w-3xl mx-auto bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                        @foreach($leaderboard as $idx => $user)
                            <div class="flex items-center justify-between p-4 border-b border-gray-100 hover:bg-gray-50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg {{ $idx == 0 ? 'rank-1' : ($idx == 1 ? 'rank-2' : ($idx == 2 ? 'rank-3' : 'rank-other')) }}">
                                        {{ $idx + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">{{ $user->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $user->classroom->name ?? 'Tanpa Kelas' }}</p>
                                    </div>
                                </div>
                                <div class="text-lg font-black text-[var(--cal-poly)] bg-[#f0fdf4] px-4 py-1 rounded-full border border-[#bbf7d0]">
                                    {{ $user->integrity_points }} Pts
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPT TABS & DOM JAVASCRIPT UNTUK STATEMENT BUILDER -->
    <script>
        // Logika Tab
        function switchTab(tabName) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        }

        // DOM Manipulasi untuk Statement Builder
        function updateStatementUI() {
            const type = document.getElementById('condition_type').value;
            const operator = document.getElementById('condition_operator');
            const valTime = document.getElementById('condition_value_time');
            const valStatus = document.getElementById('condition_value_status');

            if (type === 'status') {
                // Jika pilih status (Hadir/Telat/Alpha)
                operator.style.display = 'none';
                operator.disabled = true;
                
                valTime.style.display = 'none';
                valTime.disabled = true;

                valStatus.style.display = 'inline-block';
                valStatus.disabled = false;
                valStatus.name = 'condition_value'; // Ganti name agar masuk ke request
            } else {
                // Jika pilih Jam
                operator.style.display = 'inline-block';
                operator.disabled = false;
                
                valTime.style.display = 'inline-block';
                valTime.disabled = false;
                valTime.name = 'condition_value';

                valStatus.style.display = 'none';
                valStatus.disabled = true;
            }
        }
        
        // Panggil sekali saat halaman dimuat
        updateStatementUI();
    </script>
</x-app-layout>