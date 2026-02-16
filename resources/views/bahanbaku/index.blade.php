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

    /* HEADER STYLE */
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

    /* BUTTONS */
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

    /* SEARCH CARD */
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
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* TABLE CONTAINER */
    .table-container { 
        background: #ffffff; 
        padding: 25px; 
        border-radius: 166px; /* Sesuai kode awalmu */
        border-radius: 16px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
    }

    /* TABLE HEADER */
    .table thead th { 
        padding: 18px 20px;
        background-color: #f8fafc;
        color: var(--text-grey) !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap; /* BIAR JUDUL TIDAK NUMPUK */
    }

    /* TABLE BODY - Perbaikan jarak agar tidak numpuk */
    .table tbody td { 
        padding: 22px 20px !important; /* DITINGKATKAN agar baris lebih lega */
        color: var(--text-dark) !important;
        font-weight: 500;
        font-size: 0.875rem;
        border-bottom: 1px solid #f8fafc;
        white-space: nowrap; /* BIAR TULISAN PANJANG TIDAK TURUN KE BAWAH */
        vertical-align: middle;
    }

    .table tbody td.fw-bold {
        font-weight: 700 !important;
    }

    /* MONEY WRAPPER */
    .money-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-variant-numeric: tabular-nums;
    }

    .code-badge { 
        background: #f1f5f9; 
        color: var(--text-dark); 
        padding: 5px 12px; 
        border-radius: 8px; 
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.75rem; 
        font-weight: 800; 
        border: 1px solid #e2e8f0;
        display: inline-block;
    }

    /* PERBAIKAN WARNA BADGE OTOMATIS & MANUAL */
    .badge-auto {
        background: #dbeafe !important; /* Biru lebih jelas */
        color: #1e40af !important;      /* Teks biru tua */
        border: 1px solid #bfdbfe;
    }

    .badge-manual {
        background: #dcfce7 !important; /* Hijau lebih jelas */
        color: #15803d !important;      /* Teks hijau tua */
        border: 1px solid #bbf7d0;
    }

    .badge-qty {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .small-badge-text {
        font-size: 0.65rem !important;
        font-weight: 800;
        letter-spacing: 0.02em;
        padding: 4px 8px;
        border-radius: 5px;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Manajemen Bahan Baku</h4>
            <p class="page-subtitle mb-0">Inventaris Stok Material • Arqom Kitchen</p>
        </div>
        <a href="{{ route('bahanbaku.create') }}" class="btn btn-tambah">
            <i class="fas fa-plus me-2"></i> Tambah Data Manual
        </a>
    </div>

    <div class="table-container">
        <div class="search-card">
            <form action="{{ route('bahanbaku.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 0.05em;">Pencarian Data</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3" style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1; border-right: none;">
                            <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 form-control-search" 
                               style="border-radius: 0 10px 10px 0; border: 1px solid #cbd5e1;"
                               placeholder="Cari nama bahan atau konsumen..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-cari w-100 py-2" type="submit" style="background: var(--text-dark); color: white; border-radius: 10px; font-weight: 700; font-size: 0.85rem;">CARI</button>
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
                        <th class="text-center" style="width: 140px;">KODE & TIPE</th>
                        <th>KONSUMEN / PROYEK</th>
                        <th>NAMA BAHAN BAKU</th>
                        <th class="text-center">STOK</th>
                        <th>SATUAN</th>
                        <th class="text-end">HARGA SATUAN</th>
                        <th class="text-end">TOTAL NILAI</th>
                        <th class="text-center" style="width: 120px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bahans as $b)
                    @php $isSystem = Str::startsWith($b->kd_bhn, 'BB-'); @endphp
                    <tr>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <span class="code-badge">{{ $b->kd_bhn }}</span>
                                <span class="badge {{ $isSystem ? 'badge-auto' : 'badge-manual' }} small-badge-text">
                                    <i class="fas {{ $isSystem ? 'fa-robot' : 'fa-hand-paper' }} me-1"></i> 
                                    {{ $isSystem ? 'OTOMATIS' : 'MANUAL' }}
                                </span>
                            </div>
                        </td>
                        <td class="text-uppercase fw-bold text-primary" style="font-size: 0.8rem;">
                            {{ $b->nm_kons ?? 'UMUM' }}
                        </td>
                        <td class="text-dark fw-bold">{{ $b->nm_bhn }}</td>
                        <td class="text-center">
                            <span class="badge {{ $b->jml_bhn < 10 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} badge-qty border {{ $b->jml_bhn < 10 ? 'border-danger' : 'border-success' }}">
                                {{ number_format($b->jml_bhn, 0) }}
                            </span>
                        </td>
                        <td class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">{{ $b->satuan_bhn }}</td>
                        
                        <td class="text-end">
                            <div class="money-wrapper text-muted fw-semibold">
                                <span>Rp</span>
                                <span>{{ number_format($b->harga_bhn, 0, ',', '.') }}</span>
                            </div>
                        </td>

                        <td class="text-end">
                            <div class="money-wrapper text-dark fw-bold">
                                <span>Rp</span>
                                <span>{{ number_format($b->tot_bhn, 0, ',', '.') }}</span>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <a href="{{ route('bahanbaku.edit', $b->kd_bhn) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('bahanbaku.destroy', $b->kd_bhn) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" 
                                            onclick="return confirm('{{ $isSystem ? 'Peringatan: Data terhubung dengan Jurnal. Hapus?' : 'Hapus data manual?' }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted fw-bold bg-light">
                            <i class="fas fa-box-open d-block mb-3 fa-2x opacity-25"></i>
                            Belum ada data bahan baku tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                @if(!$bahans->isEmpty())
                <tfoot style="border-top: 2px solid #f1f5f9;">
                    <tr class="fw-bold" style="background-color: #fcfdfe;">
                        <td colspan="6" class="text-end py-4 text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">Total Nilai Inventaris :</td>
                        <td>
                            <div class="money-wrapper text-primary" style="font-size: 1rem; font-weight: 800;">
                                <span>Rp</span>
                                <span>{{ number_format($bahans->sum('tot_bhn'), 0, ',', '.') }}</span>
                            </div>
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