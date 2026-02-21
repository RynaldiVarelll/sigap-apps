<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // 1. Menampilkan halaman form laporan
    public function index() 
    {
        // Query: "Tampilkan laporan milik SAYA saja"
        // Logika: WHERE user_id = ID Saya yang sedang login
        $reports = Report::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('user.lapor', compact('reports'));
    }


    // 2. Menyimpan data laporan + upload foto
    public function store(Request $request)
    {
        // A. Validasi input
        $request->validate([
            'title'       => 'required|max:255',
            'description' => 'required',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // B. Proses upload foto
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        // C. Simpan ke database
        Report::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location, // Nama Lokasi (Opsional)
            'latitude' => $request->latitude, // [BARU] Koordinat Lat
            'longitude' => $request->longitude, // [BARU] Koordinat Long
            'image' => $imagePath,
            'status' => '0',
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil dikirim!');
    }

    // 3. Menampilkan Detail Laporan (Langkah 3)
public function show(Report $report)
{
    // Mengambil data detail laporan beserta User (pelapor)
    // dan Responses (tanggapan) jika ada.
    // Konsep: Route Model Binding (otomatis cari data berdasarkan ID)
    $report->load(['user', 'responses.user']);

    return view('admin.detail', compact('report'));
}

// 4. Update Status Laporan (Langkah 4 - dimasukkan sekarang biar aman)
public function update(Request $request, Report $report)
{
    // Validasi: status hanya boleh berisi 0, proses, atau selesai
    $data = $request->validate([
        'status' => 'required|in:0,proses,selesai',
    ]);

    // Simpan perubahan ke database
    $report->update($data);

    // Kembali ke halaman sebelumnya dengan pesan sukses
    return back()->with('success', 'Status laporan berhasil diperbarui!');
}

// 5 Fungsi Cetak PDF
 public function exportPdf() {
        // Ambil semua data laporan
        $reports = Report::all();
        // Load View khusus PDF (nanti kita buat)
        $pdf = Pdf::loadView('admin.print', ['reports' => $reports]);
    // Download file
    return $pdf->download('laporan-pengaduan.pdf');
}

}