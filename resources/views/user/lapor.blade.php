@extends('layouts.master')

@section('title', 'Tulis Pengaduan')

@section('content')
<div class="row">
    {{-- TEKS BERJALAN --}}
    <div class="alert alert-info w-100" role="alert">
        <marquee direction="left" scrollamount="8">
            <strong>Selamat datang di aplikasi SIGAP! {{ Auth::user()->name }}</strong>
            Gunakan fitur laporan pengaduan untuk menyampaikan keluhan terkait layanan publik di wilayah Anda.
        </marquee>
    </div>

    {{-- KOLOM KIRI: FORM LAPOR --}}
    <div class="col-md-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow">
            <div class="card-header bg-primary text-white">Tulis Laporan Baru</div>

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
                <form action="{{ route('user.lapor.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label>Judul Laporan</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Jalan Berlubang" required>
                    </div>

                    <div class="mb-3">
                        <label>Isi Keluhan</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Kejadian</label>
                        <input type="text" name="location" id="location_text" class="form-control mb-2" placeholder="Geser marker di peta, alamat akan muncul di sini..." required>

                        {{-- Wadah Peta --}}
                        <div id="map" style="height: 300px; border-radius: 10px; border: 1px solid #ccc;"></div>

                        {{-- Koordinat tersembunyi --}}
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>

                    <div class="mb-3">
                        <label>Bukti Foto</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Format JPG/PNG, Maks 2MB</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">KIRIM LAPORAN</button>
                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: RIWAYAT LAPORAN --}}
    <div class="col-md-7">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Riwayat Laporan Saya</div>
            <div class="card-body">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Judul & Tanggal</th>
                            <th>Status & Balasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $item)
                            @php $responses = $item->responses; @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item->title }}</strong><br>
                                    <small class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                                    @if ($item->image)
                                        <br>
                                        <img src="{{ asset('storage/' . $item->image) }}" width="80" class="mt-2 rounded" alt="Foto laporan">
                                    @endif
                                </td>

                                <td style="min-width: 300px;">
                                    {{-- LABEL STATUS --}}
                                    <div class="mb-3">
                                        @if ($item->status == '0')
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">Menunggu</span>
                                        @elseif($item->status == 'proses')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Sedang Diproses</span>
                                        @else
                                            <span class="badge bg-success px-3 py-2 rounded-pill">Selesai</span>
                                        @endif
                                    </div>

                                    {{-- TIMELINE VERTICAL --}}
                                    @if($responses->count() > 0)
                                        <div class="ps-3 mt-2" style="border-left: 3px solid #dee2e6;">
                                            @foreach($responses as $resp)
                                                <div class="position-relative mb-3">
                                                    <span class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -23px; top: 4px; border: 2px solid white;"></span>
                                                    <div class="bg-light p-3 rounded-3 border shadow-sm">
                                                        <small class="text-primary fw-bold d-block mb-1">{{ $resp->created_at->format('d M Y, H:i') }}</small>
                                                        <p class="mb-2 text-dark small"><strong>Petugas:</strong> {{ $resp->response_text }}</p>
                                                        @if($resp->image)
                                                            <img src="{{ asset('storage/' . $resp->image) }}" class="img-fluid rounded border" style="max-height: 100px;" alt="Foto balasan petugas">
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted small mt-2"><em>Belum ada tindakan dari petugas.</em></p>
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

{{-- --------------------- LEAFLET & AUTOLOCATE --------------------- --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi map
    var map = L.map('map').setView([-6.2088, 106.8456], 12); // fallback Jakarta
    var marker = L.marker([-6.2088, 106.8456], { draggable: true }).addTo(map);

    // Tile layer OSM
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Reverse geocoding
    function getAddress(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(res => res.json())
            .then(data => document.getElementById("location_text").value = data.display_name)
            .catch(err => console.log("Gagal ambil alamat", err));
    }

    // Drag marker
    marker.on('dragend', function(e){
        const latlng = marker.getLatLng();
        document.getElementById("latitude").value = latlng.lat;
        document.getElementById("longitude").value = latlng.lng;
        getAddress(latlng.lat, latlng.lng);
    });

    // Klik map
    map.on('click', function(e){
        marker.setLatLng(e.latlng);
        document.getElementById("latitude").value = e.latlng.lat;
        document.getElementById("longitude").value = e.latlng.lng;
        getAddress(e.latlng.lat, e.latlng.lng);
    });

    // Autolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position){
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            map.setView([lat, lng], 16);
            marker.setLatLng([lat, lng]);
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
            getAddress(lat, lng);
        }, function(error){
            console.warn("Geolocation error:", error);
            // tetap fallback Jakarta
            map.setView([-6.2088, 106.8456], 12);
        });
    }
});
</script>

@endsection