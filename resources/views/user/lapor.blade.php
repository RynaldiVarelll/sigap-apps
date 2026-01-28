@extends('layouts.master')

@section('title', 'Tulis Pengaduan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- alert sukses --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- card form laporan --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Tulis Laporan Baru
            </div>

            <div class="card-body">
                <form action="{{ route('user.lapor.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Judul Laporan</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               placeholder="Contoh: Jalan Berlubang"
                               value="{{ old('title') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Keluhan</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="5"
                                  required>{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi Kejadian</label>
                        <input type="text"
                               name="location"
                               class="form-control"
                               placeholder="Contoh: Depan Pasar Cibinong"
                               value="{{ old('location') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti Foto</label>
                        <input type="file"
                               name="image"
                               class="form-control">
                        <small class="text-muted">
                            Format JPG/PNG, maksimal 2MB
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        KIRIM LAPORAN
                    </button>
                </form>
            </div>
        </div>

        {{-- riwayat laporan --}}
        <div class="card">
            <div class="card-header bg-success text-white">
                Riwayat Laporan Saya
            </div>

            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($myReports as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                <td>{{ $item->title }}</td>
                                <td>
                                    @if ($item->status == 0)
                                        <span class="badge bg-danger">Menunggu</span>
                                    @elseif ($item->status == 'proses')
                                        <span class="badge bg-warning">Diproses</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Belum ada laporan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
