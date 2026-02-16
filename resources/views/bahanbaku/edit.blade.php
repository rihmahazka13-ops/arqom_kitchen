@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --text-dark: #000000;
        --text-grey: #475569;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* HEADER STYLE */
    .page-title {
        font-weight: 800;
        color: var(--text-dark) !important;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        margin-bottom: 25px;
        font-size: 1.4rem;
    }

    /* FORM CONTAINER - Dibuat lebih lega */
    .form-container {
        background: #ffffff;
        padding: 40px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        max-width: 900px;
    }

    /* LABEL - Hitam Pekat & Anti Numpuk */
    .form-label {
        font-weight: 700;
        color: var(--text-dark) !important;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        border: 1px solid #cbd5e1;
        border-right: none;
        border-radius: 12px 0 0 12px !important;
        color: #64748b;
        min-width: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-select-custom, .form-control-custom {
        border-radius: 0 12px 12px 0 !important;
        border: 1px solid #cbd5e1;
        padding: 14px 18px;
        font-size: 0.95rem;
        color: var(--text-dark) !important;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: var(--text-dark);
        box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
        outline: none;
    }

    /* Style khusus saat disabled / readonly */
    .form-control-custom:disabled, .form-select-custom:disabled, .form-control-custom:read-only {
        background-color: #f8fafc;
        color: #94a3b8 !important;
        cursor: not-allowed;
        border-color: #e2e8f0;
    }

    /* BUTTONS */
    .btn-update {
        background-color: #000000; 
        color: white !important;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 16px 25px;
        transition: 0.3s;
        letter-spacing: 1px;
    }

    .btn-update:hover {
        background-color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-locked {
        background-color: #e2e8f0;
        color: #94a3b8;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 16px 25px;
        cursor: not-allowed;
    }

    /* SECTION SUBTITLE - Hitam Pekat */
    .section-subtitle {
        font-weight: 800;
        color: var(--text-dark); 
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
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-edit me-2"></i> Perbarui Data Bahan Baku</h4>

    @php
        $isSystem = Str::startsWith($bahanbaku->kd_bhn, 'BB-');
    @endphp

    {{-- ERROR MESSAGES --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm mb-4 border-0" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <ul class="mb-0 small fw-bold list-unstyled">
                @foreach ($errors->all() as $err)
                    <li><i class="fas fa-times-circle me-2"></i> {{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- WARNING DATA SISTEM --}}
    @if($isSystem)
        <div class="alert shadow-sm mb-4 border-0 d-flex align-items-center p-3" style="border-radius: 12px; background-color: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b !important;">
            <i class="fas fa-lock me-3 fs-4"></i>
            <div>
                <strong class="d-block text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Mode Baca Saja (Read Only)</strong>
                <span class="small">Data ini otomatis. Untuk ubah harga/jumlah, edit di menu <a href="{{ route('pengeluaran.index') }}" class="fw-bold text-dark text-decoration-underline">Pengeluaran</a>.</span>
            </div>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('bahanbaku.update', $bahanbaku->kd_bhn) }}" method="POST">
            @csrf 
            @method('PUT')

            <div class="section-subtitle">Identitas & Relasi</div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Kode Bahan</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="kd_bhn" class="form-control-custom fw-bold" value="{{ $bahanbaku->kd_bhn }}" readonly>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label">Konsumen / Proyek</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-user-circle"></i></span>
                        <select name="nm_kons" class="form-select-custom" {{ $isSystem ? 'disabled' : 'required' }}>
                            <option value="" disabled>-- Pilih Konsumen --</option>
                            @foreach($konsumen as $k)
                                <option value="{{ $k->nm_kons }}" 
                                    {{ old('nm_kons', $bahanbaku->nm_kons) == $k->nm_kons ? 'selected' : '' }}>
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
                        <input type="text" name="nm_bhn" class="form-control-custom text-uppercase fw-bold" 
                               value="{{ old('nm_bhn', $bahanbaku->nm_bhn) }}" placeholder="Masukkan nama bahan"
                               {{ $isSystem ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Stok & Satuan</div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Jumlah (Stok)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-layer-group"></i></span>
                        <input type="number" name="jml_bhn" class="form-control-custom fw-bold" 
                               value="{{ old('jml_bhn', $bahanbaku->jml_bhn) }}" placeholder="0"
                               {{ $isSystem ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Satuan</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-tag"></i></span>
                        <input type="text" name="satuan_bhn" class="form-control-custom" 
                               value="{{ old('satuan_bhn', $bahanbaku->satuan_bhn) }}" placeholder="Kg, Gram, dll"
                               {{ $isSystem ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Informasi Harga</div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label">Harga Satuan (Beli)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom fw-bold text-dark">Rp</span>
                        <input type="number" name="harga_bhn" class="form-control-custom fw-bold" 
                               value="{{ old('harga_bhn', $bahanbaku->harga_bhn) }}" placeholder="0"
                               {{ $isSystem ? 'disabled' : '' }}>
                    </div>
                    <small class="text-muted mt-3 d-block"><i class="fas fa-info-circle me-1"></i> Total nilai saat ini: <strong class="text-dark">Rp {{ number_format($bahanbaku->tot_bhn, 0, ',', '.') }}</strong></small>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                @if(!$isSystem)
                    <button type="submit" class="btn btn-update flex-grow-1">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                @else
                    <button type="button" class="btn btn-locked flex-grow-1" disabled>
                        <i class="fas fa-lock me-2"></i> Terkunci oleh Sistem
                    </button>
                @endif
                
                <a href="{{ route('bahanbaku.index') }}" class="btn btn-outline-secondary px-4 fw-bold d-flex align-items-center" style="border-radius: 12px;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection