@extends('layout')

@section('content')
<style>
    .page-title { font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: -0.02em; }
    .table-container { 
        background: #ffffff; padding: 30px; border-radius: 20px; 
        border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02); 
    }
    .thead-custom { background: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .td-custom { font-weight: 600; color: #0f172a; vertical-align: middle; }
    .badge-qty { background: #eff6ff; color: #3b82f6; font-weight: 800; padding: 5px 12px; border-radius: 8px; }
    /* Badge proyek yang lebih menonjol */
    .badge-proyek { background: #f0fdf4; color: #16a34a; font-weight: 700; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; border: 1px solid #dcfce7; display: inline-block; }
    .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; transition: 0.3s; }
    
    .filter-section { background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #f1f5f9; }
    .form-select-sm, .form-control-sm { border-radius: 8px; border: 1px solid #e2e8f0; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Master Barang Jadi</h4>
            <p class="text-muted small mb-0 fw-bold text-uppercase">Stok Gudang & Manajemen HPP Per Proyek</p>
        </div>
        <a href="{{ route('barangjadi.create') }}" class="btn btn-dark px-4 fw-bold rounded-3 shadow-sm">
            <i class="fas fa-plus me-2"></i> TAMBAH BARANG
        </a>
    </div>

    <div class="table-container">
        <div class="filter-section">
            <form action="{{ route('barangjadi.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="filter_konsumen" class="form-select form-select-sm shadow-none" onchange="this.form.submit()">
                        <option value="">-- Semua Proyek/Konsumen --</option>
                        @foreach($list_konsumen as $k)
                            <option value="{{ $k->nm_kons }}" {{ request('filter_konsumen') == $k->nm_kons ? 'selected' : '' }}>
                                {{ $k->nm_kons }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm shadow-none" placeholder="Cari nama barang..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold rounded-2">CARI</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('barangjadi.index') }}" class="btn btn-sm btn-outline-secondary w-100 fw-bold rounded-2">RESET</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover border-0">
                <thead class="thead-custom">
                    <tr>
                        <th class="border-0 px-4 py-3">Kode</th>
                        <th class="border-0 py-3">Nama Barang</th>
                        <th class="border-0 py-3">Proyek / Konsumen</th> <th class="border-0 py-3 text-center">Stok</th>
                        <th class="border-0 py-3 text-end">HPP Unit</th>
                        <th class="border-0 py-3 text-end">Total Nilai</th>
                        <th class="border-0 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangjadi as $bj)
                    <tr>
                        <td class="td-custom px-4"><span class="text-primary">#{{ $bj->kd_brgjadi }}</span></td>
                        <td class="td-custom">{{ $bj->nm_brgjadi }}</td>
                        <td class="td-custom">
                            <span class="badge-proyek text-uppercase">
                                <i class="fas fa-project-diagram me-1"></i> {{ $bj->nm_kons ?? 'UMUM' }}
                            </span>
                        </td>
                        <td class="td-custom text-center"><span class="badge-qty">{{ $bj->jmlh_brgjadi }}</span></td>
                        <td class="td-custom text-end">Rp {{ number_format($bj->hpp_unit, 0, ',', '.') }}</td>
                        <td class="td-custom text-end fw-bold text-success">Rp {{ number_format($bj->hpp_total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('barangjadi.edit', $bj->id) }}" class="btn-action bg-light text-warning me-2"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('barangjadi.destroy', $bj->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action border-0 bg-light text-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted fw-bold">Data barang tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection