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

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-grey);
        font-weight: 800;
    }

    .info-value {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 1rem;
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

    .btn-action {
        border-radius: 12px;
        font-weight: 700;
        padding: 10px 20px;
        transition: 0.3s;
    }

    /* TOMBOL KUNING SUMBER */
    .btn-source {
        background-color: #fbbf24;
        color: #78350f;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        padding: 10px 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-source:hover {
        background-color: #f59e0b;
        color: #451a03;
    }

    .total-section {
        background-color: #f8fafc;
        border-radius: 12px;
        padding: 20px;
    }

    .fw-800 { font-weight: 800; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('jurnal.index') }}" class="text-decoration-none text-muted fw-bold">Entri Jurnal</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary">{{ $jurnal->kd_jurnal }}</li>
                </ol>
            </nav>
            <h4 class="page-title">Rincian Jurnal Umum</h4>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            {{-- TOMBOL SUMBER TRANSAKSI --}}
            @if($source && $sourceType == 'pemasukan')
                <a href="{{ route('pemasukan.index', ['search' => $source]) }}" class="btn btn-source">
                    <i class="fas fa-file-invoice-dollar me-2"></i> LIHAT PEMASUKAN
                </a>
            @elseif($source && $sourceType == 'pengeluaran')
                <a href="{{ route('pengeluaran.index', ['search' => $source]) }}" class="btn btn-source">
                    <i class="fas fa-file-signature me-2"></i> LIHAT PENGELUARAN
                </a>
            @endif

            <a href="{{ route('jurnal.index') }}" class="btn btn-action btn-outline-primary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>

            <a href="{{ route('jurnal.edit', $jurnal->kd_jurnal) }}" class="btn btn-action btn-outline-dark shadow-sm">
                <i class="fas fa-edit me-2"></i> Edit
            </a>

            <form action="{{ route('jurnal.destroy', $jurnal->kd_jurnal) }}" method="POST" onsubmit="return confirm('Hapus entri ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-action btn-outline-danger shadow-sm">
                    <i class="fas fa-trash me-2"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <div class="info-label mb-1">Referensi / Keterangan</div>
                    <div class="info-value">{{ $jurnal->ket_jurnal }}</div>
                </div>
                <div class="text-end">
                    <div class="info-label mb-1">Tanggal Akuntansi</div>
                    <div class="info-value fw-bold">
                        {{ \Carbon\Carbon::parse($jurnal->tgl_jurnal)->format('d F Y') }}
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40%">Akun</th>
                            <th>Label</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jurnal->details as $d)
                        <tr>
                            <td class="fw-bold">
                                <span class="text-primary me-2">{{ $d->kd_akun }}</span>
                                {{ $d->coa->nama_akun ?? 'N/A' }}
                            </td>
                            <td class="text-muted small fw-semibold">{{ $jurnal->ket_jurnal }}</td>
                            <td class="text-end fw-bold">
                                {{ $d->debit > 0 ? 'Rp ' . number_format($d->debit, 2, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ $d->kredit > 0 ? 'Rp ' . number_format($d->kredit, 2, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-md-5">
                    <div class="total-section">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-muted small text-uppercase">Total Debit</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($jurnal->details->sum('debit'), 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="fw-bold text-muted small text-uppercase">Total Kredit</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($jurnal->details->sum('kredit'), 2, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-800 text-dark">BALANCE</span>
                            <span class="fw-800 text-primary fs-5">Rp {{ number_format($jurnal->details->sum('debit'), 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection