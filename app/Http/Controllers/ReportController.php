<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // 1. Menampilkan halaman form laporan
    public function index() 
    { 

        // LOGIKA: Ambil laporan DIMANA (Where) id pemiliknya == ID saya yang sedang login
        $myReports = Report::where('user_id', Auth::id()) 
                        ->orderBy('created_at', 'desc')
                        ->get(); 
        return view('user.lapor', compact('myReports')); 
    }

    // 2. Menyimpan data laporan + upload foto
    public function store(Request $request)
    {
        // A. Validasi input
        $request->validate([
            'title'       => 'required|max:255',
            'description' => 'required',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // B. Proses upload foto
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        // C. Simpan ke database
        Report::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
            'image'       => $imagePath,
            'status'      => '0',
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil dikirim!');
    }
}
