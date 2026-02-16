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
    .form-control-custom:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }
    .btn-save { background: #0f172a; color: white; border-radius: 12px; padding: 12px; font-weight: 700; border: none; width: 100%; transition: 0.3s; }
</style>

<div class="container py-4">
    <div class="text-center mb-4">
        <h4 class="page-title">Tambah Master Barang Jadi</h4>
        <p class="text-muted fw-bold small">INPUT IDENTITAS BARANG BARU</p>
    </div>

    <div class="form-container">
        <form action="{{ route('barangjadi.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-custom">Kode Barang</label>
                    <input type="text" name="kd_brgjadi" class="form-control form-control-custom text-uppercase" placeholder="Contoh: BJ-001" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Nama Barang</label>
                    <input type="text" name="nm_brgjadi" class="form-control form-control-custom" placeholder="Nama Produk" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom text-primary">Jumlah Awal (Stok)</label>
                    <input type="number" step="any" name="jmlh_brgjadi" class="form-control form-control-custom" value="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom text-success">HPP Satuan (Rp)</label>
                    <input type="number" name="hpp_unit" class="form-control form-control-custom" placeholder="0" required>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-save">SIMPAN DATA BARANG</button>
                    <a href="{{ route('barangjadi.index') }}" class="btn btn-link w-100 mt-2 text-muted fw-bold text-decoration-none">BATAL</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection