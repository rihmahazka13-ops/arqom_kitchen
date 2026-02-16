@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }

    .form-container {
        background: #ffffff;
        padding: 40px;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    }

    .page-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.8rem;
        letter-spacing: -0.02em;
    }

    .form-label {
        font-weight: 700;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: block;
    }

    .input-group-custom {
        display: flex;
        width: 100%;
        margin-bottom: 5px;
    }

    .input-group-text-custom {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-right: none;
        border-radius: 12px 0 0 12px !important;
        color: #94a3b8;
        min-width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 0 12px 12px 0 !important;
        border: 1px solid #cbd5e1;
        padding: 14px 18px;
        font-size: 0.95rem;
        color: #0f172a !important;
        font-weight: 500;
        background-color: #fff;
        width: 100%;
        transition: all 0.3s ease;
    }

    /* Style untuk input otomatis yang tidak bisa diubah */
    .form-control-custom:read-only {
        background-color: #f1f5f9;
        color: #64748b !important;
        cursor: not-allowed;
    }

    .form-select-custom {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px 12px;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .btn-save {
        background-color: #0f172a;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 16px 30px;
        transition: 0.3s;
        letter-spacing: 0.5px;
    }

    .btn-save:hover {
        background-color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.1);
        color: white;
    }

    .section-subtitle {
        font-weight: 800;
        color: #3b82f6;
        font-size: 0.95rem;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title"><i class="fas fa-plus-circle me-2 text-primary"></i> Tambah Produksi</h4>
            <p class="text-muted fw-bold small">INPUT HASIL PRODUKSI DAN ESTIMASI BIAYA OPERASIONAL</p>
        </div>
        <a href="{{ route('barangproduksi.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-2"></i> KEMBALI
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm mb-4 border-0" style="border-radius: 12px;">
            <ul class="mb-0 small fw-bold">
                @foreach ($errors->all() as $err)
                    <li><i class="fas fa-times me-1"></i> {{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('barangproduksi.store') }}" method="POST">
            @csrf

            <div class="section-subtitle">Identitas Produksi</div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Kode Produksi (Otomatis)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-barcode"></i></span>
                        {{-- UPDATE: Menggunakan $kd_otomatis dan readonly --}}
                        <input type="text" name="kd_bhnpro" class="form-control-custom fw-bold" 
                               value="{{ $kd_otomatis }}" readonly required>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label">Konsumen / Proyek</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-user-circle"></i></span>
                        <select name="nm_kons" class="form-select-custom" required>
                            <option value="" selected disabled>-- Pilih Konsumen / Proyek --</option>
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
                    <label class="form-label">Nama Item Produksi</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-box-open"></i></span>
                        <input type="text" name="bhnpro" class="form-control-custom text-uppercase" value="{{ old('bhnpro') }}" placeholder="MASUKKAN NAMA BARANG PRODUKSI..." required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Kalkulasi Unit & Biaya</div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Jumlah Unit (QTY)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-layer-group"></i></span>
                        <input type="number" name="jml_pro" class="form-control-custom fw-bold" value="{{ old('jml_pro') }}" placeholder="0" required>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label text-primary">Estimasi HPP Per Unit</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom text-primary fw-bold">Rp</span>
                        <input type="number" name="est_hpp" class="form-control-custom fw-bold" value="{{ old('est_hpp') }}" placeholder="0">
                    </div>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Total Estimasi akan dihitung otomatis oleh sistem.</small>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex">
                <button type="submit" class="btn btn-save flex-grow-1 shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> Simpan Data Produksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection