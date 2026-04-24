<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between header-section">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span style="color: #E4EB9C; font-weight: 800;">Pusat Bantuan</span>
                </h2>
            </div>
        </div>
        <p class="text-gray-500;" style="color: #E4EB9C">Laporkan kendala teknis atau masalah operasional Anda di sini.</p>
    </x-slot>

    <style>
        :root { --dark-green: #142C14; --cal-poly: #2D5128; --fern: #537B2F; --cream: #FAFAF5; }

        .ticket-card {
            background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0;
            margin-bottom: 16px; transition: 0.3s; display: flex; flex-direction: column; gap: 12px;
        }
        .ticket-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: var(--fern); }

        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .bg-open { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; } /* Kuning */
        .bg-progress { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; } /* Biru */
        .bg-closed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; } /* Hijau */

        .badge-priority { font-size: 0.7rem; padding: 2px 8px; border-radius: 6px; font-weight: bold; }
        .prio-high { background: #fee2e2; color: #dc2626; }
        .prio-mid { background: #fef9c3; color: #ca8a04; }
        .prio-low { background: #f1f5f9; color: #64748b; }

        .btn-create {
            background: var(--cal-poly); color: white; padding: 12px 24px; border-radius: 12px;
            font-weight: bold; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-create:hover { background: var(--dark-green); box-shadow: 0 4px 12px rgba(45,81,40,0.2); }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen">
        <div class="max-w-5xl mx-auto">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                
                <a href="{{ route('user.tickets.create') }}" class="btn-create">
                    <i class="fa-solid fa-plus"></i> BUAT TIKET BARU
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-bold text-lg text-[#142C14] mb-4">Tiket Aduan Saya</h3>

                @forelse($tickets as $t)
                    <a href="{{ route('user.tickets.show', $t->id) }}" class="ticket-card">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-mono text-gray-400 mb-1 block">{{ $t->ticket_code }}</span>
                                <h4 class="font-bold text-lg text-gray-800">{{ $t->subject }}</h4>
                            </div>
                            <span class="badge
                                {{ $t->status == 'open' ? 'bg-open' : ($t->status == 'in_progress' ? 'bg-progress' : 'bg-closed') }}">
                                {{ str_replace('_', ' ', $t->status) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 text-xs">
                            <span class="badge-priority {{ $t->priority == 'high' ? 'prio-high' : ($t->priority == 'mid' ? 'prio-mid' : 'prio-low') }}">
                                <i class="fa-solid fa-flag"></i> Prioritas: {{ ucfirst($t->priority) }}
                            </span>
                            <span class="text-gray-500"><i class="fa-solid fa-tag"></i> {{ $t->category }}</span>
                            <span class="text-gray-400"><i class="fa-regular fa-clock"></i> {{ $t->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-16 opacity-50">
                        <i class="fa-solid fa-headset text-6xl text-gray-300 mb-4"></i>
                        <h4 class="text-lg font-bold text-gray-500">Belum Ada Aduan</h4>
                        <p class="text-sm text-gray-400">Jika Anda mengalami kendala, klik tombol Buat Tiket Baru.</p>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
