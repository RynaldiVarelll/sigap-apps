@extends('layouts.master')

@section('title', 'Tulis Pengaduan')

@section('content')
<div class="row">
    {{-- TEKS BERJALAN --}}
    <div class="alert alert-info w-100" role="alert">
        <marquee direction="left" scrollamount="8">
            <strong> Selamat datang di aplikasi SIGAP! {{ Auth::user()->name }}</strong>
            Gunakan fitur laporan pengaduan untuk menyampaikan keluhan
            terkait layanan publik di wilayah Anda.
        </marquee>

    </div>
    {{-- KOLOM KIRI: FORM LAPOR --}}
    <div class="col-md-5">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                Tulis Laporan Baru
            </div>

        @if ($errors->any())
            <div style="color:red; margin-bottom:15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            <div class="card-body">
                <form action="{{ route('user.lapor.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label>Judul Laporan</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               placeholder="Contoh: Jalan Berlubang"
                               required>
                    </div>

                    <div class="mb-3">
                        <label>Isi Keluhan</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="4"
                                  required></textarea>
                    </div>

                    <div class="mb-3">
                    <label class="form-label">Lokasi Kejadian</label>

                    {{-- Input alamat (otomatis dari peta, tapi bisa diedit manual) --}}
                    <input 
                        type="text"
                        name="location"
                        id="location_text"
                        class="form-control mb-2"
                        placeholder="Geser marker di peta, alamat akan muncul di sini..."
                        required
                    >

                    {{-- Wadah peta --}}
                    <div id="map" style="height: 300px; border-radius: 10px; border: 1px solid #ccc;"></div>

                    {{-- Koordinat tersembunyi --}}
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </div>

                <script>
                    // Koordinat awal (Jakarta default)
                    var defaultLat = -6.200000;
                    var defaultLng = 106.816666;

                    // Inisialisasi peta
                    var map = L.map('map').setView([defaultLat, defaultLng], 13);

                    // Tile layer OpenStreetMap
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);

                    // Marker draggable
                    var marker = L.marker([defaultLat, defaultLng], {
                        draggable: true
                    }).addTo(map);

                    // Fungsi ambil alamat dari koordinat
                    function getAddress(lat, lng) {
                        document.getElementById("location_text").value = "Sedang mencari lokasi...";

                        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.display_name) {
                                    document.getElementById("location_text").value = data.display_name;
                                } else {
                                    document.getElementById("location_text").value = "Alamat tidak ditemukan (Isi manual saja)";
                                }
                            })
                            .catch(error => {
                                document.getElementById("location_text").value = "Alamat tidak ditemukan (Isi manual saja)";
                                console.error(error);
                            });
                    }

                    // Event saat marker digeser
                    marker.on('dragend', function () {
                        var coord = marker.getLatLng();

                        document.getElementById("latitude").value = coord.lat;
                        document.getElementById("longitude").value = coord.lng;

                        getAddress(coord.lat, coord.lng);
                    });

                    // Event saat klik peta
                    map.on('click', function (e) {
                        marker.setLatLng(e.latlng);

                        document.getElementById("latitude").value = e.latlng.lat;
                        document.getElementById("longitude").value = e.latlng.lng;

                        getAddress(e.latlng.lat, e.latlng.lng);
                    });
                    </script>


                    <div class="mb-3">
                        <label>Bukti Foto</label>
                        <input type="file"
                               name="image"
                               class="form-control">
                        <small class="text-muted">
                            Format JPG/PNG, Maks 2MB
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        KIRIM LAPORAN
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: TABEL RIWAYAT --}}
    <div class="col-md-7">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                Riwayat Laporan Saya
            </div>

            <div class="card-body">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Status & Balasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $item)
                            <tr>

                                {{-- KOLOM 1: DATA LAPORAN --}}
                                <td>
                                    <strong>{{ $item->title }}</strong><br>
                                    <small class="text-muted">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </small>

                                    {{-- Foto laporan warga --}}
                                    @if ($item->image)
                                        <br>
                                        <img src="{{ asset('storage/' . $item->image) }}"
                                            width="80"
                                            class="mt-2 rounded">
                                    @endif
                                </td>

                                {{-- KOLOM 2: STATUS & BALASAN --}}
                                <td>

                                    {{-- Label status --}}
                                    @if ($item->status == '0')
                                        <span class="badge bg-danger">Menunggu</span>
                                    @elseif ($item->status == 'proses')
                                        <span class="badge bg-warning">Diproses</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif

                                    {{-- Pesan balasan admin --}}
                                    @if ($item->responses->count() > 0)
                                        @php
                                            $lastResponse = $item->responses->last();
                                        @endphp

                                        <div class="mt-2 p-2 border rounded bg-light">
                                            <small>
                                                <strong>Admin:</strong> {{ $lastResponse->response_text }}
                                            </small>

                                            {{-- Foto balasan admin --}}
                                            @if ($lastResponse->image)
                                                <br>
                                                <img src="{{ asset('storage/' . $lastResponse->image) }}"
                                                    width="100"
                                                    class="mt-1 rounded border">
                                            @endif
                                        </div>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
