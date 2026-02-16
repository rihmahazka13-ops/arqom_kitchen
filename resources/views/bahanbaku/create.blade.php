@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #475569;
        --accent-blue: #0f172a; 
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    .page-title { 
        font-weight: 800; 
        color: #000000; 
        text-transform: uppercase; 
        font-size: 1.4rem;
        margin-bottom: 25px;
        letter-spacing: -0.02em;
    }

    .form-container {
        background: #ffffff;
        padding: 40px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        max-width: 900px;
    }

    .form-label {
        font-weight: 700;
        color: #000000; 
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
        display: block;
        white-space: nowrap;
    }

    .input-group-custom {
        display: flex;
        width: 100%;
    }

    .input-group-text-custom {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: none;
        border-radius: 12px 0 0 12px !important;
        color: #64748b;
        min-width: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 0 12px 12px 0 !important;
        border: 1px solid #e2e8f0;
        padding: 14px 18px;
        font-size: 0.95rem;
        color: #000000; 
        width: 100%;
        transition: all 0.3s ease;
        background-color: #fff;
    }

    /* Style khusus untuk input readonly agar terlihat berbeda sedikit tapi tetap elegan */
    .form-control-custom:read-only {
        background-color: #f1f5f9;
        cursor: not-allowed;
        color: #475569;
    }

    .form-control-custom:focus {
        border-color: #000000;
        box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
        outline: none;
    }

    .btn-save {
        background: #000000; 
        border: none;
        color: white !important;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 16px 30px;
        transition: 0.3s;
        letter-spacing: 1px;
    }

    .btn-save:hover {
        background: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .section-subtitle {
        font-weight: 800;
        color: #000000; 
        font-size: 0.95rem;
        margin-bottom: 30px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
    }

    .section-subtitle::after {
        content: "";
        flex-grow: 1;
        height: 1px;
        background: #f1f5f9;
        margin-left: 15px;
    }

    .alert-custom {
        border-radius: 12px;
        padding: 18px;
        border: none;
        font-weight: 600;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-plus-circle me-2 text-dark"></i> Tambah Inventaris Bahan Baku</h4>

    @if (session('error'))
        <div class="alert alert-danger alert-custom shadow-sm mb-4" style="background-color: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('bahanbaku.store') }}" method="POST">
            @csrf

            <div class="section-subtitle">Informasi Utama & Relasi</div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Kode Bahan Baku (Otomatis)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-barcode"></i></span>
                        {{-- Bagian yang diperbarui: Value mengambil $kd_otomatis dan diset readonly --}}
                        <input name="kd_bhn" class="form-control-custom fw-bold" value="{{ $kd_otomatis }}" 
                               readonly required>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label">Konsumen / Proyek</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-user-circle"></i></span>
                        <select name="nm_kons" class="form-select-custom" required>
                            <option value="" selected disabled>-- Pilih Konsumen --</option>
                            @foreach($konsumen as $k)
                                <option value="{{ $k->nm_kons }}" {{ old('nm_kons') == $k->nm_kons ? 'selected' : '' }}>
                                    {{ $k->kd_kons }} - {{ $k->nm_kons }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label">Nama Bahan Baku</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-box"></i></span>
                        <input name="nm_bhn" class="form-control-custom text-uppercase fw-bold" 
                               value="{{ old('nm_bhn') }}" placeholder="Masukkan nama bahan" required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Stok & Satuan</div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Jumlah (Qty)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-layer-group"></i></span>
                        <input type="number" name="jml_bhn" class="form-control-custom fw-bold" 
                               value="{{ old('jml_bhn') }}" placeholder="0" required>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Satuan</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-tag"></i></span>
                        <input name="satuan_bhn" class="form-control-custom" 
                               value="{{ old('satuan_bhn') }}" placeholder="Kg, Pcs, Liter..." required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Finansial</div>
            
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label">Harga Satuan (Beli)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom text-dark fw-bold">Rp</span>
                        <input type="number" name="harga_bhn" class="form-control-custom fw-bold" 
                               value="{{ old('harga_bhn') }}" placeholder="0" required>
                    </div>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-info-circle me-1"></i> Total harga akan dihitung otomatis oleh sistem.
                    </p>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-save flex-grow-1">
                    <i class="fas fa-check-circle me-2"></i> Simpan Bahan Baku Baru
                </button>
                <a href="{{ route('bahanbaku.index') }}" class="btn btn-outline-secondary px-4 fw-bold d-flex align-items-center" 
                   style="border-radius: 12px; color: #000; border-color: #e2e8f0;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection