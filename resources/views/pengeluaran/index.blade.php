@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --danger-red: #ef4444;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .page-title { font-weight: 800; color: var(--text-dark); text-transform: uppercase; letter-spacing: -0.02em; font-size: 1.4rem; }
    
    .table-container { 
        background: #ffffff; padding: 25px; border-radius: 20px; 
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
    }

    .table thead th { 
        background-color: #f8fafc;
        padding: 15px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-grey) !important;
        border-bottom: 2px solid #f1f5f9;
    }

    .table tbody td { 
        vertical-align: middle;
        padding: 16px 15px;
        color: var(--text-dark) !important;
        font-weight: 500;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .money-cell {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 130px;
        font-family: 'monospace';
        font-weight: 600;
    }
    .money-cell span:first-child { font-size: 0.7rem; opacity: 0.5; }

    .code-badge { 
        background: #f8fafc; color: var(--text-dark); padding: 5px 12px; border-radius: 8px; 
        font-family: monospace; font-size: 0.8rem; font-weight: 800; border: 1px solid #e2e8f0;
    }

    .cat-badge {
        font-size: 0.65rem; font-weight: 800; padding: 6px 12px; border-radius: 8px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px;
    }
    .cat-baku { background-color: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .cat-jadi { background-color: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
    .cat-ops { background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
    .cat-cogs { background-color: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }

    .img-evidence { 
        width: 42px; height: 42px; object-fit: cover; border-radius: 10px; 
        border: 2px solid #f1f5f9; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .img-evidence:hover { transform: scale(1.8); z-index: 50; border-color: var(--text-dark); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

    .filter-card {
        background: #f8fafc; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; margin-bottom: 25px;
    }
    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.85rem; padding: 8px 12px; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Manajemen Pengeluaran</h4>
            <p class="text-muted small mb-0 text-uppercase fw-bold">Data Pengeluaran & Stok • Arqom Kitchen</p>
        </div>
        <a href="{{ route('pengeluaran.create') }}" class="btn btn-danger px-4 py-2 shadow-sm fw-bold border-0" style="border-radius: 10px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <i class="fas fa-plus-circle me-2"></i> TAMBAH PENGELUARAN
        </a>
    </div>

    <div class="table-container">
        <div class="filter-card shadow-none">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Dari</label>
                    <input type="date" name="start_date" class="form-control form-control-custom" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Sampai</label>
                    <input type="date" name="end_date" class="form-control form-control-custom" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.65rem;">Cari Transaksi</label>
                    <input type="text" name="search" class="form-control form-control-custom" placeholder="Cari vendor, proyek, atau barang..." value="{{ request('search') }}">
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
                        <th class="text-center">KATEGORI</th> 
                        <th>VENDOR & PROYEK</th>
                        <th>URAIAN BARANG</th>
                        <th class="text-center">QTY</th>
                        <th class="text-end">HARGA</th>
                        <th class="text-end">TOTAL</th>
                        <th class="text-center">BUKTI</th> 
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalPengeluaran = 0; @endphp
                    @forelse($data as $p)
                    @php $totalPengeluaran += $p->tot_peng; @endphp
                    <tr>
                        <td class="text-center"><span class="code-badge">{{ $p->kd_peng }}</span></td>
                        <td class="fw-bold">{{ \Carbon\Carbon::parse($p->tgl_peng)->format('d/m/y') }}</td>
                        
                        <td class="text-center">
                            @if($p->kategori == 'bahan_baku')
                                <span class="cat-badge cat-baku"><i class="fas fa-cubes"></i> BAKU</span>
                            @elseif($p->kategori == 'barang_jadi')
                                <span class="cat-badge cat-jadi"><i class="fas fa-box-open"></i> JADI</span>
                            @elseif($p->kategori == 'cogs_lainnya')
                                <span class="cat-badge cat-cogs"><i class="fas fa-tags"></i> COGS</span>
                            @else
                                <span class="cat-badge cat-ops"><i class="fas fa-briefcase"></i> OPS</span>
                            @endif
                        </td>

                        <td>
                            <div class="fw-bold text-dark text-uppercase" style="font-size: 0.8rem;">{{ $p->nm_vendor }}</div>
                            @if(in_array($p->kategori, ['bahan_baku', 'cogs_lainnya', 'barang_jadi']))
                                <div class="text-primary fw-bold" style="font-size: 0.7rem;">
                                    <i class="fas fa-project-diagram me-1"></i>{{ $p->nm_kons }}
                                </div>
                            @else
                                <div class="text-muted" style="font-size: 0.7rem;">INTERNAL ARQOM</div>
                            @endif
                        </td>

                        <td>
                            <div class="fw-bold text-uppercase">{{ $p->nm_bhn }}</div>
                            <div class="text-muted italic" style="font-size: 0.7rem;">{{ $p->spek_peng ?? '-' }}</div>
                        </td>
                        
                        <td class="text-center">
                            <span class="fw-bold">{{ $p->jml_peng ?? 0 }}</span>
                            <span class="text-muted small ms-1">{{ $p->satuan_peng }}</span>
                        </td>
                        
                        <td class="text-end">
                            <div class="money-cell text-muted"><span>Rp</span><span>{{ number_format($p->hrg_peng, 0, ',', '.') }}</span></div>
                        </td>

                        <td class="text-end">
                            <div class="money-cell text-dark fw-bold"><span>Rp</span><span>{{ number_format($p->tot_peng, 0, ',', '.') }}</span></div>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @if($p->buktinot_peng)
                                    <a href="{{ asset('gambar/' . $p->buktinot_peng) }}" target="_blank">
                                        <img src="{{ asset('gambar/' . $p->buktinot_peng) }}" class="img-evidence" title="Nota">
                                    </a>
                                @endif
                                @if($p->buktitf_peng)
                                    <a href="{{ asset('gambar/' . $p->buktitf_peng) }}" target="_blank">
                                        <img src="{{ asset('gambar/' . $p->buktitf_peng) }}" class="img-evidence" style="border-color: #dbeafe;" title="Transfer">
                                    </a>
                                @endif
                                @if(!$p->buktinot_peng && !$p->buktitf_peng)
                                    <i class="fas fa-image-slash text-muted opacity-25"></i>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="btn-group gap-1">
                                <a href="{{ route('pengeluaran.edit', $p->kd_peng) }}" class="btn btn-sm btn-outline-warning border-0"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('pengeluaran.destroy', $p->kd_peng) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted">Data pengeluaran tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot>
                    <tr style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                        <td colspan="7" class="text-end py-3 fw-bold text-uppercase" style="font-size: 0.7rem;">Total Tampil :</td>
                        <td class="text-end">
                            <div class="money-cell text-danger fw-bold fs-6">
                                <span>Rp</span>
                                <span>{{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                            </div>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        <div class="mt-4">
            {{ $data->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection