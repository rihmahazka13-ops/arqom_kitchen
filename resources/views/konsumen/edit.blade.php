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
        max-width: 800px; /* Lebar maksimal agar form tidak terlalu melebar */
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

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    .form-control:disabled {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
    }

    /* Input Group Icon Styling */
    .input-group-text {
        background-color: #ffffff;
        border-radius: 12px 0 0 12px !important;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
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

    .btn-update {
        background-color: #0f172a;
        border: none;
        color: white;
    }

    .btn-update:hover {
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
</style>

<div class="container-fluid">
    <h4 class="page-title"><i class="fas fa-user-pen me-2 text-primary"></i> Edit Data Konsumen</h4>

    <div class="form-container">
        <form method="POST" action="{{ route('konsumen.update', $k->kd_kons) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted">Kode Konsumen (Sistem)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="text" class="form-control form-control-with-icon" value="{{ $k->kd_kons }}" disabled>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="nm_kons" class="form-control form-control-with-icon" value="{{ $k->nm_kons }}" required placeholder="Masukkan nama konsumen">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <label class="form-label">Kota</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                        <input type="text" name="kota_kons" class="form-control form-control-with-icon" value="{{ $k->kota_kons }}" placeholder="Nama Kota">
                    </div>
                </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kontak</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="kontak_kons" class="form-control form-control-with-icon" value="{{ $k->kontak_kons }}" placeholder="08123456789">
                    </div>
                </div>
</div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-update flex-grow-1 shadow-sm">
                    <i class="fas fa-save me-2"></i> Perbarui Data Konsumen
                </button>
                <a href="{{ route('konsumen.index') }}" class="btn btn-back px-4">
                    <i class="fas fa-arrow-left me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection