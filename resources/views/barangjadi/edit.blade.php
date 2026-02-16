@extends('layout')

@section('content')
<style>
    .page-title { font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.02em; }
    .form-container { 
        background: #ffffff; padding: 40px; border-radius: 20px; 
        border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02); 
        max-width: 800px; margin: 0 auto;
    }
    .form-label-custom { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; display: block; }
    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 12px 15px; font-weight: 600; transition: 0.3s; }
    .form-control-custom:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .btn-update { background: #3b82f6; color: white; border-radius: 12px; padding: 12px; font-weight: 700; border: none; width: 100%; transition: 0.3s; }
    .btn-update:hover { background: #2563eb; transform: translateY(-2px); }
</style>

<div class="container py-4">
    <div class="text-center mb-4">
        <h4 class="page-title">Edit Barang Jadi</h4>
        <p class="text-muted fw-bold small">KODE BARANG: <span class="text-primary">{{ $barangjadi->kd_brgjadi }}</span></p>
    </div>

    <div class="form-container">
        {{-- PENTING: Menggunakan $barangjadi->id sebagai identifier utama --}}
        <form action="{{ route('barangjadi.update', $barangjadi->id) }}" method="POST">
            @csrf 
            @method('PUT')
            
            <div class="row g-4">
                {{-- Nama Barang --}}
                <div class="col-md-12">
                    <label class="form-label-custom">Nama Barang</label>
                    <input type="text" name="nm_brgjadi" class="form-control form-control-custom" value="{{ $barangjadi->nm_brgjadi }}" required>
                </div>

                {{-- Proyek / Konsumen --}}
                <div class="col-md-12">
                    <label class="form-label-custom text-info">Proyek / Konsumen</label>
                    <select name="nm_kons" class="form-select form-control-custom" required>
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($konsumen as $k)
                            <option value="{{ $k->nm_kons }}" {{ $barangjadi->nm_kons == $k->nm_kons ? 'selected' : '' }}>
                                {{ $k->nm_kons }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jumlah Stok --}}
                <div class="col-md-6">
                    <label class="form-label-custom text-primary">Jumlah (Stok)</label>
                    <input type="number" step="any" name="jmlh_brgjadi" class="form-control form-control-custom" value="{{ $barangjadi->jmlh_brgjadi }}">
                </div>

                {{-- HPP Satuan --}}
                <div class="col-md-6">
                    <label class="form-label-custom text-success">HPP Satuan (Rp)</label>
                    <input type="number" name="hpp_unit" class="form-control form-control-custom" value="{{ $barangjadi->hpp_unit }}" required>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-update shadow">SIMPAN PERUBAHAN</button>
                    <a href="{{ route('barangjadi.index') }}" class="btn btn-link w-100 mt-2 text-muted fw-bold text-decoration-none">BATAL</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection