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

    /* 1. REPORT CONTAINER */
    .report-container { 
        background: #fff; 
        border-radius: 24px; 
        border: 1px solid #e2e8f0; 
        padding: 40px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    }

    .table-lr td { 
        padding: 14px 15px; 
        font-size: 0.9rem; 
        border-bottom: 1px solid #f8fafc; 
        color: var(--text-dark);
        font-weight: 500;
    }

    /* 2. ROW STYLING */
    .header-row { 
        background: #f8fafc; 
        font-weight: 800; 
        color: var(--text-grey); 
        text-transform: uppercase; 
        letter-spacing: 1px; 
        font-size: 0.7rem !important;
    }
    
    .total-row { 
        background: #ffffff; 
        font-weight: 700; 
        border-top: 1px solid #e2e8f0;
    }

    .subtotal-row { 
        background: #f1f5f9; 
        font-weight: 800; 
        color: var(--text-dark);
        border-left: 4px solid var(--accent-blue);
    }

    /* 3. PROFIT/LOSS INDICATORS */
    .net-profit-up { 
        background: linear-gradient(135deg, #059669 0%, #10b981 100%); 
        color: #fff !important; 
        border-radius: 12px; 
    }
    
    .net-profit-down { 
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); 
        color: #fff !important; 
        border-radius: 12px; 
    }

    .indent { padding-left: 40px !important; color: var(--text-grey) !important; font-size: 0.85rem !important; }
    
    .money-font { font-family: 'JetBrains Mono', monospace; font-weight: 700; }

    /* 4. PRINT OPTIMIZATION */
    @media print {
        .no-print, .filter-card, .btn { display: none !important; }
        .report-container { border: none; padding: 0; box-shadow: none; }
        body { background: white; }
    }
</style>

<div class="container-fluid pb-5">
    {{-- TOP BAR --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-extrabold text-dark mb-0" style="letter-spacing: -1px;">LAPORAN LABA RUGI</h3>
            <p class="text-muted fw-bold text-uppercase small" style="letter-spacing: 1px;">
                <i class="fas fa-chart-line me-1"></i> Performa Finansial • Arqom Kitchen
            </p>
        </div>
        <div class="col-md-6 text-md-end no-print">
            <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm">
                <i class="fas fa-print me-2"></i> CETAK LAPORAN
            </button>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 filter-card">
        <div class="card-body p-4">
            <form action="{{ route('labarugi.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2 text-uppercase">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control border-0 bg-light fw-bold" value="{{ $tgl_mulai }}">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2 text-uppercase">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control border-0 bg-light fw-bold" value="{{ $tgl_selesai }}">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-2 text-uppercase">Cari Akun Spesifik</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-light fw-bold" placeholder="Misal: Beban Sewa..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm py-2">
                        <i class="fas fa-filter me-1"></i> FILTER
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- REPORT CONTENT --}}
    <div class="report-container">
        {{-- Report Header (Internal) --}}
        <div class="text-center mb-5">
            <h4 class="fw-bold mb-0">ARQOM KITCHEN</h4>
            <p class="text-muted text-uppercase fw-bold small mb-1">Statement of Profit or Loss</p>
            <p class="fw-bold small border-top d-inline-block pt-1">
                Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tgl_selesai)->format('d M Y') }}
            </p>
        </div>

        <table class="table table-borderless table-lr mb-0">
            {{-- 1. PENDAPATAN --}}
            <tr class="header-row">
                <td><i class="fas fa-arrow-down me-2 text-success"></i> Pendapatan Usaha</td>
                <td class="text-end">Nilai (IDR)</td>
            </tr>
            @php $totalP = 0; @endphp
            @forelse($pendapatan as $p)
                <tr>
                    <td class="indent">{{ $p['nama_akun'] }}</td>
                    <td class="text-end money-font">Rp {{ number_format($p['total'], 0, ',', '.') }}</td>
                </tr>
                @php $totalP += $p['total']; @endphp
            @empty
                <tr><td colspan="2" class="indent text-muted small italic">Tidak ada data pendapatan</td></tr>
            @endforelse
            <tr class="total-row text-primary">
                <td class="ps-3">TOTAL PENDAPATAN USAHA</td>
                <td class="text-end money-font">Rp {{ number_format($totalP, 0, ',', '.') }}</td>
            </tr>

            {{-- 2. COGS --}}
            <tr class="header-row">
                <td class="pt-4"><i class="fas fa-tags me-2 text-danger"></i> Harga Pokok Penjualan (HPP)</td>
                <td class="text-end pt-4"></td>
            </tr>
            @php $totalC = 0; @endphp
            @forelse($cogs as $c)
                <tr>
                    <td class="indent">{{ $c['nama_akun'] }}</td>
                    <td class="text-end text-danger money-font">({{ number_format($c['total'], 0, ',', '.') }})</td>
                </tr>
                @php $totalC += $c['total']; @endphp
            @empty
                <tr><td colspan="2" class="indent text-muted small italic">Tidak ada HPP terekam</td></tr>
            @endforelse
            <tr class="total-row text-danger">
                <td class="ps-3">TOTAL HARGA POKOK PENJUALAN</td>
                <td class="text-end money-font">Rp {{ number_format($totalC, 0, ',', '.') }}</td>
            </tr>

            @php $labaKotor = $totalP - $totalC; @endphp
            <tr class="subtotal-row">
                <td class="ps-3">LABA KOTOR (GROSS PROFIT)</td>
                <td class="text-end money-font">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
            </tr>

            {{-- 3. BEBAN OPERASIONAL --}}
            <tr class="header-row">
                <td class="pt-4"><i class="fas fa-cog me-2 text-warning"></i> Beban Operasional</td>
                <td class="text-end pt-4"></td>
            </tr>
            @php 
                $totalBebanOp = 0; 
                $totalLainLain = 0;
                $lainLainList = [];
            @endphp
            @foreach($beban as $b)
                @php 
                    $isLainLain = str_contains(strtolower($b['nama_akun']), 'bunga') || str_contains(strtolower($b['nama_akun']), 'lain');
                @endphp
                
                @if(!$isLainLain)
                    <tr>
                        <td class="indent">{{ $b['nama_akun'] }}</td>
                        <td class="text-end text-danger money-font">({{ number_format($b['total'], 0, ',', '.') }})</td>
                    </tr>
                    @php $totalBebanOp += $b['total']; @endphp
                @else
                    @php $lainLainList[] = $b; @endphp
                @endif
            @endforeach
            <tr class="total-row" style="color: #d97706;">
                <td class="ps-3">TOTAL BEBAN OPERASIONAL</td>
                <td class="text-end money-font">Rp {{ number_format($totalBebanOp, 0, ',', '.') }}</td>
            </tr>

            @php $labaOp = $labaKotor - $totalBebanOp; @endphp
            <tr class="subtotal-row" style="background: #f0fdf4; border-left-color: #10b981;">
                <td class="ps-3">LABA OPERASIONAL (EBIT)</td>
                <td class="text-end text-success money-font">Rp {{ number_format($labaOp, 0, ',', '.') }}</td>
            </tr>

            {{-- 4. PENDAPATAN & BEBAN LAIN-LAIN --}}
            <tr class="header-row">
                <td class="pt-4"><i class="fas fa-coins me-2 text-info"></i> Non-Operasional (Lain-lain)</td>
                <td class="text-end pt-4"></td>
            </tr>
            @forelse($lainLainList as $l)
                <tr>
                    <td class="indent">{{ $l['nama_akun'] }}</td>
                    <td class="text-end money-font {{ $l['total'] < 0 ? 'text-success' : 'text-danger' }}">
                        {{ $l['total'] < 0 ? 'Rp ' . number_format(abs($l['total']), 0, ',', '.') : '(' . number_format($l['total'], 0, ',', '.') . ')' }}
                    </td>
                </tr>
                @php $totalLainLain += $l['total']; @endphp
            @empty
                <tr><td colspan="2" class="indent text-muted small italic">Tidak ada transaksi non-operasional</td></tr>
            @endforelse

            {{-- 5. FINAL NET PROFIT --}}
            @php $labaBersih = $labaOp - $totalLainLain; @endphp
            <tr><td colspan="2" class="py-4"></td></tr> {{-- Spacer --}}
            <tr class="{{ $labaBersih >= 0 ? 'net-profit-up' : 'net-profit-down' }}">
                <td class="py-4 ps-4 fs-5 fw-bold text-uppercase">Laba (Rugi) Bersih Neto</td>
                <td class="text-end py-4 pe-4 fs-4 money-font">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </td>
            </tr>
        </table>
        
        <div class="mt-5 d-none d-print-block">
            <div class="row">
                <div class="col-4 text-center">
                    <p class="mb-5 small fw-bold">Disetujui Oleh,</p>
                    <br><br>
                    <p class="border-top d-inline-block px-4 small">Manager Finansial</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection