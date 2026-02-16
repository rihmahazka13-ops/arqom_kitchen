@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --accent-blue: #2563eb;
        --bg-light: #f8fafc;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* HEADER & BRANDING */
    .page-title { 
        font-weight: 800; 
        color: var(--text-dark); 
        text-transform: uppercase; 
        font-size: 1.4rem;
        letter-spacing: -0.02em;
    }
    
    .page-subtitle {
        font-size: 0.75rem;
        color: var(--text-grey);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-action {
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
    }

    .btn-tambah {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-tambah:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }

    /* FILTER SECTION */
    .filter-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }

    .form-label-custom {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-grey);
        margin-bottom: 8px;
        display: block;
    }

    .form-control-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 9px 15px;
        font-size: 0.85rem;
        background-color: #f8fafc;
        font-weight: 600;
    }

    /* TABLE CORE */
    .table-container { 
        background: #ffffff; 
        border-radius: 20px;
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
        overflow: hidden;
    }

    .table thead th { 
        background-color: #f8fafc;
        color: var(--text-grey) !important;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 18px 20px;
        border-bottom: 2px solid #f1f5f9;
    }

    .table tbody td { 
        padding: 16px 20px;
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }

    .code-badge { 
        background: #eff6ff; 
        color: var(--accent-blue); 
        padding: 5px 12px; 
        border-radius: 8px; 
        font-family: 'JetBrains Mono', monospace; 
        font-size: 0.75rem; 
        font-weight: 700;
        border: 1px solid #dbeafe;
    }

    .money-text {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .status-badge {
        font-size: 0.6rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 6px 12px;
        border-radius: 50px;
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
</style>

<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Entri Jurnal Umum</h4>
            <p class="page-subtitle mb-0">
                <i class="fas fa-layer-group me-1"></i> Arsitektur Transaksi • Arqom Kitchen
            </p>
        </div>
        <a href="{{ route('jurnal.create') }}" class="btn-action btn-tambah text-decoration-none">
            <i class="fas fa-plus-circle"></i> BUAT JURNAL BARU
        </a>
    </div>

    {{-- FILTER SECTION --}}
    <div class="filter-card border-0 shadow-sm">
        <form action="{{ route('jurnal.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label-custom">Cari Transaksi</label>
                <input type="text" name="search" class="form-control form-control-custom" 
                       placeholder="No. Jurnal atau Keterangan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Periode Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-custom" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Periode Selesai</label>
                <input type="date" name="end_date" class="form-control form-control-custom" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-dark w-100 fw-bold py-2 shadow-sm" style="border-radius: 10px; font-size: 0.85rem;" type="submit">
                    <i class="fas fa-search me-2"></i> FILTER
                </button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="border-radius: 12px; background: #ecfdf5; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i> 
            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ session('success') }}</div>
        </div>
    @endif

    {{-- DATA TABLE --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">Tanggal</th>
                        <th>Nomor Jurnal</th>
                        <th>Deskripsi Transaksi</th>
                        <th class="text-end">Total Nominal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse ($jurnals as $j)
                        @php 
                            $currentTotal = $j->details->sum('debit'); 
                            $grandTotal += $currentTotal;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold" style="font-size: 0.8rem;">
                                {{ \Carbon\Carbon::parse($j->tgl_jurnal)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="code-badge">{{ $j->kd_jurnal }}</span>
                            </td>
                            <td class="text-dark">
                                <div class="fw-bold" style="font-size: 0.9rem;">{{ $j->ket_jurnal }}</div>
                                <span class="text-muted" style="font-size: 0.7rem;">Ref: Jurnal-System</span>
                            </td>
                            <td class="text-end money-text text-primary">
                                Rp {{ number_format($currentTotal, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="status-badge">
                                    <i class="fas fa-check-double me-1"></i> POSTED
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('jurnal.show', $j->kd_jurnal) }}" class="btn btn-sm btn-outline-primary border-0 p-2" title="Detail Jurnal">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <form action="{{ route('jurnal.destroy', $j->kd_jurnal) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-2" onclick="return confirm('Hapus entri jurnal ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-25 mb-3">
                                    <i class="fas fa-receipt fa-4x"></i>
                                </div>
                                <h6 class="fw-bold text-muted">Belum ada data jurnal yang terekam.</h6>
                                <p class="text-muted small">Silahkan klik tombol "Buat Jurnal Baru" untuk memulai pencatatan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(!$jurnals->isEmpty())
                <tfoot style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                    <tr>
                        <td colspan="3" class="text-end py-3 text-muted fw-bold" style="font-size: 0.7rem;">TOTAL AKUMULASI (DEBIT):</td>
                        <td class="text-end py-3 text-primary money-text fs-6">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                        <td colspan="2" class="bg-white border-start"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection