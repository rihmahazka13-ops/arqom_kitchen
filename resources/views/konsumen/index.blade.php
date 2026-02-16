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

    /* BUTTON TAMBAH */
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
        border: 1px solid #cbd5e1;
    }

    /* TABLE CONTAINER */
    .table-container { 
        background: #ffffff; 
        padding: 25px; 
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
        white-space: nowrap;
    }

    /* TABLE BODY */
    .table tbody td { 
        padding: 22px 20px !important; 
        color: var(--text-dark) !important;
        font-weight: 500;
        font-size: 0.875rem;
        border-bottom: 1px solid #f8fafc;
        white-space: nowrap;
        vertical-align: middle;
    }

    .code-badge { 
        background: #eff6ff; 
        color: #2563eb; 
        padding: 6px 14px; 
        border-radius: 8px; 
        font-family: monospace; 
        font-size: 0.85rem; 
        font-weight: 800; 
        border: 1px solid #dbeafe;
        display: inline-block;
    }

    .contact-info-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-dark);
        font-weight: 600;
    }

    .contact-info-wrapper i {
        color: var(--text-grey);
        font-size: 0.8rem;
    }

    .location-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Manajemen Data Konsumen</h4>
            <p class="page-subtitle mb-0">Master Database Pelanggan • Arqom Kitchen</p>
        </div>
        <a href="{{ route('konsumen.create') }}" class="btn btn-tambah">
            <i class="fas fa-plus me-2"></i> TAMBAH KONSUMEN
        </a>
    </div>

    <div class="table-container">
        <div class="search-card shadow-none">
            <form action="{{ route('konsumen.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 0.05em;">Pencarian Data</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3" style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1;">
                            <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 form-control-search" 
                               style="border-radius: 0 10px 10px 0;"
                               placeholder="Cari nama atau kota konsumen..." 
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
                        <th class="text-center">KODE KONSUMEN</th>
                        <th>NAMA KONSUMEN</th>
                        <th>INFORMASI KONTAK</th>
                        <th>KOTA / WILAYAH</th>
                        <th class="text-center" style="width: 120px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $k)
                    <tr>
                        <td class="text-center">
                            <span class="code-badge">{{ $k->kd_kons }}</span>
                        </td>
                        <td class="fw-bold text-uppercase" style="letter-spacing: 0.02em;">
                            {{ $k->nm_kons }}
                        </td>
                        <td>
                            <div class="contact-info-wrapper">
                                <i class="fas fa-phone-alt"></i>
                                <span>{{ $k->kontak_kons }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="location-badge">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                {{ $k->kota_kons }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <a href="{{ route('konsumen.edit', $k->kd_kons) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                    <i class="fas fa-user-edit"></i>
                                </a>

                                <form action="{{ route('konsumen.destroy', $k->kd_kons) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus" onclick="return confirm('Hapus data konsumen ini?')">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted fw-bold bg-light">
                            <i class="fas fa-users-slash d-block mb-3 fa-2x opacity-25"></i>
                            Belum ada data konsumen yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection