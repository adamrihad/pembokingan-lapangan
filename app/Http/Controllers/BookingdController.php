<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input (Gunakan jam_mulai & jam_selesai sesuai migration)
        $request->validate([
            'field_id'   => 'required|exists:fields,id',
            'jam_mulai'  => 'required|date|after:now',
            'jam_selesai' => 'required|date|after:jam_mulai',
        ]);

        // 2. Cek apakah ada jadwal yang bentrok
        $isBooked = Booking::where('field_id', $request->field_id)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '>=', $request->jam_mulai)
                      ->where('jam_mulai', '<', $request->jam_selesai);
                })
                ->orWhere(function ($q) use ($request) {
                    $q->where('jam_selesai', '>', $request->jam_mulai)
                      ->where('jam_selesai', '<=', $request->jam_selesai);
                })
                ->orWhere(function ($q) use ($request) {
                    $q->where('jam_mulai', '<=', $request->jam_mulai)
                      ->where('jam_selesai', '>=', $request->jam_selesai);
                });
            })->exists();

        if ($isBooked) {
            return response()->json(['message' => 'Maaf, lapangan sudah dipesan pada jam tersebut.'], 422);
        }

        // 3. Ambil data lapangan untuk mendapatkan harga
        $field = Field::findOrFail($request->field_id);

        // 4. Hitung Total Harga Otomatis
        $start = Carbon::parse($request->jam_mulai);
        $end   = Carbon::parse($request->jam_selesai);
        $durationInHours = $start->diffInHours($end);
        
        // Pastikan minimal bayar 1 jam jika durasi kurang dari 1 jam
        if ($durationInHours < 1) { $durationInHours = 1; }

        $totalPrice = $durationInHours * $field->harga_per_jam;

        // 5. Simpan ke Database
        $booking = Booking::create([
            'user_id'     => auth()->id(), // Diambil otomatis dari Token JWT
            'field_id'    => $request->field_id,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'total_harga' => $totalPrice,
            'status'      => 'pending' // Kita set langsung confirmed untuk latihan ini
        ]);

        return response()->json([
            'message' => 'Booking berhasil!',
            'data'    => $booking
        ], 201);
    }

    public function myBookings() {
        $data = Booking::where('user_id', auth()->id())->with('field')->get();
        return response()->json($data);
    }

    // Fungsi untuk Admin mengonfirmasi pembayaran
public function updateStatus(Request $request, $id)
{
    // Validasi input status
    $request->validate([
        'status' => 'required|in:pending,success,cancelled'
    ]);

    $booking = Booking::findOrFail($id);
    
    $booking->update([
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Status pembayaran diperbarui menjadi: ' . $request->status,
        'data' => $booking
    ]);
}

}
