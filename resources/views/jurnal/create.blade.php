@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root { --text-dark: #0f172a; --text-grey: #64748b; --accent-blue: #3b82f6; --bg-light: #f8fafc; }
    body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
    .page-title { font-weight: 800; color: var(--text-dark); text-transform: uppercase; font-size: 1.5rem; letter-spacing: -0.5px; }
    .page-subtitle { font-size: 0.9rem; color: var(--text-grey); font-weight: 600; margin-top: 5px; }
    .table-container { background: #ffffff; padding: 30px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03); }
    .form-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-grey); font-weight: 700; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 12px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 0.95rem; font-weight: 600; background-color: #f8fafc; }
    
    /* Style untuk input readonly otomatis */
    .form-control:read-only {
        background-color: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
        border-style: dashed;
    }

    .table thead th { background-color: #f8fafc; color: var(--text-dark) !important; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #e2e8f0; }
    .btn-simpan { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white !important; border-radius: 12px; padding: 12px 30px; font-weight: 700; border: none; }
    .btn-tambah-baris { background-color: var(--text-dark); color: white !important; border-radius: 10px; padding: 8px 18px; font-weight: 700; border: none; }
    .alert-custom { border-radius: 12px; border-left: 4px solid #ef4444; background: #fef2f2; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="page-title">Input Jurnal Manual</h4>
            <p class="page-subtitle mb-0">Pencatatan Transaksi Baru • Arqom Kitchen</p>
        </div>
        <a href="{{ route('jurnal.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted border">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="table-container">
        @if(session('error'))
            <div class="alert alert-custom shadow-sm mb-4 d-flex align-items-center p-3">
                <i class="fas fa-exclamation-triangle me-3 text-danger fs-5"></i> 
                <div>
                    <div class="fw-bold text-dark">Kesalahan Input!</div>
                    <div class="small text-muted">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <form action="{{ route('jurnal.store') }}" method="POST">
            @csrf
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Jurnal</label>
                    <input type="date" name="tgl_jurnal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kode / No. Jurnal (Otomatis)</label>
                    {{-- UPDATE: value menggunakan $kd_otomatis dan readonly --}}
                    <input type="text" name="kd_jurnal" class="form-control fw-bold" value="{{ $kd_otomatis }}" readonly required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Keterangan / Referensi</label>
                    {{-- UPDATE: Autofocus dipindah ke sini --}}
                    <input type="text" name="ket_jurnal" class="form-control" placeholder="Contoh: Penjualan Produk" required autofocus>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table align-middle" id="tableJurnal">
                    <thead>
                        <tr>
                            <th width="45%">Akun Transaksi</th>
                            <th width="25%">Debit (Rp)</th>
                            <th width="25%">Kredit (Rp)</th>
                            <th width="50px"></th>
                        </tr>
                    </thead>
                    <tbody id="rowJurnal">
                        <tr>
                            <td>
                                <select name="details[0][kd_akun]" class="form-select" required>
                                    <option value="" disabled selected>Pilih Akun...</option>
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->kd_akun }}">{{ $coa->kd_akun }} - {{ $coa->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="details[0][debit]" class="form-control text-end" value="0" min="0"></td>
                            <td><input type="number" name="details[0][kredit]" class="form-control text-end" value="0" min="0"></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                <button type="button" class="btn btn-tambah-baris" id="addBtn">
                    <i class="fas fa-plus me-1"></i> Tambah Baris
                </button>
                <button type="submit" class="btn btn-simpan">
                    <i class="fas fa-save me-2"></i> Simpan Jurnal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let i = 1;
    document.getElementById('addBtn').addEventListener('click', function() {
        let row = `<tr>
            <td>
                <select name="details[${i}][kd_akun]" class="form-select" required>
                    <option value="" disabled selected>Pilih Akun...</option>
                    @foreach($coas as $coa)
                        <option value="{{ $coa->kd_akun }}">{{ $coa->kd_akun }} - {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="details[${i}][debit]" class="form-control text-end" value="0" min="0"></td>
            <td><input type="number" name="details[${i}][kredit]" class="form-control text-end" value="0" min="0"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;
        document.getElementById('rowJurnal').insertAdjacentHTML('beforeend', row);
        i++;
    });

    document.addEventListener('click', function(e) {
        if(e.target && (e.target.classList.contains('remove-row') || e.target.closest('.remove-row'))) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection