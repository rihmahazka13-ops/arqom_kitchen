@extends('layout')

@section('content')
<style>
    /* Styling Formal & Tajam */
    .page-title {
        font-weight: 800;
        color: #0f172a; /* Slate Dark */
        text-transform: uppercase;
        letter-spacing: -0.02em;
        margin-bottom: 25px;
    }

    .form-container {
        background: #ffffff;
        padding: 35px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        max-width: 850px; /* Lebar optimal untuk kenyamanan mata */
    }

    /* Label & Input Styling */
    .form-label {
        font-weight: 700;
        color: #475569; /* Slate Gray */
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        color: #1e293b;
        transition: all 0.3s ease;
    }

    /* Style khusus untuk input otomatis yang terkunci */
    .form-control:read-only {
        background-color: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
        border-style: dashed;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        background-color: #fff;
    }

    /* Input Group Icon Styling */
    .input-group-text {
        background-color: #f8fafc;
        border-radius: 12px 0 0 12px !important;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        min-width: 45px;
        justify-content: center;
    }

    .form-control-with-icon {
        border-radius: 0 12px 12px 0 !important;
    }

    /* Button Styling */
    .btn {
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 25px;
        transition: 0.3s;
    }

    .btn-save {
        background-color: #0f172a; /* Slate Dark Button */
        border: none;
        color: white;
    }

    .btn-save:hover {
        background-color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-back {
        border: 2px solid #e2e8f0;
        color: #64748b;
        background: transparent;
    }

    .btn-back:hover {
        background-color: #f8fafc;
        color: #1e293b;
    }

    .required-star {
        color: #ef4444;
    }
</style>

<div class="container-fluid">
    <h4 class="page-title"><i class="fas fa-user-plus me-2 text-primary"></i> Registrasi Konsumen Baru</h4>

    <div class="form-container">
        <form method="POST" action="{{ route('konsumen.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kode Konsumen (Otomatis) <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                        {{-- UPDATE: Menggunakan $kd_otomatis dan readonly --}}
                        <input type="text" name="kd_kons" class="form-control form-control-with-icon fw-bold" 
                               value="{{ $kd_otomatis }}" readonly required>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Nama Lengkap <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        {{-- UPDATE: Autofocus dipindah ke sini --}}
                        <input type="text" name="nm_kons" class="form-control form-control-with-icon" 
                               placeholder="Masukkan nama lengkap" required autofocus>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kota / Kabupaten</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                        <input type="text" name="kota_kons" class="form-control form-control-with-icon" 
                               placeholder="Masukkan nama kota">
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Kontak</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone-flip"></i></span>
                        <input type="text" name="kontak_kons" class="form-control form-control-with-icon" 
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-save flex-grow-1 shadow-sm">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Simpan Data Konsumen
                </button>
                <a href="{{ route('konsumen.index') }}" class="btn btn-back px-4">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection