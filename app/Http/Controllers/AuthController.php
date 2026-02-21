<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
            $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    if (Auth::attempt([
        'username' => $request->username,
        'password' => $request->password,
    ])) {
        $request->session()->regenerate();

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

    // TAMBAHAN PERTEMUAN 6
    // 1. Menampilkan Form Daftar
public function showRegisterForm()
{
    return view('auth.register');
}

// 2. Proses Simpan Warga Baru
public function register(Request $request)
{
    // VALIDASI: Pastikan NIK & Username belum pernah dipakai
    $data = $request->validate([
        'nik'      => 'required|numeric|unique:users',
        'name'     => 'required',
        'username' => 'required|unique:users',
        'password' => 'required|min:6',
        'telp'     => 'required|numeric',
    ]);

    // CREATE: Simpan data ke database
    User::create([
        'nik'      => $data['nik'],
        'name'     => $data['name'],
        'username' => $data['username'],
        'password' => bcrypt($data['password']), // Enkripsi wajib
        'telp'     => $data['telp'],
        'role'     => 'masyarakat', // Default role otomatis masyarakat
    ]);

    return redirect()
        ->route('login')
        ->with('success', 'Akun berhasil dibuat! Silakan login.');
}

}