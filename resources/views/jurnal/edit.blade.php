@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --accent-blue: #3b82f6;
        --bg-light: #f8fafc;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .page-title { 
        font-weight: 800; 
        color: var(--text-dark); 
        text-transform: uppercase; 
        font-size: 1.5rem;
        letter-spacing: -0.5px;
    }

    .card-custom {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03);
    }

    .form-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-grey);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 12px 15px;
        font-weight: 600;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .table thead th {
        background-color: #f8fafc;
        color: var(--text-dark);
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 15px;
        border-bottom: 2px solid #e2e8f0;
    }

    .btn-update {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white !important;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 700;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
    }
</style>

<div class="container-fluid py-4">
    {{-- Notifikasi Error --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="border-radius: 12px; background: #fef2f2; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-triangle me-3 text-danger fs-5"></i> 
            <div>
                <div class="fw-bold text-dark">Gagal Memperbarui Jurnal!</div>
                <div class="small text-muted">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Edit Entri Jurnal</h4>
            <p class="text-muted fw-semibold mb-0">Sesuaikan rincian transaksi akuntansi</p>
        </div>
        <span class="badge bg-white text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold shadow-sm">
            {{ $jurnal->kd_jurnal }}
        </span>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <form action="{{ route('jurnal.update', $jurnal->kd_jurnal) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Jurnal</label>
                        <input type="date" name="tgl_jurnal" class="form-control" value="{{ $jurnal->tgl_jurnal }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode / Nomor Jurnal</label>
                        <input type="text" name="kd_jurnal" class="form-control" value="{{ $jurnal->kd_jurnal }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Referensi / Keterangan</label>
                        <input type="text" name="ket_jurnal" class="form-control" value="{{ $jurnal->ket_jurnal }}" required>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table align-middle" id="tableJurnal">
                        <thead>
                            <tr>
                                <th width="45%">Akun Transaksi</th>
                                <th width="25%" class="text-end">Debit (Rp)</th>
                                <th width="25%" class="text-end">Kredit (Rp)</th>
                                <th width="50px"></th>
                            </tr>
                        </thead>
                        <tbody id="rowJurnal">
                            @foreach($jurnal->details as $index => $detail)
                            <tr>
                                <td>
                                    <select name="details[{{ $index }}][kd_akun]" class="form-select" required>
                                        @foreach($coas as $coa)
                                            <option value="{{ $coa->kd_akun }}" {{ $detail->kd_akun == $coa->kd_akun ? 'selected' : '' }}>
                                                {{ $coa->kd_akun }} - {{ $coa->nama_akun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="details[{{ $index }}][debit]" class="form-control text-end" value="{{ (int)$detail->debit }}" step="any" required></td>
                                <td><input type="number" name="details[{{ $index }}][kredit]" class="form-control text-end" value="{{ (int)$detail->kredit }}" step="any" required></td>
                                <td class="text-center">
                                    @if($loop->iteration > 2)
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="fas fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                    <button type="button" class="btn btn-dark btn-sm rounded-pill px-3" id="addBtn">
                        <i class="fas fa-plus me-1"></i> Tambah Baris
                    </button>
                    <div>
                        <a href="{{ route('jurnal.index') }}" class="btn btn-light px-4 rounded-pill me-2 fw-bold text-muted">Batal</a>
                        <button type="submit" class="btn btn-update shadow-sm">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let i = {{ $jurnal->details->count() }};
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
            <td><input type="number" name="details[${i}][debit]" class="form-control text-end" value="0" required></td>
            <td><input type="number" name="details[${i}][kredit]" class="form-control text-end" value="0" required></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="fas fa-trash"></i></button></td>
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