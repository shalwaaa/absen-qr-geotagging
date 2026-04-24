<x-app-layout>
    <x-slot name="header">
        <div class="header-section">
            <h2 class="font-semibold text-xl leading-tight">
                <span style="color: #E4EB9C; font-weight: 800;">Pusat Bantuan</span>
            </h2>
        </div>
    </x-slot>

    <style>
        :root {
            --dark-green: #142C14;
            --cal-poly: #2D5128;
            --fern: #537B2F;
            --asparagus: #8DA750;
            --mindaro: #E4EB9C;
        }

        .content-card { background: white; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); overflow: hidden; }

        /* Chat Area Default */
        .chat-area { background: #F8FAFC; }

        /* Layout Bubble Utama */
        .bubble { max-width: 75%; padding: 14px 20px; border-radius: 20px; font-size: 0.95rem; line-height: 1.5; position: relative; }

        /* BUBBLE SAYA (Kanan - Hijau) */
        .bubble-me {
            background: var(--cal-poly);
            color: white;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 15px rgba(45, 81, 40, 0.15);
        }

        /* BUBBLE ORANG LAIN (Kiri - Putih) */
        .bubble-other {
            background: white;
            color: var(--dark-green);
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        /* Form Rating ClockIn Style (Dari perbaikan sebelumnya) */
        .rating-card { border-radius: 24px; border: 1px solid #e2e8f0; background: white; box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1); }
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 15px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 3rem; color: #e5e7eb; cursor: pointer; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #fbbf24; transform: scale(1.2) rotate(5deg); }
    </style>

    <div class="py-10 px-4 bg-[#FDFDF9] min-h-screen pb-24">
        <div class="max-w-4xl mx-auto">

            <!-- HEADER TIKET -->
            <div class="flex flex-col md:flex-row justify-between md:items-end mb-6 gap-4">
                <div>
                    <span class="text-[10px] font-black text-[var(--asparagus)] uppercase tracking-widest">{{ $ticket->ticket_code }}</span>
                    <h1 class="text-3xl font-black text-[var(--dark-green)] mt-1">{{ $ticket->subject }}</h1>
                    <p class="text-xs text-gray-400 font-bold mt-1"><i class="fa-solid fa-tag"></i> Kategori: {{ $ticket->category }}</p>
                </div>
                <div>
                    <span class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border shadow-sm
                        {{ $ticket->status == 'open' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($ticket->status == 'in_progress' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200') }}">
                        <i class="fa-solid fa-circle text-[8px] mr-1"></i> {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
            </div>

            <!-- KOTAK PERCAKAPAN -->
            <div class="content-card">
                <div class="chat-area p-6 h-[550px] overflow-y-auto space-y-6">

                    <!-- 1. PESAN PERTAMA (DESKRIPSI TIKET) -->
                    @php $isMe = $ticket->user_id == auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} w-full">
                        <div class="bubble {{ $isMe ? 'bubble-me' : 'bubble-other' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-black text-[11px] {{ $isMe ? 'text-[var(--mindaro)]' : 'text-[var(--cal-poly)]' }}">
                                    {{ $ticket->user->name }}
                                </span>
                                <span class="text-[8px] font-bold px-2 py-0.5 rounded {{ $isMe ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                                    PELAPOR
                                </span>
                            </div>
                            <p class="font-medium text-[14px]">{{ $ticket->description }}</p>
                            <p class="text-[9px] mt-2 text-right {{ $isMe ? 'text-white/60' : 'text-gray-400' }} font-bold">
                                {{ $ticket->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- 2. BALASAN-BALASAN (RESPONSES) -->
                    @foreach($ticket->responses as $res)
    @php $isMe = $res->user_id == auth()->id(); @endphp
    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} w-full">
        <div class="bubble {{ $isMe ? 'bubble-me' : 'bubble-other' }}">
            <div class="flex items-center gap-2 mb-2">
                <span class="font-black text-[11px] {{ $isMe ? 'text-[var(--mindaro)]' : 'text-[var(--cal-poly)]' }}">
                    {{ $res->user->name }}
                </span>

                <!-- BADGE AUTO REPLY -->
                @if($res->is_auto_reply)
                    <span class="text-[7px] font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-tighter">
                        <i class="fa-solid fa-robot"></i> Auto Reply
                    </span>
                @endif

                <span class="text-[8px] font-bold px-2 py-0.5 rounded {{ $isMe ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $res->user->role == 'admin' ? 'OPERATOR' : 'PELAPOR' }}
                </span>
            </div>
            <p class="font-medium text-[14px]">{{ $res->message }}</p>
            <p class="text-[9px] mt-2 text-right {{ $isMe ? 'text-white/60' : 'text-gray-400' }} font-bold">
                Sent at {{ $res->created_at->format('H:i') }}
            </p>
        </div>
    </div>
@endforeach
                </div>

                <!-- FOOTER / AREA INPUT BALASAN -->
                <div class="p-6 border-t bg-white">
                    @if($ticket->status != 'closed')
                        <form action="{{ auth()->user()->role == 'admin' ? route('operator.tickets.reply', $ticket->id) : route('user.tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <textarea name="message" rows="3" required
                                class="w-full border-gray-200 bg-[#F9FAF4] rounded-2xl focus:border-[var(--cal-poly)] focus:ring-[var(--cal-poly)] p-4 text-sm placeholder:text-gray-400 mb-4 transition shadow-inner"
                                placeholder="Ketik balasan Anda di sini..."></textarea>

                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <!-- Tombol Tutup Tiket Hanya untuk Admin -->
                                @if(auth()->user()->role == 'admin')
                                    <button type="submit" formaction="{{ route('operator.tickets.close', $ticket->id) }}" formnovalidate
                                        class="text-red-500 text-[10px] font-black uppercase tracking-widest hover:text-red-700 transition flex items-center gap-1 border-b border-transparent hover:border-red-700 pb-1">
                                        <i class="fa-solid fa-lock"></i> Selesaikan & Tutup Tiket
                                    </button>
                                @else
                                    <div></div> <!-- Spacer untuk alignment -->
                                @endif

                                <button type="submit"
                                    style="background-color: var(--cal-poly);"
                                    class="text-white px-8 py-3 rounded-xl font-black transition-all active:scale-95 flex items-center gap-2 uppercase text-xs tracking-widest shadow-lg shadow-green-900/20 hover:opacity-90">
                                    Kirim Balasan <i class="fa-solid fa-paper-plane text-[var(--mindaro)]"></i>
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Tampilan Jika Tiket Sudah Selesai -->
                        <div class="text-center py-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <p class="text-gray-400 text-xs font-black uppercase tracking-widest"><i class="fa-solid fa-check-circle text-emerald-500 mr-1"></i> Tiket Ini Telah Ditutup</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- FORM RATING & HASIL PENILAIAN -->
            @if($ticket->status == 'closed' && auth()->user()->role != 'admin' && !$ticket->rating)
                <!-- Form untuk Pelapor -->
                &nbsp;
                <div class="mt-12 rating-card">
                    <div class="bg-[#2D5128] p-6 rounded-t-[24px] text-center">
                        <h3 class="font-black text-[#E4EB9C] text-lg uppercase tracking-widest">Penilaian Anda Sangat Berarti! 🌟</h3>
                        <p class="text-white/70 text-[10px] font-bold uppercase tracking-tighter mt-1">Beri rating untuk layanan operator kami</p>
                    </div>

                    <div class="p-10">
                        <form action="{{ route('user.tickets.rate', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="star-rating mb-10">
                                @for($i=5; $i>=1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="score" value="{{ $i }}" required>
                                    <label for="star{{ $i }}"><i class="fa-solid fa-star"></i></label>
                                @endfor
                            </div>
                            <div style="padding: 30px">
                                <div class="mb-8 text-left">
                                    <label class="text-[10px] font-black text-gray-400 uppercase mb-3 block tracking-widest">Pesan Tambahan (Opsional)</label>
                                    <textarea name="feedback" rows="3" class="w-full border-gray-100 rounded-2xl bg-[#F9FAF4] text-sm focus:ring-[#2D5128] focus:border-[#2D5128] p-5 placeholder:text-gray-300 shadow-inner" placeholder="Apa yang bisa kami tingkatkan lagi?"></textarea>
                                </div>

                                <div class="flex justify-center pb-2">
                                    <button type="submit"
                                        style="background-color: #2D5128 !important; color: #ffffff !important; padding: 20px;"
                                        class="hover:bg-[#142C14] px-14 py-4 rounded-2xl font-black shadow-xl shadow-green-900/30 transition-all active:scale-95 flex items-center gap-3 uppercase text-xs tracking-widest border-b-4 border-black/20 border-0">
                                        KIRIM PENILAIAN <i class="fa-solid fa-paper-plane text-[#E4EB9C]"></i>
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            @elseif($ticket->rating)
                <!-- Info Jika Sudah Memberi Rating (Dilihat oleh Admin dan Pelapor) -->
                <div class="mt-8 p-8 bg-white rounded-[24px] border border-emerald-100 text-center shadow-sm">
                    <p class="text-[#142C14] text-xs font-black uppercase tracking-widest mb-4">Hasil Penilaian Pelapor</p>
                    <div class="flex justify-center gap-2 text-yellow-400 text-3xl mb-4">
                        @for($i=1; $i<=5; $i++)
                            <i class="fa-{{ $ticket->rating->score >= $i ? 'solid' : 'regular' }} fa-star drop-shadow-sm"></i>
                        @endfor
                    </div>
                    @if($ticket->rating->feedback)
                        <p class="text-gray-500 text-sm italic mt-3 bg-gray-50 p-4 rounded-2xl border border-gray-100 font-medium">"{{ $ticket->rating->feedback }}"</p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
