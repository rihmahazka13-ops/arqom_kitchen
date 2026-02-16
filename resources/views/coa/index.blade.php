@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --accent-blue: #2563eb;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* 1. HEADER & ACTIONS */
    .page-title { font-weight: 800; color: var(--text-dark); text-transform: uppercase; font-size: 1.4rem; letter-spacing: -0.02em; }
    
    .btn-action {
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 700;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
    }

    .btn-tambah {
        background: var(--text-dark);
        color: white !important;
    }

    .btn-sync {
        background: #ffffff;
        color: var(--accent-blue) !important;
        border: 1px solid #dbeafe !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        filter: brightness(1.1);
    }

    /* 2. TABLE CONTAINER */
    .table-container { 
        background: #ffffff; 
        padding: 25px; 
        border-radius: 20px;
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    /* 3. TYPOGRAPHY & LINKS */
    .ledger-link {
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 700;
        transition: 0.2s;
        font-size: 0.9rem;
    }

    .ledger-link:hover {
        color: var(--accent-blue);
        text-decoration: underline;
    }

    .code-badge { 
        background: #f8fafc; 
        color: var(--text-dark); 
        padding: 5px 10px; 
        border-radius: 8px; 
        font-family: 'JetBrains Mono', monospace; 
        font-size: 0.8rem; 
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }

    /* 4. FINANCIAL BADGES */
    .badge-category {
        padding: 6px 14px !important;
        border-radius: 8px !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        display: inline-block !important;
        min-width: 110px;
        text-align: center;
    }

    .table thead th { 
        background-color: #f8fafc;
        color: var(--text-grey) !important;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .balance-text {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid py-4">
    {{-- TOP BAR --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Chart of Accounts</h4>
            <p class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">
                <i class="fas fa-shield-alt me-1"></i> Struktur Finansial • Arqom Kitchen
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('coa.sync') }}" class="btn-action btn-sync text-decoration-none">
                <i class="fas fa-sync-alt"></i> SYNC SHEETS
            </a>
            <a href="{{ route('coa.create') }}" class="btn-action btn-tambah text-decoration-none">
                <i class="fas fa-plus"></i> TAMBAH AKUN
            </a>
        </div>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="border-radius: 12px; background: #ecfdf5; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i> 
            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ session('success') }}</div>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="12%">Kode</th>
                        <th width="35%">Nama Akun</th>
                        <th class="text-center" width="20%">Kategori</th>
                        <th class="text-end" width="20%">Saldo Saat Ini</th>
                        <th class="text-center" width="13%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach ($coas as $c)
                    <tr>
                        <td><span class="code-badge">{{ $c->kd_akun }}</span></td>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="{{ route('coa.show', $c->kd_akun) }}" class="ledger-link">
                                    {{ $c->nama_akun }}
                                </a>
                                <span class="text-muted text-uppercase mt-1" style="font-size: 0.6rem; font-weight: 700;">
                                    <i class="fas fa-book-open me-1"></i> Lihat Buku Besar
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $jenis = strtoupper($c->jenis_akun);
                                
                                $badgeStyle = match(true) {
                                    str_contains($jenis, 'ASET') || str_contains($jenis, 'AKTIVA') 
                                        => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe;',
                                    
                                    str_contains($jenis, 'KEWAJIBAN') || str_contains($jenis, 'HUTANG') 
                                        => 'background: #fffbeb; color: #b45309; border: 1px solid #fef3c7;',
                                    
                                    str_contains($jenis, 'EKUITAS') || str_contains($jenis, 'MODAL') 
                                        => 'background: #f5f3ff; color: #6d28d9; border: 1px solid #ede9fe;',
                                    
                                    str_contains($jenis, 'PENDAPATAN') 
                                        => 'background: #ecfdf5; color: #047857; border: 1px solid #d1fae5;',
                                    
                                    str_contains($jenis, 'COGS') 
                                        => 'background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5;',
                                    
                                    str_contains($jenis, 'OPERASIONAL') || str_contains($jenis, 'BEBAN')
                                        => 'background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2;',
                                    
                                    default => 'background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;',
                                };

                                // Logika Saldo Normal
                                $isDebit = false;
                                $debitGroup = ['ASET', 'AKTIVA', 'BEBAN', 'COGS', 'OPERASIONAL'];
                                foreach($debitGroup as $dg) {
                                    if(str_contains($jenis, $dg)) { $isDebit = true; break; }
                                }
                                
                                $saldo = $isDebit 
                                    ? ($c->total_debit ?? 0) - ($c->total_kredit ?? 0)
                                    : ($c->total_kredit ?? 0) - ($c->total_debit ?? 0);
                                
                                $grandTotal += $saldo;
                            @endphp
                            <span class="badge-category" style="{{ $badgeStyle }}">
                                {{ $c->jenis_akun }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="balance-text {{ $saldo < 0 ? 'text-danger' : 'text-primary' }}">
                                {{ $saldo < 0 ? '-' : '' }}Rp {{ number_format(abs($saldo), 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('coa.edit', $c->kd_akun) }}" class="btn btn-sm btn-outline-warning border-0 p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('coa.destroy', $c->kd_akun) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-2" onclick="return confirm('Hapus akun ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if(!$coas->isEmpty())
                <tfoot>
                    <tr class="border-top" style="background: #f8fafc;">
                        <td colspan="3" class="text-end py-3 text-muted fw-bold" style="font-size: 0.7rem;">TOTAL SALDO BERSIH:</td>
                        <td class="text-end py-3 text-dark balance-text fs-6">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection