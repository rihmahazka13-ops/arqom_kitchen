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
        -webkit-font-smoothing: antialiased;
    }

    .page-title { 
        font-weight: 800; 
        color: var(--text-dark); 
        text-transform: uppercase; 
        font-size: 1.4rem;
        margin-bottom: 0;
        letter-spacing: -0.02em;
    }
    
    .page-subtitle {
        font-size: 0.85rem;
        color: var(--text-grey);
        font-weight: 500;
        margin-top: 4px;
    }

    .btn-tambah {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white !important;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        transition: 0.3s;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
    }

    .search-card {
        background: #ffffff;
        padding: 18px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }

    .form-control-search {
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .table-container { 
        background: #ffffff; 
        padding: 25px; 
        border-radius: 16px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
    }

    .table thead th { 
        padding: 18px 20px;
        background-color: #f8fafc;
        color: var(--text-grey) !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }

    .table tbody td { 
        padding: 22px 20px !important; 
        color: var(--text-dark) !important;
        font-weight: 500;
        font-size: 0.875rem;
        border-bottom: 1px solid #f8fafc;
        white-space: nowrap;
        vertical-align: middle;
    }

    .money-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-variant-numeric: tabular-nums;
    }

    .code-badge { 
        background: #f1f5f9; 
        color: var(--text-dark); 
        padding: 5px 12px; 
        border-radius: 8px; 
        font-size: 0.75rem; 
        font-weight: 800; 
        border: 1px solid #e2e8f0;
        display: inline-block;
    }

    .margin-positive {
        color: #15803d !important;
        background: #dcfce7;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 700;
    }

    .margin-negative {
        color: #b91c1c !important;
        background: #fee2e2;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 700;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Manajemen Data Barang Produksi</h4>
            <p class="page-subtitle mb-0">Kalkulasi HPP Riil • Arqom Kitchen</p>
        </div>
        <a href="{{ route('barangproduksi.create') }}" class="btn btn-tambah">
            <i class="fas fa-plus me-2"></i> TAMBAH DATA BARANG
        </a>
    </div>

    <div class="table-container">
        <div class="search-card shadow-none">
            <form action="{{ route('barangproduksi.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 0.05em;">Pencarian Data (Filter)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3" style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1;">
                            <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 form-control-search" 
                               style="border-radius: 0 10px 10px 0; border: 1px solid #cbd5e1;"
                               placeholder="Cari barang atau nama konsumen..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn w-100 py-2" type="submit" style="background: var(--text-dark); color: white; border-radius: 10px; font-weight: 700; font-size: 0.85rem;">CARI</button>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="border-radius: 10px; background: #ecfdf5; border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle me-3 text-success fs-5"></i> 
                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ session('success') }}</div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">KODE</th>
                        <th>KONSUMEN/PROYEK</th>
                        <th>BARANG PRODUKSI</th>
                        <th class="text-center">QTY</th>
                        <th class="text-end">EST. HPP (UNIT)</th>
                        <th class="text-end" style="background: #f8fafc;">TOTAL EST. HPP</th> 
                        <th class="text-end">RIIL HPP (UNIT)</th>
                        <th class="text-end">RIIL HPP (TOTAL)</th>
                        <th class="text-end" style="background: #f1f5f9;">SELISIH / MARGIN</th> 
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedBahans = $bahans->groupBy('nm_kons');
                        $runningGrandTotalReal = 0; // Untuk menghitung footer secara akurat
                    @endphp

                    @forelse ($groupedBahans as $nm_kons => $items)
                        @php
                            $groupSumEst = $items->sum('tot_est');
                            // Perbaikan: Ganti 'total' menjadi 'tot_bhn' sesuai tabel bahanbaku Anda
                            $realBahanBakuProyek = \DB::table('bahanbaku') 
                                                    ->where('nm_kons', $nm_kons)
                                                    ->sum('tot_bhn'); 
                        @endphp
                        
                        @foreach ($items as $b)
                            @php
                                if ($groupSumEst > 0) {
                                    $totalRealBaris = ($b->tot_est / $groupSumEst) * $realBahanBakuProyek;
                                } else {
                                    $totalRealBaris = 0;
                                }

                                $hppRealPerUnit = ($b->jml_pro > 0) ? ($totalRealBaris / $b->jml_pro) : 0;
                                $selisihRow = $b->tot_est - $totalRealBaris;
                                
                                $runningGrandTotalReal += $totalRealBaris;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <span class="code-badge">{{ $b->kd_bhnpro }}</span>
                                </td>
                                <td class="text-uppercase fw-bold text-primary" style="font-size: 0.8rem;">
                                    {{ $b->nm_kons ?? 'UMUM' }}
                                </td>
                                <td class="fw-bold">{{ $b->bhnpro }}</td>
                                <td class="text-center fw-bold">{{ number_format($b->jml_pro, 0) }}</td>
                                
                                <td class="text-end">
                                    <div class="money-wrapper text-muted">
                                        <span>Rp</span>
                                        <span>{{ number_format($b->est_hpp, 0, ',', '.') }}</span>
                                    </div>
                                </td>

                                <td class="text-end" style="background-color: #fcfdfe;">
                                    <div class="money-wrapper fw-bold text-primary">
                                        <span>Rp</span>
                                        <span>{{ number_format($b->tot_est, 0, ',', '.') }}</span>
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="money-wrapper fw-bold">
                                        <span>Rp</span>
                                        <span>{{ number_format($hppRealPerUnit, 0, ',', '.') }}</span>
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="money-wrapper">
                                        <span>Rp</span>
                                        <span>{{ number_format($totalRealBaris, 0, ',', '.') }}</span>
                                    </div>
                                </td>

                                <td class="text-end" style="background: #fcfdfe;">
                                    <div class="money-wrapper {{ $selisihRow >= 0 ? 'margin-positive' : 'margin-negative' }}">
                                        <span>{{ $selisihRow >= 0 ? '+' : '-' }}</span>
                                        <span>{{ number_format(abs($selisihRow), 0, ',', '.') }}</span>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group gap-2">
                                        <a href="{{ route('barangproduksi.edit', $b->kd_bhnpro) }}" class="btn btn-sm btn-outline-warning border-0">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('barangproduksi.destroy', $b->kd_bhnpro) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="10" class="text-center py-5 text-muted fw-bold">Data produksi belum tersedia.</td></tr>
                    @endforelse
                </tbody>
                
                <tfoot style="border-top: 2px solid #f1f5f9;">
                    <tr class="fw-bold" style="background-color: #f8fafc;">
                        <td colspan="7" class="text-end py-3 text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">Grand Total Estimasi HPP :</td>
                        <td>
                            <div class="money-wrapper text-primary" style="font-size: 0.9rem; font-weight: 700;">
                                <span>Rp</span>
                                <span>{{ number_format($sumTotEst, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="fw-bold" style="background-color: #fcfdfe;">
                        <td colspan="7" class="text-end py-3 text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">Grand Total Riil HPP (Terpakai) :</td>
                        <td>
                            <div class="money-wrapper text-dark" style="font-size: 1rem; font-weight: 800;">
                                <span>Rp</span>
                                <span>{{ number_format($runningGrandTotalReal, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($bahans->hasPages())
        <div class="mt-4">
            {{ $bahans->appends(request()->input())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection