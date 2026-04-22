<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Dompet Integritas Siswa </span>
            </h2>
        </div>
        <p style="color: #E4EB9C; margin-top: 4px; font-size: 0.9rem;">Tukarkan pointmu dengan kupon dan nikmati rewardnya!</p>
    </x-slot>

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --fern: #537B2F; --asparagus: #8DA750; --mindaro: #E4EB9C; }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- 3 KOTAK STATISTIK (PENGGANTI BANNER PANJANG) --- */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 24px;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        @media (min-width: 768px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }

        .stat-card {
            background: white; border: 1px solid #f0fdf4; border-bottom: 4px solid var(--mindaro);
            border-radius: 20px; padding: 24px; display: flex; align-items: center; gap: 20px;
            transition: transform 0.2s, box-shadow 0.2s; height: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05); border-bottom-color: var(--fern); }
        
        .stat-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .icon-poin { background-color: #fef3c7; color: #d97706; }
        .icon-level { background-color: #eff6ff; color: #2563eb; }
        .icon-kupon { background-color: #f0fdf4; color: var(--fern); }

        .stat-label { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px; }
        .stat-value { font-size: 2rem; font-weight: 900; color: var(--dark-green); line-height: 1; }
        .stat-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 6px; }

        /* TABS STYLING */
        .tab-container { background: white; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); animation: fadeInUp 0.8s ease-out forwards; }
        .tab-header { display: flex; border-bottom: 2px solid #f1f5f9; background: #fafafa; overflow-x: auto; }
        .tab-button { flex: 1; padding: 18px; font-weight: 800; color: #64748b; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; border: none; background: transparent; font-size: 1rem; white-space: nowrap; }
        .tab-button.active { color: var(--cal-poly); background: white; border-bottom: 4px solid var(--fern); }
        .tab-pane { padding: 32px; display: none; animation: fadeIn 0.4s ease; }
        .tab-pane.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* LIST MUTASI */
        .ledger-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
        .ledger-item:last-child { border-bottom: none; }
        .ledger-item:hover { background: #fcfdfa; }
        .amount-earn { color: #16a34a; font-weight: 900; font-size: 1.2rem; }
        .amount-spend { color: #dc2626; font-weight: 900; font-size: 1.2rem; }

        /* MARKETPLACE GRID */
        .shop-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .shop-card { border: 2px solid #f1f5f9; border-radius: 20px; padding: 24px; text-align: center; transition: 0.3s; position: relative; background: white;}
        .shop-card:hover { border-color: var(--mindaro); transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .shop-icon { font-size: 3rem; color: var(--fern); margin-bottom: 16px; }
        .shop-price { background: #fef3c7; color: #b45309; padding: 6px 16px; border-radius: 20px; font-weight: 900; display: inline-block; margin-bottom: 16px; border: 1px solid #fde68a;}
        
        .btn-buy { background: var(--cal-poly); color: white; width: 100%; padding: 12px; border-radius: 12px; font-weight: bold; border: none; cursor: pointer; transition: 0.2s; }
        .btn-buy:hover:not(:disabled) { background: var(--fern); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(45,81,40,0.2); }
        .btn-buy:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; }

        /* INVENTORY TICKET */
        .ticket-card { background: linear-gradient(135deg, #f8fafc, #f1f5f9); border: 2px dashed #cbd5e1; border-radius: 16px; padding: 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .ticket-used { opacity: 0.6; background: #f1f5f9; border-style: solid; }
        .ticket-badge { background: var(--fern); color: white; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
        .ticket-badge.used { background: #94a3b8; }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-6xl mx-auto">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl text-sm font-bold border border-green-200 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-xl text-sm font-bold border border-red-200 flex items-center gap-3">
                    <i class="fa-solid fa-circle-xmark text-xl"></i> {{ session('error') }}
                </div>
            @endif

            <!-- 1. KOTAK STATISTIK (MENGGANTIKAN BANNER) -->
            <div class="stat-grid">
                
                <!-- Kotak 1: Saldo Poin -->
                <div class="stat-card">
                    <div class="stat-icon icon-poin"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <p class="stat-label">Saldo Saat Ini</p>
                        <h3 class="stat-value">{{ number_format($user->integrity_points) }} <span class="text-sm text-yellow-600">Pts</span></h3>
                        <p class="stat-desc">Total poin integritas Anda.</p>
                    </div>
                </div>

                <!-- Kotak 2: Level Kedisiplinan -->
                <div class="stat-card" style="animation-delay: 0.1s;">
                    <div class="stat-icon icon-level"><i class="fa-solid {{ $badge }}"></i></div>
                    <div>
                        <p class="stat-label">Status Level</p>
                        <h3 class="stat-value text-blue-700" style="font-size: 1.5rem;">{{ $level }}</h3>
                        <p class="stat-desc">Tingkatkan terus kehadiranmu!</p>
                    </div>
                </div>

                <!-- Kotak 3: Jumlah Kupon / Token -->
                <div class="stat-card" style="animation-delay: 0.2s;">
                    <div class="stat-icon icon-kupon"><i class="fa-solid fa-ticket"></i></div>
                    <div>
                        <p class="stat-label">Kupon Tersedia</p>
                        <h3 class="stat-value">{{ $tokens->where('status', 'AVAILABLE')->count() }} <span class="text-sm text-[var(--fern)]">Kupon</span></h3>
                        <p class="stat-desc">Lihat di menu Tas Saya.</p>
                    </div>
                </div>

            </div>

            <!-- 2. TABS CONTAINER -->
            <div class="tab-container">
                <div class="tab-header">
                    <button class="tab-button active" onclick="switchTab('mutasi')"><i class="fa-solid fa-list-ul"></i> Riwayat Mutasi</button>
                    <button class="tab-button" onclick="switchTab('shop')"><i class="fa-solid fa-store"></i> Marketplace</button>
                    <button class="tab-button" onclick="switchTab('inventory')"><i class="fa-solid fa-backpack"></i> Tas Saya</button>
                </div>

                <!-- TAB 1: RIWAYAT MUTASI -->
                <div id="tab-mutasi" class="tab-pane active">
                    <h3 class="text-xl font-bold text-[#142C14] mb-6">Buku Besar Poin</h3>
                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-white">
                        @forelse($ledgers as $l)
                            <div class="ledger-item">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg {{ $l->transaction_type == 'EARN' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        <i class="fa-solid {{ $l->transaction_type == 'EARN' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $l->description }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $l->created_at->format('d M Y, H:i') }} • Saldo Akhir: {{ $l->current_balance }}</p>
                                    </div>
                                </div>
                                <div class="{{ $l->transaction_type == 'EARN' ? 'amount-earn' : 'amount-spend' }}">
                                    {{ $l->transaction_type == 'EARN' ? '+' : '-' }}{{ $l->amount }}
                                </div>
                            </div>
                        @empty
                            <div class="p-16 text-center text-gray-400">
                                <i class="fa-solid fa-receipt text-5xl mb-4 opacity-20"></i>
                                <p class="text-lg font-bold">Belum ada riwayat transaksi</p>
                                <p class="text-sm mt-1">Absen tepat waktu untuk mulai mengumpulkan poin.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB 2: MARKETPLACE -->
                <div id="tab-shop" class="tab-pane">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-[#142C14]">Tukar Poin dengan Kelonggaran</h3>
                            <p class="text-sm text-gray-500 mt-1">Token kompensasi absen akan dipakai otomatis oleh sistem.</p>
                        </div>
                        <div class="bg-yellow-50 text-yellow-700 px-4 py-2 rounded-lg text-sm font-bold border border-yellow-200">
                            Poin Anda: {{ number_format($user->integrity_points) }}
                        </div>
                    </div>
                    
                    <div class="shop-grid">
                        @forelse($items as $item)
                            <div class="shop-card">
                                <div class="shop-icon"><i class="fa-solid {{ $item->icon }}"></i></div>
                                <h4 class="font-bold text-lg text-gray-800">{{ $item->item_name }}</h4>
                                <p class="text-xs text-gray-500 mt-2 mb-4 h-8">{{ $item->description ?? 'Item kelonggaran absen.' }}</p>
                                <div class="shop-price"><i class="fa-solid fa-coins mr-1"></i> {{ $item->point_cost }} Pts</div>
                                
                                <form action="{{ route('student.wallet.purchase', $item->id) }}" method="POST" onsubmit="return confirm('Yakin tukar {{ $item->point_cost }} poin dengan item ini?')">
                                    @csrf
                                    @if($user->integrity_points >= $item->point_cost)
                                        <button type="submit" class="btn-buy">TUKAR SEKARANG</button>
                                    @else
                                        <button type="button" class="btn-buy" disabled title="Poin tidak cukup">POIN TIDAK CUKUP</button>
                                    @endif
                                </form>
                            </div>
                        @empty
                            <div class="col-span-full p-16 text-center text-gray-400 border-2 border-dashed rounded-2xl">
                                <i class="fa-solid fa-store-slash text-5xl mb-4 opacity-20"></i>
                                <p class="text-lg font-bold">Marketplace Masih Kosong</p>
                                <p class="text-sm mt-1">Admin belum menambahkan item untuk ditukarkan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB 3: TAS SAYA (INVENTORY) -->
                <div id="tab-inventory" class="tab-pane">
                    <h3 class="text-xl font-bold text-[#142C14] mb-2">Inventory Token</h3>
                    <p class="text-sm text-gray-500 mb-6">Daftar item dan kupon yang Anda miliki saat ini.</p>

                    @forelse($tokens as $t)
                        <div class="ticket-card {{ $t->status == 'USED' ? 'ticket-used' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="text-3xl {{ $t->status == 'USED' ? 'text-gray-300' : 'text-[var(--cal-poly)]' }}">
                                    <i class="fa-solid fa-ticket-simple"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-gray-800">{{ $t->item->item_name }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">Dibeli: {{ $t->created_at->format('d M Y, H:i') }}</p>
                                    @if($t->status == 'USED')
                                        <p class="text-xs text-red-500 mt-1 font-bold"><i class="fa-solid fa-check-double"></i> Telah digunakan oleh sistem</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="ticket-badge {{ $t->status == 'USED' ? 'used' : '' }}">
                                    {{ $t->status == 'AVAILABLE' ? 'SIAP PAKAI' : 'SUDAH DIPAKAI' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-16 text-center text-gray-400 border-2 border-dashed rounded-2xl bg-gray-50">
                            <i class="fa-solid fa-box-open text-5xl mb-4 opacity-20"></i>
                            <p class="text-lg font-bold">Tas Masih Kosong</p>
                            <p class="text-sm mt-1">Silakan tukar poin Anda di Marketplace.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <!-- Script Tab -->
    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        }
    </script>
</x-app-layout>