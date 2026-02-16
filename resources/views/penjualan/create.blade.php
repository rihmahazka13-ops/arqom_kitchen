@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    /* Styling Formal & Tajam */
    .page-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
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
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        max-width: 1000px;
    }

    /* Label & Input Styling */
    .form-label {
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 12px 15px;
        font-size: 0.95rem;
        color: #0f172a;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Input Group Icon Styling */
    .input-group-text {
        background-color: #f1f5f9;
        border-radius: 12px 0 0 12px !important;
        border: 1px solid #cbd5e1;
        color: #64748b;
        font-weight: 700;
        min-width: 45px;
        justify-content: center;
    }

    /* Styling khusus untuk input readonly agar terlihat berbeda */
    .form-control[readonly] {
        background-color: #f1f5f9;
        cursor: not-allowed;
        font-weight: 600;
        color: #64748b;
    }

    .btn-save {
        background-color: #0f172a; /* Navy Dark Arqom Kitchen */
        border: none;
        color: white;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 15px 25px;
        transition: 0.3s;
        letter-spacing: 0.5px;
    }

    .btn-save:hover {
        background-color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
    }

    .section-subtitle {
        font-weight: 800;
        color: #3b82f6; /* Blue Accent */
        font-size: 0.9rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Input Penjualan Baru</h4>

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <div class="fw-bold small mb-2 text-uppercase"><i class="fas fa-exclamation-triangle me-2"></i> Gagal Menyimpan:</div>
            <ul class="mb-0 small fw-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form method="POST" action="{{ route('penjualan.store') }}">
            @csrf

            <div class="section-subtitle">Informasi Transaksi</div>
            <div class="row">
                {{-- Kode Penjualan (DIUBAH MENJADI OTOMATIS & READONLY) --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Kode Penjualan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="kd_penj" class="form-control" 
                               value="{{ $kd_otomatis }}" readonly required>
                    </div>
                </div>

                {{-- Tanggal Penjualan --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Tanggal Penjualan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_penj" class="form-control" 
                               value="{{ old('tgl_penj', date('Y-m-d')) }}" required>
                    </div>
                </div>

                {{-- Invoice --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">No. Invoice</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                        <input type="text" name="invoice" class="form-control" 
                               placeholder="INV/2026/..." value="{{ old('invoice') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Nama Konsumen --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Nama Konsumen / Proyek <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                        <select name="nm_kons" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Konsumen / Proyek --</option>
                            @foreach($konsumen as $ks)
                                <option value="{{ $ks->nm_kons }}" {{ old('nm_kons') == $ks->nm_kons ? 'selected' : '' }}>
                                    {{ $ks->kd_kons }} - {{ $ks->nm_kons }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Detail Finansial</div>
            <div class="row">
                {{-- Total Awal --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Total Proyek Awal</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold text-primary">Rp</span>
                        <input type="number" name="tot_awal" class="form-control fw-bold" 
                               placeholder="0" value="{{ old('tot_awal') }}">
                    </div>
                </div>

                {{-- Total Akhir --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Total Proyek Final <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold text-success">Rp</span>
                        <input type="number" name="tot_akhir" class="form-control fw-bold" 
                               placeholder="0" value="{{ old('tot_akhir') }}" required>
                    </div>
                </div>

                {{-- Budget Pengiriman --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Budget Pengiriman</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold text-warning">Rp</span>
                        <input type="number" name="bud_pengiriman" class="form-control fw-bold" 
                               placeholder="0" value="{{ old('bud_pengiriman') }}">
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-save flex-grow-1 shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Penjualan
                </button>
                <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 12px;">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection