<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

class AuthController extends Controller
{
    //1.tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }
    //2.proses data login si satpam
    public function login(Request $request)
    {
        //validasi input dulu
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        //cek database satpam bekerja
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); //buat session baru (gelang tiket)

            //cek role: jika admin ke dashboard, jika warga ke laporan
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.lapor');
            }
        }

        //jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ]);
    }

    //3.proses logout
    public function logout (Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    //4. tambahan petemuan 4 (dashboard admin)
    public function dashboard()
    {
        // logika: ambil semua laporan, urutkan dari yang tebaru
        $reports = Report::orderBy('created_at', 'desc')->get();

        //kirim data '$reports' ke view
        return view('admin.dashboard', compact('reports'));
    }
}