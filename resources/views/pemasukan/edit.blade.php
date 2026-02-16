@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .page-title { 
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800; 
        color: #0f172a !important; 
        text-transform: uppercase; 
        margin-bottom: 25px; 
    }

    .form-container { 
        background: #ffffff; 
        padding: 40px; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 15px 30px rgba(0,0,0,0.03); 
        max-width: 1000px; 
    }

    .form-label { 
        font-weight: 700; 
        color: #64748b !important; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        letter-spacing: 0.8px;
        margin-bottom: 8px; 
        display: block;
    }

    .input-group {
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        overflow: hidden; 
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .input-group:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .input-group-text-custom { 
        background-color: #f8fafc; 
        color: #94a3b8 !important; 
        font-weight: 700; 
        width: 50px; 
        border: none; 
        border-right: 1px solid #e2e8f0; 
        display: flex;
        align-items: center; 
        justify-content: center; 
    }

    .form-control-custom, .form-select-custom { 
        border: none; 
        padding: 14px 18px; 
        color: #0f172a !important; 
        font-weight: 600;
        background-color: #fff;
        width: 1%; 
        flex: 1 1 auto;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        box-shadow: none;
        outline: none;
    }

    .section-subtitle { 
        font-weight: 800; 
        color: #3b82f6 !important; 
        font-size: 0.9rem; 
        margin-bottom: 25px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #f1f5f9; 
        text-transform: uppercase;
    }

    .btn-update { 
        background-color: #0f172a; 
        color: white; 
        border-radius: 12px; 
        font-weight: 700; 
        padding: 16px 25px; 
        text-transform: uppercase; 
        border: none; 
        transition: 0.3s;
    }

    .btn-update:hover { 
        background-color: #1e293b; 
        transform: translateY(-2px);
    }

    .current-image-preview {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 15px;
        background: #f8fafc;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-edit me-2 text-primary"></i> Perbarui Transaksi Pemasukan</h4>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fef2f2; border-left: 5px solid #ef4444 !important;">
            <div class="fw-bold small mb-2 text-uppercase text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Periksa Inputan:</div>
            <ul class="mb-0 small fw-bold text-danger ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form method="POST" action="{{ route('pemasukan.update', $k->kd_pem) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="section-subtitle">Informasi Transaksi</div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Kode Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="kd_pem" class="form-control-custom" value="{{ $k->kd_pem }}" readonly>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Tanggal Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-calendar-day"></i></span>
                        <input type="date" name="tgl_pem" class="form-control-custom" value="{{ old('tgl_pem', $k->tgl_pem) }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- PERBAIKAN: Menggunakan kd_penj agar sinkron dengan Controller --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label">Pilih Transaksi Penjualan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-shopping-cart"></i></span>
                        <select name="kd_penj" class="form-select-custom form-select" required style="border:none;">
                            <option value="" disabled>-- Pilih Transaksi --</option>
                            @foreach($penjualan as $item)
                                <option value="{{ $item->kd_penj }}" 
                                    {{ (old('kd_penj', $k->kd_penj) == $item->kd_penj) ? 'selected' : '' }}>
                                    {{ $item->kd_penj }} - {{ $item->nm_kons }} ({{ $item->invoice ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label text-primary">Jumlah (Nominal)</label>
                    <div class="input-group">
                        <span class="input-group-text-custom text-primary fw-bold">Rp</span>
                        <input type="number" name="jml_pem" class="form-control-custom fw-bold text-dark" 
                               value="{{ old('jml_pem', $k->jml_pem) }}" required placeholder="0">
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-3">Dokumentasi & Catatan</div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label">Keterangan Tambahan</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-align-left"></i></span>
                        <input type="text" name="ket_pem" class="form-control-custom" 
                               value="{{ old('ket_pem', $k->ket_pem) }}" placeholder="Contoh: Pelunasan tahap akhir">
                    </div>
                </div>
            </div>

            <div class="row align-items-end">
                @if ($k->buktitf_pem)
                    <div class="col-md-4 mb-4">
                        <label class="form-label text-muted">Bukti Saat Ini</label>
                        <div class="current-image-preview text-center">
                            <img src="{{ asset('gambar/' . $k->buktitf_pem) }}" alt="bukti transfer" class="rounded shadow-sm" style="max-width:100%; height:140px; object-fit: contain;">
                        </div>
                    </div>
                @endif

                <div class="{{ $k->buktitf_pem ? 'col-md-8' : 'col-12' }} mb-4">
                    <label class="form-label">
                        <i class="fas fa-cloud-upload-alt me-1"></i> 
                        {{ $k->buktitf_pem ? 'Ganti Bukti Transfer' : 'Unggah Bukti Transfer' }}
                    </label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-image"></i></span>
                        <input type="file" name="buktitf_pem" class="form-control-custom pt-3">
                    </div>
                    <small class="text-muted mt-2 d-block fw-bold" style="font-size: 0.75rem;">
                        <i class="fas fa-info-circle me-1"></i> Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG (Maks 2MB).
                    </small>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-update flex-grow-1 shadow-sm">
                    <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                </button>
                <a href="{{ route('pemasukan.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 12px; display: flex; align-items: center;">
                    BATAL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection