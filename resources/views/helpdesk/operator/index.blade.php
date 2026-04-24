<x-app-layout>
    <x-slot name="header">
        <div class="header-section">
            <h2 class="font-semibold text-xl leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Operator Helpdesk</span>
            </h2>
        </div>
    </x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly: #2D5128;
            --fern: #537B2F;
            --mindaro: #E4EB9C;
        }

        .hover-dark:hover {
            background-color: #142C14 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(20, 44, 20, 0.2) !important;
        }

        /* Hover khusus untuk tombol selesai (hijau terang ke gelap) */
        .hover-done:hover {
            background-color: #1e3a1e !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 81, 40, 0.3) !important;
        }

        .rounded-4 { border-radius: 12px !important; }
        .fw-black { font-weight: 900 !important; }
        .text-xs { font-size: 0.75rem; }

        .tab-btn { padding: 10px 24px; border-radius: 12px; font-weight: 800; transition: 0.3s; text-decoration: none; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .tab-active { background: var(--cal-poly); color: white !important; shadow: 0 4px 12px rgba(45,81,40,0.2); }
        .tab-inactive { color: #94a3b8; background: white; border: 1px solid #f1f5f9; }
        .tab-inactive:hover { background: #f8fafc; color: var(--dark-green); }

        .card-ticket { border-radius: 20px; border: 1px solid #e2e8f0; transition: 0.3s; background: white; margin-bottom: 20px; position: relative; overflow: hidden; }
        .card-ticket:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -10px rgba(0,0,0,0.1); border-color: #8DA750; }

        .prio-high { border-left: 6px solid #ef4444; }
        .prio-mid { border-left: 6px solid #f59e0b; }
        .prio-low { border-left: 6px solid #94a3b8; }

        .rating-badge {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fef3c7;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
        }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-6xl mx-auto">

            <!-- Filter Status (Tabs) -->
            <div class="flex gap-3 mb-10 bg-gray-100/50 p-2 rounded-2xl w-fit border border-gray-200">
                <a href="{{ route('operator.tickets.index', ['status' => 'open']) }}"
                   class="tab-btn {{ $status == 'open' ? 'tab-active' : 'tab-inactive' }}">
                   <i class="fa-solid fa-inbox mr-1"></i> Baru
                </a>
                <a href="{{ route('operator.tickets.index', ['status' => 'in_progress']) }}"
                   class="tab-btn {{ $status == 'in_progress' ? 'tab-active' : 'tab-inactive' }}">
                   <i class="fa-solid fa-spinner mr-1"></i> Diproses
                </a>
                <a href="{{ route('operator.tickets.index', ['status' => 'closed']) }}"
                   class="tab-btn {{ $status == 'closed' ? 'tab-active' : 'tab-inactive' }}">
                   <i class="fa-solid fa-check-double mr-1"></i> Selesai
                </a>
            </div>

            <div class="space-y-2">
                @forelse($tickets as $t)
                    <div class="card-ticket p-6 {{ $t->priority == 'high' ? 'prio-high' : ($t->priority == 'mid' ? 'prio-mid' : 'prio-low') }}">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

                            <!-- Bagian Info Tiket (Kiri) -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $t->ticket_code }}</span>
                                    <span class="text-[9px] px-3 py-1 rounded-full bg-gray-50 text-[var(--cal-poly)] border border-gray-100 font-black uppercase">{{ $t->category }}</span>

                                    @if($t->status == 'closed' && $t->rating)
                                        <div class="rating-badge">
                                            <i class="fa-solid fa-star text-yellow-500"></i>
                                            {{ $t->rating->score }} / 5
                                        </div>
                                    @endif
                                </div>

                                <h3 class="text-xl font-black text-[var(--dark-green)] mb-1">{{ $t->subject }}</h3>
                                <p class="text-xs text-gray-500 line-clamp-1 italic font-medium">"{{ $t->description }}"</p>

                                <div class="flex flex-wrap items-center gap-5 mt-5 text-[11px] font-bold text-gray-400 uppercase tracking-tighter">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-user text-gray-300 text-sm"></i> Pelapor: {{ $t->user->name }}</span>
                                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-gray-300 text-sm"></i> Masuk: {{ $t->created_at->diffForHumans() }}</span>

                                    @if($t->priority == 'high')
                                        <span class="text-red-500"><i class="fa-solid fa-triangle-exclamation"></i> Prioritas Tinggi</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Bagian Aksi (Kanan) -->
                            <div class="flex flex-row items-stretch gap-3 w-full md:w-[450px]">
                                <!-- Tombol Detail (Selalu Ada) -->
                                <a href="{{ route('operator.tickets.show', $t->id) }}"
                                class="flex-1 flex items-center justify-center text-center px-4 py-3 rounded-xl bg-gray-50 text-gray-600 font-black text-[11px] hover:bg-gray-100 transition border border-gray-200 uppercase tracking-widest no-underline">
                                    Detail
                                </a>

                                <!-- Tombol Ambil Alih (Hanya muncul di tab BARU) -->
                                @if($t->status == 'open')
                                    <form action="{{ route('operator.tickets.take', $t->id) }}" method="POST" class="flex-1 flex m-0 p-0">
                                        @csrf
                                        <button type="submit"
                                            style="background-color: #2D5128 !important; color: #ffffff !important;"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-black text-[11px] hover-dark transition uppercase tracking-widest shadow-lg shadow-green-900/10 border-0 cursor-pointer">
                                            Ambil Alih
                                        </button>
                                    </form>
                                @endif

                                <!-- TOMBOL SELESAI (Hanya muncul di tab DIPROSES) -->
                                @if($t->status == 'in_progress')
                                    <form action="{{ route('operator.tickets.close', $t->id) }}" method="POST" class="flex-1 flex m-0 p-0">
                                        @csrf
                                        <button type="submit"
                                            style="background-color: #1e3a1e !important; color: #ffffff !important;"
                                            class="w-full flex items-center justify-center px-4 py-3 rounded-xl font-black text-[11px] hover-done transition uppercase tracking-widest shadow-lg shadow-green-900/10 border-0 cursor-pointer">
                                            <i class="fa-solid fa-check-circle mr-1"></i> Selesai
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="text-center py-32 bg-white rounded-[32px] border-2 border-dashed border-gray-100">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-6">
                            <i class="fa-solid fa-clipboard-list text-3xl text-gray-200"></i>
                        </div>
                        <h4 class="text-lg font-black text-gray-400 uppercase tracking-widest">Antrean Kosong</h4>
                        <p class="text-sm text-gray-300 mt-1 font-medium">Belum ada tiket dalam kategori ini.</p>
                    </div>
                @endforelse

                <div class="mt-10">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
