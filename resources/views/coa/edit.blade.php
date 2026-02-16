@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

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
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        max-width: 800px;
    }

    .form-label {
        font-weight: 700;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 8px;
        display: block;
    }

    .input-group-custom {
        display: flex;
        width: 100%;
    }

    .input-group-text-custom {
        background-color: #f8fafc; /* Kembali ke Abu-abu muda bersih */
        border: 1px solid #e2e8f0;
        border-right: none;
        border-radius: 12px 0 0 12px !important;
        color: #94a3b8;
        min-width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 0 12px 12px 0 !important;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        color: #1e293b;
        width: 100%;
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #0d6efd; /* Fokus Biru */
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        outline: none;
    }

    /* Tombol Biru Navy (Konsisten dengan Create) */
    .btn-update {
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

    .btn-update:hover {
        background-color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(15, 23, 42, 0.1);
        color: white;
    }

    .section-subtitle {
        font-weight: 800;
        color: #0d6efd; /* Subtitle Biru */
        font-size: 0.95rem;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-edit me-2 text-primary"></i> Edit Data Akun (COA)</h4>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm mb-4 border-0" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <ul class="mb-0 small fw-bold">
                @foreach ($errors->all() as $err)
                    <li><i class="fas fa-times me-1"></i> {{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        {{-- Form Action ke Route Update --}}
        <form action="{{ route('coa.update', $coa->kd_akun) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="section-subtitle">Identitas Akun</div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Kode Akun</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-hashtag"></i></span>
                        {{-- Value diambil dari database ($coa->kd_akun) --}}
                        <input type="text" name="kd_akun" class="form-control-custom fw-bold" value="{{ old('kd_akun', $coa->kd_akun) }}" required>
                    </div>
                    <small class="text-muted d-block mt-1 ms-1" style="font-size: 0.75rem;">Pastikan kode tetap unik.</small>
                </div>

                <div class="col-md-8 mb-4">
                    <label class="form-label">Nama Akun</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-font"></i></span>
                        <input type="text" name="nama_akun" class="form-control-custom text-uppercase" value="{{ old('nama_akun', $coa->nama_akun) }}" required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Kategori & Klasifikasi</div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label">Jenis Akun</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-layer-group"></i></span>
                        <select name="jenis_akun" class="form-select-custom cursor-pointer" required>
                            <option value="" disabled>-- Pilih Kategori Akun --</option>
                            {{-- Logic Selected untuk Edit --}}
                            <option value="Aset" {{ old('jenis_akun', $coa->jenis_akun) == 'Aset' ? 'selected' : '' }}>Aset (Harta)</option>
                            <option value="Kewajiban" {{ old('jenis_akun', $coa->jenis_akun) == 'Kewajiban' ? 'selected' : '' }}>Kewajiban (Utang)</option>
                            <option value="Ekuitas" {{ old('jenis_akun', $coa->jenis_akun) == 'Ekuitas' ? 'selected' : '' }}>Ekuitas (Modal)</option>
                            <option value="Pendapatan" {{ old('jenis_akun', $coa->jenis_akun) == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                            <option value="Beban COGS" {{ old('jenis_akun', $coa->jenis_akun) == 'Beban COGS' ? 'selected' : '' }}>Beban COGS</option>
                            <option value="Beban Operasional" {{ old('jenis_akun', $coa->jenis_akun) == 'Beban Operasional' ? 'selected' : '' }}>Beban Operasional</option>
                            <option value="Pendapatan Lain-lain" {{ old('jenis_akun', $coa->jenis_akun) == 'Pendapatan Lain-lain' ? 'selected' : '' }}>Pendapatan Lain-lain</option>
                            <option value="Beban Lain-lain" {{ old('jenis_akun', $coa->jenis_akun) == 'Beban Lain-lain' ? 'selected' : '' }}>Beban Lain-lain</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-update flex-grow-1 shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
                <a href="{{ route('coa.index') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection