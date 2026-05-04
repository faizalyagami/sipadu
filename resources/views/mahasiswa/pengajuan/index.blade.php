@extends('layouts.app')

@section('title', 'Buat Surat Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Pengajuan Surat</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('mahasiswa.pengajuan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Jenis Surat</label>
                            <select name="jenis_surat_id" class="form-select" required>
                                <option value="">Pilih Jenis Surat</option>
                                @foreach($jenisSurats as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama_surat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keperluan</label>
                            <textarea name="keperluan" class="form-control" rows="4" required placeholder="Jelaskan keperluan pengajuan surat..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajukan Surat</button>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection