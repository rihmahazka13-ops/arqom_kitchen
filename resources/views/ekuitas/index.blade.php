@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap');

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .report-container { 
        background: #fff; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        padding: 40px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .table-report td { 
        padding: 15px 20px; 
        font-size: 1rem; 
        border-bottom: 1px solid #f1f5f9;
        color: #0f172a;
    }

    .money-font { font-family: 'JetBrains Mono', monospace; font-weight: 700; }
    
    .total-row-box { 
        background: #f8fafc; 
        border-top: 2px solid #0f172a !important;
        font-weight: 800;
    }

    @media print {
        .no-print { display: none !important; }
        .report-container { border: none; box-shadow: none; padding: 0; }
    }
</style>

<div class="container-fluid py-4">
    {{-- Header Page --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="fw-bold text-dark mb-0">LAPORAN PERUBAHAN EKUITAS</h4>
        <button onclick="window.print()" class="btn btn-outline-dark fw-bold rounded-pill px-4">
            <i class="fas fa-print me-2"></i> CETAK
        </button>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-4">
            <form action="{{ route('ekuitas.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-2">DARI TANGGAL</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $tgl_mulai }}">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-2">SAMPAI TANGGAL</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="{{ $tgl_selesai }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">FILTER</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Konten Laporan --}}
    <div class="report-container shadow-sm">
        {{-- Kop Surat untuk Cetak --}}
        <div class="text-center mb-5">
            <h4 class="fw-bold mb-0">ARQOM KITCHEN</h4>
            <p class="text-muted small fw-bold mb-1">LAPORAN PERUBAHAN EKUITAS</p>
            <span class="small text-secondary">Periode: {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tgl_selesai)->format('d/m/Y') }}</span>
        </div>

        <table class="table table-borderless table-report mb-0">
            <thead>
                <tr style="border-bottom: 2px solid #0f172a;">
                    <th class="pb-3 fw-extrabold" style="font-size: 0.85rem; letter-spacing: 1px;">DESKRIPSI PERUBAHAN MODAL</th>
                    <th class="pb-3 text-end fw-extrabold" style="font-size: 0.85rem; letter-spacing: 1px;">NILAI (IDR)</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. Modal Awal --}}
                <tr>
                    <td class="pt-4"><strong>Modal Awal</strong> (Per {{ \Carbon\Carbon::parse($tgl_mulai)->format('d M Y') }})</td>
                    <td class="text-end pt-4 money-font">Rp {{ number_format($modal_awal, 0, ',', '.') }}</td>
                </tr>

                {{-- 2. Laba Ditahan --}}
                <tr>
                    <td>Laba Ditahan Sebelumnya</td>
                    <td class="text-end money-font">Rp {{ number_format($laba_ditahan, 0, ',', '.') }}</td>
                </tr>

                {{-- 3. Laba Bersih (Logika Posisi Hijau) --}}
                <tr>
                    <td class="text-success fw-bold">Laba Bersih Periode Berjalan</td>
                    <td class="text-end text-success money-font">+ Rp {{ number_format($laba_bersih, 0, ',', '.') }}</td>
                </tr>

                {{-- 4. Dividen (Logika Posisi Merah) --}}
                <tr>
                    <td class="text-danger fw-bold">Dividen / Prive (Pengambilan)</td>
                    <td class="text-end text-danger money-font">- Rp {{ number_format($dividen, 0, ',', '.') }}</td>
                </tr>

                {{-- Logic Perhitungan Tetap Sama --}}
                @php 
                    $totalEkuitas = $modal_awal + $laba_ditahan + $laba_bersih - $dividen; 
                @endphp

                {{-- Spacer --}}
                <tr><td colspan="2" class="py-3"></td></tr>

                {{-- 5. TOTAL EKUITAS AKHIR --}}
                <tr class="total-row-box">
                    <td class="fs-5 py-4">EKUITAS AKHIR PER {{ \Carbon\Carbon::parse($tgl_selesai)->format('d M Y') }}</td>
                    <td class="text-end fs-4 py-4 money-font">
                        Rp {{ number_format($totalEkuitas, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection