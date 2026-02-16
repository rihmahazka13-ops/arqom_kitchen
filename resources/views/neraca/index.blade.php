@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --accent-blue: #2563eb;
        --bg-body: #f1f5f9;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* 1. REPORT CONTAINER */
    .report-container { 
        background: #fff; 
        border-radius: 24px; 
        border: 1px solid #e2e8f0; 
        padding: 40px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    }

    .table-balance td { 
        padding: 14px 15px; 
        font-size: 0.9rem; 
        border-bottom: 1px solid #f8fafc; 
        color: var(--text-dark);
        font-weight: 500;
    }

    /* 2. HEADER & GROUP STYLING */
    .header-row { 
        background: #f8fafc; 
        font-weight: 800; 
        color: var(--text-grey); 
        text-transform: uppercase; 
        letter-spacing: 1.5px; 
        font-size: 0.7rem !important;
    }

    .group-label {
        font-weight: 800;
        color: #334155;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding-top: 25px !important;
        letter-spacing: 0.5px;
    }
    
    .total-group-row { 
        background: #f8fafc; 
        font-weight: 700; 
        border-top: 2px solid #e2e8f0 !important;
    }

    .indent { padding-left: 40px !important; color: var(--text-grey) !important; font-size: 0.85rem !important; }
    
    .money-font { font-family: 'JetBrains Mono', monospace; font-weight: 700; }

    /* 3. STATUS BADGES */
    .status-card {
        border-radius: 16px;
        padding: 20px;
        transition: 0.3s;
    }

    .status-balanced {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .status-unbalanced {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* 4. UTILITIES */
    .form-label-custom {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-grey);
        margin-bottom: 8px;
        display: block;
    }

    @media print {
        .no-print, .card, .btn { display: none !important; }
        .report-container { border: none; padding: 0; box-shadow: none; }
        body { background: white; }
    }
</style>

<div class="container-fluid py-4">
    {{-- TOP BAR --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -1px;">LAPORAN POSISI KEUANGAN</h4>
            <p class="text-muted fw-bold text-uppercase mb-0" style="font-size: 0.7rem; letter-spacing: 1px;">
                <i class="fas fa-balance-scale me-1"></i> Balance Sheet • Arqom Kitchen
            </p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm" style="font-size: 0.85rem;">
                <i class="fas fa-print me-2"></i> CETAK NERACA
            </button>
        </div>
    </div>

    {{-- FILTER TANGGAL --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-4">
            <form action="{{ route('neraca.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label-custom">Posisi Per Tanggal</label>
                    <input type="date" name="tgl_akhir" class="form-control border-0 bg-light fw-bold" value="{{ $tgl_akhir ?? date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-2" style="border-radius: 10px;">
                        TAMPILKAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="report-container shadow-sm">
        {{-- Formal Header --}}
        <div class="text-center mb-5">
            <h4 class="fw-bold mb-0">ARQOM KITCHEN</h4>
            <p class="text-muted text-uppercase fw-bold small mb-1" style="letter-spacing: 2px;">Statement of Financial Position</p>
            <div class="d-inline-block border-top pt-1 mt-1">
                <span class="fw-bold small">PER TANGGAL: {{ \Carbon\Carbon::parse($tgl_akhir)->format('d F Y') }}</span>
            </div>
        </div>

        <table class="table table-borderless table-balance mb-0">
            {{-- ================= ASET (AKTIVA) ================= --}}
            <tr class="header-row">
                <td><i class="fas fa-archive me-2 text-primary"></i> ASET (AKTIVA)</td>
                <td class="text-end">Nilai (IDR)</td>
            </tr>
            
            <tr><td class="group-label">Aset Lancar</td><td></td></tr>
            @php $totalAsetLancar = 0; @endphp
            @foreach($aset_lancar as $al)
                <tr>
                    <td class="indent">{{ $al['nama_akun'] }}</td>
                    <td class="text-end money-font">Rp {{ number_format($al['total'], 0, ',', '.') }}</td>
                </tr>
                @php $totalAsetLancar += $al['total']; @endphp
            @endforeach

            <tr><td class="group-label">Aset Tetap</td><td></td></tr>
            @php $totalAsetTetap = 0; @endphp
            @foreach($aset_tetap as $at)
                <tr>
                    <td class="indent">{{ $at['nama_akun'] }}</td>
                    <td class="text-end money-font">Rp {{ number_format($at['total'], 0, ',', '.') }}</td>
                </tr>
                @php $totalAsetTetap += $at['total']; @endphp
            @endforeach

            <tr class="total-group-row text-primary">
                <td class="ps-3 fw-bold">TOTAL ASET</td>
                <td class="text-end money-font fs-6">Rp {{ number_format($totalAsetLancar + $totalAsetTetap, 0, ',', '.') }}</td>
            </tr>

            {{-- ================= PASSIVA ================= --}}
            <tr class="header-row">
                <td style="padding-top: 50px !important;"><i class="fas fa-landmark me-2 text-success"></i> LIABILITAS & EKUITAS (PASSIVA)</td>
                <td class="text-end" style="padding-top: 50px !important;"></td>
            </tr>

            <tr><td class="group-label">Kewajiban / Hutang</td><td></td></tr>
            @php $totalLiabilitas = 0; @endphp
            @forelse($liabilitas as $l)
                <tr>
                    <td class="indent">{{ $l['nama_akun'] }}</td>
                    <td class="text-end money-font">Rp {{ number_format($l['total'], 0, ',', '.') }}</td>
                </tr>
                @php $totalLiabilitas += $l['total']; @endphp
            @empty
                <tr><td class="indent text-muted small italic">Tidak ada saldo kewajiban</td><td class="text-end money-font">Rp 0</td></tr>
            @endforelse

            <tr><td class="group-label">Modal / Ekuitas</td><td></td></tr>
            @php $totalEkuitas = 0; @endphp
            @foreach($ekuitas as $e)
                <tr>
                    <td class="indent">{{ $e['nama_akun'] }}</td>
                    <td class="text-end money-font">Rp {{ number_format($e['total'], 0, ',', '.') }}</td>
                </tr>
                @php $totalEkuitas += $e['total']; @endphp
            @endforeach
            
            <tr>
                <td class="indent" style="font-style: italic;">Laba Tahun Berjalan (Net Profit)</td>
                <td class="text-end money-font">Rp {{ number_format($laba_berjalan, 0, ',', '.') }}</td>
            </tr>
            @php $totalEkuitas += $laba_berjalan; @endphp

            <tr class="total-group-row text-success">
                <td class="ps-3 fw-bold">TOTAL LIABILITAS & EKUITAS</td>
                <td class="text-end money-font fs-6">Rp {{ number_format($totalLiabilitas + $totalEkuitas, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- STATUS BALANCE INDICATOR --}}
        @php 
            $totalAset = $totalAsetLancar + $totalAsetTetap;
            $totalPassiva = $totalLiabilitas + $totalEkuitas;
            $selisih = round($totalAset) - round($totalPassiva);
        @endphp

        <div class="mt-5 status-card {{ $selisih == 0 ? 'status-balanced' : 'status-unbalanced' }}">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas {{ $selisih == 0 ? 'fa-check-circle' : 'fa-exclamation-triangle' }} fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">STATUS KESEIMBANGAN</h6>
                        <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">
                            {{ $selisih == 0 ? 'Neraca Seimbang (Balanced)' : 'Neraca Tidak Seimbang (Unbalanced)' }}
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <span class="small d-block fw-bold opacity-75">SELISIH:</span>
                    <span class="money-font fs-5">Rp {{ number_format(abs($selisih), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- Signatures for Print --}}
        <div class="mt-5 d-none d-print-block">
            <div class="row text-center">
                <div class="col-4">
                    <p class="small mb-5">Dibuat Oleh,</p>
                    <br><br>
                    <p class="border-top d-inline-block px-4 small fw-bold">Accounting</p>
                </div>
                <div class="col-4 offset-4">
                    <p class="small mb-5">Disetujui Oleh,</p>
                    <br><br>
                    <p class="border-top d-inline-block px-4 small fw-bold">Direktur Utama</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection