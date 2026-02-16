@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --accent-blue: #3b82f6;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* 1. HEADER & CONTAINER */
    .page-title { font-weight: 800; color: var(--text-dark); text-transform: uppercase; letter-spacing: -0.02em; font-size: 1.4rem; }
    
    .table-container { 
        background: #ffffff; padding: 25px; border-radius: 20px; 
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
    }

    /* 2. TABLE STYLING */
    .table thead th { 
        background-color: #f8fafc;
        padding: 15px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
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

    /* 3. ACCOUNTING FORMAT */
    .money-cell {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 120px;
        font-family: 'monospace'; /* Biar angka sejajar vertikal */
        font-weight: 600;
    }

    .money-cell span:first-child { font-size: 0.7rem; opacity: 0.5; font-weight: 400; }

    /* 4. VISUAL ELEMENTS */
    .code-badge { 
        background: #eff6ff; color: #2563eb; padding: 5px 12px; border-radius: 8px; 
        font-family: monospace; font-size: 0.8rem; font-weight: 800; border: 1px solid #dbeafe;
    }

    .filter-card {
        background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 25px;
    }

    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.85rem; padding: 8px 12px; }

    /* Status Badge */
    .badge-status {
        padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.03em;
    }
    .badge-lunas { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-utang { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    /* FOOTER TOTAL */
    .tfoot-total { background-color: #f8fafc; font-weight: 800; border-top: 2px solid #e2e8f0 !important; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Manajemen Penjualan</h4>
            <p class="text-muted small mb-0 text-uppercase fw-bold">Data Transaksi & Piutang • Arqom Kitchen</p>
        </div>
        <a href="{{ route('penjualan.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold" style="border-radius: 10px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none;">
            <i class="fas fa-plus me-2"></i> TAMBAH PENJUALAN
        </a>
    </div>

    <div class="table-container">
        {{-- FILTER AREA --}}
        <div class="filter-card shadow-none">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-custom" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Sampai</label>
                    <input type="date" name="end_date" class="form-control form-control-custom" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Cari Konsumen / Invoice</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 form-control-custom" placeholder="Nama konsumen..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Limit</label>
                    <select name="per_page" class="form-select form-control-custom">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-bold" style="border-radius: 10px; padding: 9px;">FILTER</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">KODE</th>
                        <th>TANGGAL</th>
                        <th>KONSUMEN</th>
                        <th class="text-end">TOTAL AWAL</th>
                        <th class="text-end">KIRIM</th>
                        <th class="text-end">TOTAL FINAL</th>
                        <th class="text-end text-success">DIBAYAR</th>
                        <th class="text-end text-danger">SISA</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $grandTotalAwal = 0; $grandTotalKirim = 0; $grandTotalFinal = 0; $grandTotalPiutang = 0; 
                    @endphp
                    @forelse($data as $k)
                    @php 
                        $sudahBayar = $k->pemasukans_sum_jml_pem ?? 0; 
                        $sisaTagihan = $k->tot_akhir - $sudahBayar;
                        $grandTotalAwal += $k->tot_awal;
                        $grandTotalKirim += $k->bud_pengiriman;
                        $grandTotalFinal += $k->tot_akhir;
                        $grandTotalPiutang += $sisaTagihan;
                    @endphp
                    <tr>
                        <td class="text-center"><span class="code-badge">{{ $k->kd_penj }}</span></td>
                        <td class="fw-bold">{{ \Carbon\Carbon::parse($k->tgl_penj)->format('d/m/y') }}</td>
                        <td>
                            <div class="fw-800 text-uppercase" style="font-weight: 700;">{{ $k->nm_kons }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $k->invoice ?? 'No Invoice' }}</div>
                        </td>
                        <td class="text-end"><div class="money-cell text-muted"><span>Rp</span><span>{{ number_format($k->tot_awal, 0, ',', '.') }}</span></div></td>
                        <td class="text-end"><div class="money-cell text-muted"><span>Rp</span><span>{{ number_format($k->bud_pengiriman, 0, ',', '.') }}</span></div></td>
                        <td class="text-end"><div class="money-cell fw-bold"><span>Rp</span><span>{{ number_format($k->tot_akhir, 0, ',', '.') }}</span></div></td>
                        <td class="text-end" style="background-color: #f0fdf4;"><div class="money-cell text-success"><span>Rp</span><span>{{ number_format($sudahBayar, 0, ',', '.') }}</span></div></td>
                        <td class="text-end" style="background-color: #fef2f2;"><div class="money-cell text-danger fw-800"><span>Rp</span><span>{{ number_format($sisaTagihan, 0, ',', '.') }}</span></div></td>
                        <td class="text-center">
                            @if($sisaTagihan <= 0)
                                <span class="badge-status badge-lunas">LUNAS</span>
                            @else
                                <span class="badge-status badge-utang">PIUTANG</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group gap-1">
                                <a href="{{ route('penjualan.edit', $k->kd_penj) }}" class="btn btn-sm btn-outline-warning border-0"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('penjualan.destroy', $k->kd_penj) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus data penjualan?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted fw-bold italic">Belum ada data penjualan yang terekam.</td></tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot>
                    <tr class="tfoot-total">
                        <td colspan="3" class="text-end py-3 text-muted" style="font-size: 0.7rem;">TOTAL HALAMAN INI</td>
                        <td class="text-end"><div class="money-cell"><span>Rp</span><span>{{ number_format($grandTotalAwal, 0, ',', '.') }}</span></div></td>
                        <td class="text-end"><div class="money-cell"><span>Rp</span><span>{{ number_format($grandTotalKirim, 0, ',', '.') }}</span></div></td>
                        <td class="text-end"><div class="money-cell text-dark"><span>Rp</span><span>{{ number_format($grandTotalFinal, 0, ',', '.') }}</span></div></td>
                        <td class="bg-white"></td>
                        <td class="text-end" style="background-color: #fee2e2;"><div class="money-cell text-danger"><span>Rp</span><span>{{ number_format($grandTotalPiutang, 0, ',', '.') }}</span></div></td>
                        <td colspan="2" class="bg-white"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($data->hasPages())
        <div class="mt-4">
            {{ $data->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection