@extends('layout')

@section('content')
{{-- Notifikasi Error Validasi --}}
@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="fw-bold text-uppercase small mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Gagal Memperbarui:</div>
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
        max-width: 850px;
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
        cursor: not-allowed;
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

    .section-subtitle {
        font-weight: 700;
        color: #0f172a;
        font-size: 1rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
    }
</style>

<div class="container-fluid">
    <h4 class="page-title"><i class="fas fa-pen-to-square me-2 text-primary"></i> Edit Data Barang Jual</h4>

    <div class="form-container text-start">
        <form method="POST" action="{{ route('barangjual.update', $item->kd_brgjual) }}">
            @csrf
            @method('PUT')

            <div class="section-subtitle text-primary">Identitas Produk</div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label text-muted">Kode Barang</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                        <input type="text" class="form-control form-control-with-icon" value="{{ $item->kd_brgjual }}" disabled>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-box-open"></i></span>
                        <input type="text" name="nm_brgjual" class="form-control form-control-with-icon text-uppercase" 
                               value="{{ old('nm_brgjual', $item->nm_brgjual) }}" required>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-update flex-grow-1 shadow-sm">
                    <i class="fas fa-sync-alt me-2"></i> Perbarui Data Barang
                </button>
                <a href="{{ route('barangjual.index') }}" class="btn btn-back px-4">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection