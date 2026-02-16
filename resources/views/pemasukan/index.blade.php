@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --primary-blue: #2563eb;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* 1. STYLING UTAMA */
    .page-title { font-weight: 800; color: var(--text-dark); text-transform: uppercase; letter-spacing: -0.02em; font-size: 1.4rem; }
    
    .table-container { 
        background: #ffffff; padding: 25px; border-radius: 20px; 
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
    }

    /* 2. TABLE ALIGNMENT */
    .table thead th { 
        background-color: #f8fafc;
        padding: 15px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-grey) !important;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }

    .table tbody td { 
        white-space: nowrap !important;
        vertical-align: middle;
        padding: 16px 15px;
        color: var(--text-dark) !important;
        font-weight: 500;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    /* 3. BADGE & KODE */
    .code-badge { 
        background: #eff6ff; color: var(--primary-blue); padding: 5px 12px; 
        border-radius: 8px; font-family: monospace; font-size: 0.8rem; 
        font-weight: 800; border: 1px solid #dbeafe; display: inline-block;
    }

    /* 4. MONEY FORMAT (ACCOUNTING) */
    .money-cell {
        display: flex; justify-content: space-between; align-items: center;
        min-width: 140px; font-family: 'monospace'; font-weight: 700;
    }
    .money-cell span:first-child { font-size: 0.7rem; opacity: 0.5; font-weight: 600; }

    /* 5. EVIDENCE PREVIEW */
    .img-evidence { 
        width: 45px; height: 45px; object-fit: cover; border-radius: 10px; 
        border: 2px solid #f1f5f9; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .img-evidence:hover { transform: scale(1.8); z-index: 50; border-color: var(--primary-blue); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

    /* Filter Area */
    .filter-card { background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.85rem; padding: 8px 12px; }
</style>

<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Manajemen Pemasukan</h4>
            <p class="text-muted small mb-0 text-uppercase fw-bold">Pencatatan Transaksi Masuk • Arqom Kitchen</p>
        </div>
        <a href="{{ route('pemasukan.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold border-0" style="border-radius: 10px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <i class="fas fa-plus me-2"></i> TAMBAH PEMASUKAN
        </a>
    </div>

    <div class="table-container">
        {{-- FILTER --}}
        <div class="filter-card shadow-none">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-custom" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Hingga</label>
                    <input type="date" name="end_date" class="form-control form-control-custom" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Cari</label>
                    <input type="text" name="search" class="form-control form-control-custom" placeholder="Cari nama konsumen atau kode..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-bold" style="border-radius:10px; padding: 9px;">FILTER</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">KODE</th>
                        <th>TANGGAL</th>
                        <th>KONSUMEN / PROYEK</th>
                        <th class="text-end">NOMINAL MASUK</th>
                        <th>KETERANGAN</th>
                        <th class="text-center">BUKTI TF</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPemasukan = 0; @endphp
                    @forelse($data as $k)
                    @php $totalPemasukan += $k->jml_pem; @endphp
                    <tr>
                        <td class="text-center">
                            <span class="code-badge">{{ $k->kd_pem }}</span>
                        </td>
                        <td class="fw-bold">{{ \Carbon\Carbon::parse($k->tgl_pem)->format('d/m/Y') }}</td>
                        <td class="text-uppercase fw-bold text-primary" style="font-size: 0.8rem;">
                            <i class="fas fa-user-circle me-1 opacity-50"></i> {{ $k->nm_kons }}
                        </td>
                        <td class="text-end">
                            <div class="money-cell text-dark">
                                <span>Rp</span>
                                <span>{{ number_format($k->jml_pem, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; font-size: 0.75rem;">
                                {{ $k->ket_pem ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($k->buktitf_pem)
                                <a href="{{ asset('gambar/' . $k->buktitf_pem) }}" target="_blank">
                                    <img src="{{ asset('gambar/' . $k->buktitf_pem) }}" class="img-evidence shadow-sm">
                                </a>
                            @else
                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.65rem;">No Image</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group gap-1">
                                <a href="{{ route('pemasukan.edit', $k->kd_pem) }}" class="btn btn-sm btn-outline-warning border-0"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('pemasukan.destroy', $k->kd_pem) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus data pemasukan ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 fw-bold text-muted opacity-50">Belum ada catatan pemasukan yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot>
                    <tr style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                        <td colspan="3" class="text-end py-3 fw-bold text-uppercase" style="font-size: 0.7rem;">Total Pemasukan Tampil :</td>
                        <td class="text-end">
                            <div class="money-cell text-success fw-bold fs-6">
                                <span>Rp</span>
                                <span>{{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td colspan="3" class="bg-white"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection