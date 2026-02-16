@extends('layout')

@section('content')
<style>
    /* Menggunakan Plus Jakarta Sans dengan optimasi rendering */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --bg-light: #f8fafc;
        --card-white: #ffffff;
        --text-dark: #0f172a;
        --text-grey: #64748b;
        --border-color: #e2e8f0;
        --primary-blue: #3b82f6;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-dark);
        -webkit-font-smoothing: antialiased; /* Membuat font lebih tajam */
    }

    .dashboard-wrapper {
        padding: 20px 20px 40px 20px;
    }

    /* HEADER - Ukuran lebih proporsional */
    .welcome-text h2 {
        font-weight: 800;
        font-size: 1.6rem; /* Sedikit dikecilkan agar lebih elegan */
        margin-bottom: 4px;
        color: var(--text-dark);
        letter-spacing: -0.02em;
    }
    .welcome-text p {
        color: var(--text-grey);
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* CARD STATS UTAMA */
    .stat-card {
        background: var(--card-white);
        border-radius: 18px; /* Sudut lebih lembut */
        padding: 24px;
        height: 100%;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        text-decoration: none !important;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08);
        border-color: var(--primary-blue);
    }

    /* KARTU KASIR */
    .card-kasir {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border: none;
    }
    .card-kasir h3 { 
        color: white; 
        font-size: 1.4rem; 
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    .card-kasir p { color: rgba(255, 255, 255, 0.85); font-size: 0.85rem; }

    /* KARTU KEUANGAN */
    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 16px;
    }

    .card-title {
        font-size: 0.8rem; /* Lebih kecil agar terlihat profesional */
        color: var(--text-grey);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .card-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-dark);
        letter-spacing: -0.01em;
    }

    /* SECTION LABEL - Hierarki navigasi lebih jelas */
    .section-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-grey);
        margin: 35px 0 15px 0;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1.5px;
        background: var(--border-color);
    }

    /* MASTER & AKUNTANSI ITEM */
    .menu-item {
        background: var(--card-white);
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
        border: 1px solid var(--border-color);
        text-decoration: none;
        color: var(--text-dark);
    }

    .menu-item:hover {
        transform: scale(1.02);
        border-color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .menu-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .menu-info h5 {
        font-size: 0.9rem; /* Ukuran teks menu utama */
        font-weight: 700;
        margin: 0;
        color: var(--text-dark);
        letter-spacing: -0.01em;
    }
    .menu-info span {
        font-size: 0.75rem;
        color: var(--text-grey);
        font-weight: 500;
    }

    /* Perbaikan Khusus untuk Menu Laporan agar font tetap konsisten */
    .text-success, .text-primary, h5[style*="color"] {
        font-size: 0.9rem !important;
        font-weight: 700 !important;
    }
</style>

<div class="dashboard-wrapper">
    
    {{-- HEADER --}}
    <div class="row align-items-end mb-4">
        <div class="col-lg-8">
            <div class="welcome-text">
                <h2>Arqom Kitchen</h2>
                <p>Halo Admin, berikut ringkasan operasional hari ini.</p>
            </div>
        </div>
    </div>

    {{-- STATISTIK UTAMA --}}
    <div class="row g-4 mb-2">
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('penjualan.index') }}" class="text-decoration-none">
                <div class="stat-card card-kasir">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="icon-bg" style="background: rgba(255, 255, 255, 0.2); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">POS SYSTEM</span>
                    </div>
                    <div class="mt-4">
                        <h3>Penjualan</h3>
                        <p class="mb-0 opacity-75">Buka aplikasi Kasir</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6">
            <a href="{{ route('pemasukan.index') }}" class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-icon" style="background: #fdf2f8; color: #db2777;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div>
                    <div class="card-title">Pemasukan</div>
                    <div class="card-value">Data Masuk</div>
                    <p class="text-muted small mt-1 mb-0">Kelola arus kas masuk.</p>
                </div>
            </a>
        </div>

        <div class="col-lg-4 col-md-6">
            <a href="{{ route('pengeluaran.index') }}" class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-icon" style="background: #ecfdf5; color: #059669;">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div>
                    <div class="card-title">Pengeluaran</div>
                    <div class="card-value">Belanja</div>
                    <p class="text-muted small mt-1 mb-0">Catat biaya operasional.</p>
                </div>
            </a>
        </div>
    </div>

    {{-- BAGIAN: MASTER DATA --}}
    <div class="section-label">Database Master</div>
    <div class="row g-3">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('bahanbaku.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #eff6ff; color: #3b82f6;">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div class="menu-info">
                    <h5>Bahan Baku</h5>
                    <span>Stok Material</span>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('barangproduksi.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #fdf4ff; color: #c026d3;">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="menu-info">
                    <h5>Barang Produksi</h5>
                    <span>Menu & Produk</span>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('konsumen.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #f0fdfa; color: #0d9488;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="menu-info">
                    <h5>Data Konsumen</h5>
                    <span>Pelanggan</span>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="{{ route('vendor.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #fff7ed; color: #ea580c;">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <div class="menu-info">
                    <h5>Partner Vendor</h5>
                    <span>Supplier</span>
                </div>
            </a>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('users.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #fefce8; color: #ca8a04;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="menu-info">
                    <h5>Master User</h5>
                    <span>Hak Akses</span>
                </div>
            </a>
        </div>
    </div>

   {{-- AKUNTANSI --}}
    <div class="section-label">Akuntansi & Laporan</div>
    <div class="row g-3">
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('coa.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #e0e7ff; color: #4338ca;"><i class="fas fa-book-journal-whills"></i></div>
                <div class="menu-info"><h5>Chart of Accounts</h5><span>Daftar Akun</span></div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('jurnal.index') }}" class="menu-item">
                <div class="menu-icon" style="background: #fae8ff; color: #a21caf;"><i class="fas fa-pen-nib"></i></div>
                <div class="menu-info"><h5>Jurnal Umum</h5><span>Input Manual</span></div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('labarugi.index') }}" class="menu-item" style="border-left: 3px solid #15803d;">
                <div class="menu-icon" style="background: #dcfce7; color: #15803d;"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="menu-info"><h5 class="text-success">Laba Rugi</h5><span>Laporan Performa</span></div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
           <a href="{{ route('neraca.index') }}" class="menu-item" style="border-left: 3px solid #0369a1;">
                <div class="menu-icon" style="background: #e0f2fe; color: #0369a1;"><i class="fas fa-balance-scale"></i></div>
                <div class="menu-info"><h5 style="color: #0369a1;">Posisi Keuangan</h5><span>Laporan Neraca</span></div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('ekuitas.index') }}" class="menu-item" style="border-left: 3px solid #92400e;">
                <div class="menu-icon" style="background: #fef3c7; color: #92400e;"><i class="fas fa-seedling"></i></div>
                <div class="menu-info"><h5 style="color: #92400e;">Perubahan Ekuitas</h5><span>Laporan Modal</span></div>
            </a>
        </div>
    </div>
</div>
</div>
@endsection