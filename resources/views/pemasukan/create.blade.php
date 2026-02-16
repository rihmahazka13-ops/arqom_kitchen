@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    /* Styling Formal & Tajam */
    .page-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        color: #0f172a;
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
        max-width: 900px;
    }

    .form-label {
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        color: #1e293b;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .input-group-text {
        background-color: #f1f5f9;
        border-radius: 12px 0 0 12px !important;
        border: 1px solid #e2e8f0;
        color: #64748b;
        min-width: 45px;
        justify-content: center;
    }

    .btn-save {
        background-color: #0f172a; 
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
        color: #3b82f6;
        font-size: 0.9rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Input Pemasukan Baru</h4>

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
        <form method="POST" action="{{ route('pemasukan.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="section-subtitle">Informasi Transaksi</div>
            <div class="row">
                {{-- Kode Pemasukan --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kode Pemasukan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="kd_pem" class="form-control" 
                               value="{{ $kd_otomatis ?? old('kd_pem') }}" readonly>
                    </div>
                </div>

                {{-- Tanggal Pemasukan --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_pem" class="form-control" 
                               value="{{ old('tgl_pem', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Pilih Transaksi Penjualan (PENGGANTI DROPDOWN NAMA) --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Pilih Transaksi Penjualan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-shopping-cart"></i></span>
                        <select name="kd_penj" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kode - Konsumen --</option>
                            @foreach($penjualan as $p)
                                <option value="{{ $p->kd_penj }}" {{ old('kd_penj') == $p->kd_penj ? 'selected' : '' }}>
                                    {{ $p->kd_penj }} - {{ $p->nm_kons }} ({{ $p->invoice ?? 'No Invoice' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted" style="font-size: 0.7rem;">*Pembayaran akan otomatis masuk ke proyek yang dipilih.</small>
                </div>

                {{-- Jumlah Pemasukan --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label text-primary">Jumlah Pemasukan (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold text-primary">Rp</span>
                        <input type="number" name="jml_pem" class="form-control fw-bold text-end" 
                               placeholder="0" value="{{ old('jml_pem') }}" required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Lampiran & Catatan</div>
            <div class="row">
                {{-- Keterangan --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Keterangan Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                        <input type="text" name="ket_pem" class="form-control" 
                               placeholder="Contoh: Pembayaran Termin 1" value="{{ old('ket_pem') }}">
                    </div>
                </div>

                {{-- Bukti Transfer --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Unggah Bukti Transfer (Gambar)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-upload"></i></span>
                        <input type="file" name="buktitf_pem" class="form-control">
                    </div>
                    <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> Format: JPG, PNG, JPEG. Maks: 2MB.</small>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-save flex-grow-1 shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Pemasukan
                </button>
                <a href="{{ route('pemasukan.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 12px;">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection