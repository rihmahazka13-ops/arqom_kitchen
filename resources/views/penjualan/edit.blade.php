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
        padding: 35px; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.02); 
        max-width: 1000px; 
    }

    .form-label { 
        font-weight: 700; 
        color: #475569 !important; 
        font-size: 0.85rem; 
        text-transform: uppercase; 
        letter-spacing: 0.5px;
        margin-bottom: 10px; 
    }

    .form-control-custom, .form-select-custom { 
        border-radius: 12px; 
        border: 1px solid #cbd5e1; 
        padding: 12px 18px; 
        color: #0f172a !important; 
        font-weight: 500;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #3b82f6;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Styling khusus input readonly agar terlihat 'terkunci' tapi rapi */
    .form-control-custom:read-only { 
        background-color: #f1f5f9; 
        color: #64748b !important; 
        cursor: not-allowed; 
        font-weight: 700; 
        border-color: #e2e8f0;
    }

    .section-subtitle { 
        font-weight: 800; 
        color: #3b82f6 !important; 
        font-size: 0.95rem; 
        margin-bottom: 25px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #f1f5f9; 
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Merapikan Icon Box di Input Group */
    .input-group-text-custom { 
        background-color: #f1f5f9; 
        color: #3b82f6 !important; /* Warna icon biru */
        font-weight: 700; 
        min-width: 48px; 
        display: flex;
        align-items: center;
        justify-content: center; 
        border: 1px solid #cbd5e1;
        border-radius: 12px 0 0 12px !important; 
    }

    .form-control-custom {
        border-radius: 0 12px 12px 0 !important;
    }

    /* Styling Tombol Simpan */
    .btn-update { 
        background-color: #0f172a; 
        color: white; 
        border-radius: 12px; 
        font-weight: 700; 
        padding: 15px 25px; 
        text-transform: uppercase; 
        border: none; 
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-update:hover { 
        background-color: #1e293b; 
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        color: white;
    }

    .btn-cancel {
        border-radius: 12px;
        padding: 15px 25px;
        font-weight: 700;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-edit me-2 text-primary"></i> Perbarui Transaksi Penjualan</h4>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <div class="fw-bold small mb-2 text-uppercase"><i class="fas fa-exclamation-triangle me-2"></i> Periksa kembali inputan Anda:</div>
            <ul class="mb-0 small fw-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form method="POST" action="{{ route('penjualan.update', $penjualan->kd_penj) }}">
            @csrf
            @method('PUT')

            <div class="section-subtitle">Data Utama Penjualan</div>
            <div class="row">
                {{-- Kode Penjualan --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label text-muted">Kode Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-fingerprint"></i></span>
                        <input type="text" name="kd_penj" class="form-control form-control-custom" value="{{ $penjualan->kd_penj }}" readonly>
                    </div>
                </div>

                {{-- Tanggal Penjualan --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Tanggal Transaksi</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-calendar-alt"></i></span>
                        <input type="date" name="tgl_penj" class="form-control form-control-custom" value="{{ old('tgl_penj', $penjualan->tgl_penj) }}" required>
                    </div>
                </div>

                {{-- Invoice --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Nomor Invoice</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-receipt"></i></span>
                        <input type="text" name="invoice" class="form-control form-control-custom" value="{{ old('invoice', $penjualan->invoice) }}" placeholder="Contoh: INV-001" required>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Nama Konsumen --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Konsumen / Proyek</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-address-card"></i></span>
                        <select name="nm_kons" class="form-select form-select-custom" style="border-radius: 0 12px 12px 0 !important;" required>
                            <option value="" disabled>-- Pilih Konsumen --</option>
                            @foreach($konsumen as $item)
                                <option value="{{ $item->nm_kons }}" 
                                    {{ old('nm_kons', $penjualan->nm_kons) == $item->nm_kons ? 'selected' : '' }}>
                                    {{ $item->kd_kons }} - {{ $item->nm_kons }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-3">Rincian Finansial</div>
            <div class="row">
                {{-- Total Awal --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Total Proyek Awal</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-coins"></i></span>
                        <input type="number" name="tot_awal" class="form-control form-control-custom fw-bold" 
                               value="{{ old('tot_awal', $penjualan->tot_awal) }}" placeholder="0">
                    </div>
                </div>

                {{-- Total Akhir --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label text-primary">Total Proyek Final</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-money-bill-wave"></i></span>
                        <input type="number" name="tot_akhir" class="form-control form-control-custom fw-bold text-primary" 
                               value="{{ old('tot_akhir', $penjualan->tot_akhir) }}" required placeholder="0">
                    </div>
                </div>

                {{-- Budget Pengiriman --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label">Budget Pengiriman</label>
                    <div class="input-group">
                        <span class="input-group-text-custom"><i class="fas fa-shipping-fast"></i></span>
                        <input type="number" name="bud_pengiriman" class="form-control form-control-custom fw-bold" 
                               value="{{ old('bud_pengiriman', $penjualan->bud_pengiriman) }}" placeholder="0">
                    </div>
                </div>
            </div>

            <hr class="my-4" style="border-top: 2px dashed #e2e8f0;">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-update flex-grow-1">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
                <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary btn-cancel px-4">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection