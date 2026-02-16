@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }

    .header-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
    }

    .table-container { 
        background: #ffffff; 
        padding: 0; 
        border-radius: 20px;
        border: 1px solid #e2e8f0; 
        overflow: hidden;
    }

    .table thead th { 
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

    .balance-badge {
        background: #eff6ff;
        color: #1e40af;
        padding: 15px 25px;
        border-radius: 15px;
        border: 1px solid #dbeafe;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .btn-back {
        background: white;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-back:hover { background: #f8fafc; color: #0f172a; }

    .running-balance { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0f172a; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('coa.index') }}" class="btn-back text-decoration-none mb-3 d-inline-block">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke COA
            </a>
            <h3 class="fw-bold text-dark mb-0">Buku Besar: {{ $account->nama_akun }}</h3>
            <p class="text-muted mb-0">{{ $account->kd_akun }} • {{ strtoupper($account->jenis_akun) }}</p>
        </div>
        <div class="balance-badge text-end">
            <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.65rem; opacity: 0.7;">Saldo Akhir Terfilter</small>
            @php
                $jenis = strtoupper($account->jenis_akun);
                $isDebitAccount = in_array($jenis, ['ASET', 'AKTIVA', 'BEBAN', 'BEBAN COGS', 'BEBAN OPERASIONAL']);
            @endphp
            <h4 class="mb-0 fw-bold" id="final-balance-display">Rp 0</h4>
        </div>
    </div>

    <div class="filter-card shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('coa.show', $account->kd_akun) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Cari Kode / Keterangan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="No. Bukti atau memo..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 10px;">
                            Filter
                        </button>
                        @if(request()->anyFilled(['start_date', 'end_date', 'q']))
                            <a href="{{ route('coa.show', $account->kd_akun) }}" class="btn btn-light btn-sm fw-bold text-muted">Reset</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="12%">Tanggal</th>
                        <th width="15%">No. Bukti</th>
                        <th width="33%">Keterangan</th>
                        <th width="12%" class="text-end">Debit</th>
                        <th width="12%" class="text-end">Kredit</th>
                        <th width="16%" class="text-end">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = 0; @endphp
                    @forelse($transactions as $t)
                        @php
                            if ($isDebitAccount) {
                                $runningBalance += ($t->debit - $t->kredit);
                            } else {
                                $runningBalance += ($t->kredit - $t->debit);
                            }
                        @endphp
                        <tr>
                            <td class="text-muted">{{ date('d M Y', strtotime($t->tanggal)) }}</td>
                            <td>
                                <a href="{{ route('jurnal.show', $t->no_bukti) }}" class="fw-bold text-primary text-decoration-none">
                                    <i class="fas fa-file-invoice me-1 small"></i> {{ $t->no_bukti }}
                                </a>
                            </td>
                            <td class="text-dark fw-medium">{{ $t->keterangan }}</td>
                            <td class="text-end text-success fw-bold">
                                {{ $t->debit > 0 ? number_format($t->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-danger fw-bold">
                                {{ $t->kredit > 0 ? number_format($t->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end">
                                <span class="running-balance">Rp {{ number_format($runningBalance, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open d-block mb-3 fa-3x" style="opacity: 0.2;"></i>
                                Tidak ada transaksi ditemukan untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Update Saldo Akhir di badge atas berdasarkan running balance terakhir
    document.addEventListener('DOMContentLoaded', function() {
        const finalBalance = "{{ number_format($runningBalance, 0, ',', '.') }}";
        document.getElementById('final-balance-display').innerText = 'Rp ' + finalBalance;
    });
</script>
@endsection