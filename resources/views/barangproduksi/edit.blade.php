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
        max-width: 1000px; 
    }

    .page-title { 
        font-weight: 800; 
        color: #0f172a !important; 
        text-transform: uppercase; 
        margin-bottom: 0; 
    }

    .form-label { 
        font-weight: 700; 
        color: #64748b !important; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        margin-bottom: 10px; 
        display: block;
    }

    /* INPUT GROUP CUSTOM */
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
        color: #0f172a !important; 
        font-weight: 500;
        background-color: #fff;
        width: 100%;
        transition: all 0.3s ease;
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

    .form-control-custom:read-only { 
        background-color: #f1f5f9; 
        color: #64748b !important; 
        cursor: not-allowed; 
        font-weight: 700; 
        border-color: #e2e8f0;
    }

    .section-subtitle { 
        font-weight: 800; 
        color: #0f172a !important; 
        font-size: 0.95rem; 
        margin-bottom: 25px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #f1f5f9; 
        display: flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .section-subtitle i { color: #3b82f6; margin-right: 12px; }

    .btn-update { 
        background-color: #0f172a; 
        color: white; 
        border-radius: 12px; 
        font-weight: 700; 
        padding: 16px 30px; 
        text-transform: uppercase; 
        border: none; 
        letter-spacing: 1px;
        transition: 0.3s;
    }

    .btn-update:hover { 
        background-color: #1e293b; 
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        color: white;
    }
</style>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title"><i class="fas fa-edit me-2 text-primary"></i> Perbarui Data Produksi</h4>
            <p class="text-muted fw-bold small">SESUAIKAN DATA & SISTEM AKAN MENGHITUNG ULANG HPP</p>
        </div>
        <a href="{{ route('barangproduksi.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 12px;">
            <i class="fas fa-arrow-left me-2"></i> KEMBALI
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0 small fw-bold">
                @foreach ($errors->all() as $err)
                    <li><i class="fas fa-times me-1"></i> {{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('barangproduksi.update', $barangproduksi->kd_bhnpro) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="section-subtitle"><i class="fas fa-fingerprint"></i> Identitas Barang Produksi</div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Kode Produksi</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-barcode"></i></span>
                        <input type="text" name="kd_bhnpro" class="form-control-custom" value="{{ $barangproduksi->kd_bhnpro }}" readonly>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label">Konsumen / Proyek</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-user-circle"></i></span>
                        <select name="nm_kons" class="form-select-custom" required>
                            <option value="" disabled>-- Pilih Konsumen --</option>
                            @foreach($konsumen as $k)
                                <option value="{{ $k->nm_kons }}" 
                                    {{ old('nm_kons', $barangproduksi->nm_kons) == $k->nm_kons ? 'selected' : '' }}>
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
                        <input type="text" name="bhnpro" class="form-control-custom text-uppercase" 
                               value="{{ old('bhnpro', $barangproduksi->bhnpro) }}" placeholder="Masukkan nama barang" required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2"><i class="fas fa-calculator"></i> Input Perubahan (Qty & Estimasi)</div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Jumlah Unit (Qty)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-layer-group"></i></span>
                        <input type="number" step="any" name="jml_pro" id="jml_pro" class="form-control-custom fw-bold" 
                               value="{{ old('jml_pro', $barangproduksi->jml_pro) }}" placeholder="0" required>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-primary">Estimasi HPP per Unit (Rp)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom text-primary fw-bold">Rp</span>
                        <input type="number" step="any" name="est_hpp" id="est_hpp" class="form-control-custom fw-bold" 
                               value="{{ old('est_hpp', $barangproduksi->est_hpp) }}" placeholder="0" required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2"><i class="fas fa-chart-line"></i> Kalkulasi Otomatis (Read Only)</div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Total Estimasi (Qty x Est)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom">Rp</span>
                        <input type="number" name="tot_est" id="tot_est" class="form-control-custom" value="{{ $barangproduksi->tot_est }}" readonly>
                    </div>
                </div>
                
                {{-- KOLOM REAL HPP DIBUAT READONLY KARENA DIHITUNG SYSTEM --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label text-success">Real HPP (Total)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom text-success fw-bold">Rp</span>
                        <input type="number" class="form-control-custom fw-bold text-muted" 
                               value="{{ number_format($barangproduksi->estrl_hpp, 0, '', '') }}" readonly style="background: #e2e8f0;">
                    </div>
                    <small class="text-danger fst-italic" style="font-size: 0.7rem;">*Akan berubah otomatis setelah disimpan</small>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label text-success">Real HPP (Per Unit)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom text-success fw-bold">Rp</span>
                        <input type="number" class="form-control-custom fw-bold text-muted" 
                               value="{{ number_format($barangproduksi->rl_hpp, 0, '', '') }}" readonly style="background: #e2e8f0;">
                    </div>
                </div>
            </div>

            <div class="d-flex mt-4">
                <button type="submit" class="btn btn-update flex-grow-1 shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan & Hitung Ulang HPP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jmlInput = document.getElementById('jml_pro');
        const estHppInput = document.getElementById('est_hpp');
        const totEstInput = document.getElementById('tot_est');

        // Fungsi hanya untuk update Total Estimasi saat ngetik
        // Real HPP tidak dihitung disini karena butuh data database (Backend)
        function hitung() {
            const jml = parseFloat(jmlInput.value) || 0;
            const est = parseFloat(estHppInput.value) || 0;
            
            const totalEst = jml * est;
            totEstInput.value = totalEst;
        }

        [jmlInput, estHppInput].forEach(el => el.addEventListener('input', hitung));
        hitung();
    });
</script>
@endsection