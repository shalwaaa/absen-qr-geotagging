<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class HolidayController extends Controller
{
    // 1. Tampilkan Halaman
    public function index()
    {
        // Ambil semua data libur (Tanpa Pagination) - URUTKAN berdasarkan tanggal
        $holidays = Holiday::orderBy('date', 'desc')->get();

        // Format data agar sesuai dengan FullCalendar
        $events = $holidays->map(function ($h) {
            return [
                'id' => $h->id,
                'title' => $h->title,
                'start' => $h->date->format('Y-m-d'),
                'color' => $h->type == 'national' ? '#2D5128' : '#d97706',
                'description' => $h->description ?? '',
                'extendedProps' => [
                    'type' => $h->type
                ]
            ];
        });

        // Kirim tahun untuk dropdown sync (5 tahun ke depan & belakang)
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 2); // 2024-2028 misalnya

        return view('admin.holidays.index', compact('holidays', 'events', 'years'));
    }

    // 2. Simpan Libur Manual
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
            'description' => 'nullable|string'
        ]);

        Holiday::create([
            'title' => $request->title,
            'date' => $request->date,
            'description' => $request->description,
            'type' => 'manual'
        ]);

        return back()->with('success', 'Libur manual berhasil ditambahkan.');
    }

    // 3. Hapus Libur
    public function destroy(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        // Cek apakah request dari AJAX/Fetch?
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        }

        // Kalau request biasa (non-ajax), pakai cara lama
        return back()->with('success', 'Hari libur dihapus.');
    }

    // 4. LOGIC SINKRONISASI API NASIONAL (API DENO)
    public function syncNational(Request $request)
    {
        // Ambil tahun dari request, atau default tahun ini
        $year = $request->input('year', date('Y'));

        // Format Response: [{"date": "2024-01-01", "name": "Tahun Baru Masehi", "is_holiday": true}, ...]
        $apiUrl = "https://libur.deno.dev/api?year=" . $year;

        // Coba tarik data dari API
        try {
            $response = Http::timeout(10)->get($apiUrl);
            
            // Cek apakah response sukses
            if ($response->successful()) {
                $holidays = $response->json(); 
                $count = 0;

                // Proses setiap data libur dari API
                foreach ($holidays as $h) {
                    if (isset($h['is_holiday']) && $h['is_holiday'] == false) {
                        continue;
                    }

                    $date = $h['date'];
                    $title = $h['name'];

                    // Cek Duplikat
                    $exists = Holiday::where('date', $date)->exists();
                    
                    if (!$exists) {
                        Holiday::create([
                            'title' => $title,
                            'date' => $date,
                            'type' => 'national',
                            'description' => 'Disinkronkan otomatis (API Deno).'
                        ]);
                        $count++;
                    }
                }
                
                // Kembalikan pesan sukses dengan jumlah data yang ditambahkan
                return back()->with('success', "Berhasil menarik {$count} hari libur nasional tahun {$year}.");
            } else {
                return back()->with('error', 'Gagal menghubungi server API Libur.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 5. Tampilkan Kalender Libur (FullCalendar)
    public function calendar()
{
    $holidays = Holiday::all();

    $events = $holidays->map(function ($h) {
        return [
            'title' => $h->title,
            'start' => $h->date->format('Y-m-d'),
            'color' => $h->type == 'national' ? '#2D5128' : '#d97706',
            'extendedProps' => [
                'description' => $h->description ?? 'Tidak ada keterangan tambahan.',
                'type' => $h->type 
            ]
        ];
    });

    return view('calendar', compact('events'));
}
}