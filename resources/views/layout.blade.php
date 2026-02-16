<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi | Arqom Kitchen POS</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('gambar/logo.jpg') }}"> 
    
    {{-- FONTS & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">    
    
    <style>
        :root {
            --navy-dark: #0f172a;
            --navy-light: #1e293b;
            --accent-blue: #3b82f6;
            --soft-slate: #f8fafc;
            --text-muted: #94a3b8;
            --text-white: #ffffff;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #ffffff; /* Background Putih Bersih */
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* === SIDEBAR (Fixed Left) === */
        #sidebar { 
            width: 260px; 
            height: 100vh; 
            background-color: var(--navy-dark); 
            color: white; 
            position: fixed;
            top: 0;
            left: 0;
            display: flex; 
            flex-direction: column;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 5px 0 20px rgba(0,0,0,0.05);
        }

        /* Brand Area */
        #sidebar .brand { 
            padding: 30px 25px; 
            color: #fff; 
            text-decoration: none; 
            display: flex; 
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 20px;
        }

        .brand-icon-box {
            width: 45px; height: 45px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            color: var(--accent-blue);
        }

        .brand-name {
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        /* Menu Wrapper */
        .nav-menu-wrapper { 
            flex: 1; 
            overflow-y: auto; 
            padding: 0 15px;
        }

        /* Menu Links */
        .nav-link { 
            color: var(--text-muted); 
            padding: 12px 20px; 
            font-size: 0.9rem;
            font-weight: 600; 
            display: flex; align-items: center;
            gap: 12px;
            transition: all 0.3s ease; 
            text-decoration: none;
            border-radius: 12px; /* Pill Shape */
            margin-bottom: 5px;
        }

        .nav-link i { width: 25px; font-size: 1.1rem; text-align: center; }

        .nav-link:hover { 
            color: #fff; 
            background: rgba(255, 255, 255, 0.05); 
        }

        /* Active State */
        .nav-link.active { 
            background: var(--accent-blue);
            color: #fff; 
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .nav-section-label {
            font-size: 0.7rem; 
            text-transform: uppercase; 
            color: var(--text-muted); 
            padding: 20px 20px 10px 20px; 
            font-weight: 800; 
            letter-spacing: 1px;
            opacity: 0.6;
        }

        /* User Panel (Bottom Sticky) */
        .user-panel {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
            background: rgba(0,0,0,0.1);
        }

        .btn-logout {
            color: #ef4444;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            width: 100%;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-logout:hover { background: #ef4444; color: #fff; }

        /* === MAIN CONTENT (Full Width) === */
        #content-wrapper { 
            margin-left: 260px; /* Geser konten ke kanan sebesar lebar sidebar */
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 260px);
            transition: 0.3s;
        }

        /* Mobile Header */
        .mobile-header {
            display: none; /* Sembunyikan di Desktop */
            padding: 15px 20px;
            background: var(--navy-dark);
            color: white;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* === RESPONSIVE (HP/Tablet) === */
        @media (max-width: 992px) {
            #sidebar { left: -260px; } /* Sembunyikan sidebar */
            #sidebar.active { left: 0; } /* Munculkan saat aktif */
            
            #content-wrapper { 
                margin-left: 0; 
                width: 100%; 
                padding: 20px;
            }

            .mobile-header { display: flex; } /* Tampilkan header mobile */
        }

        /* Scrollbar Halus */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        #sidebar ::-webkit-scrollbar-thumb { background: var(--navy-light); }
    </style>
</head>
<body>

    {{-- HEADER MOBILE (Hanya muncul di HP) --}}
    <div class="mobile-header shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-utensils text-primary"></i>
            <span class="fw-bold">ARQOM KITCHEN</span>
        </div>
        <button class="btn text-white p-0" id="mobileToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
    </div>

    {{-- SIDEBAR NAVIGASI --}}
    <nav id="sidebar">
        <div class="brand">
            <div class="brand-icon-box">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="d-flex flex-column">
                <span class="brand-name">Arqom Kitchen</span>
                <small class="text-muted" style="font-size: 0.7rem;">Administrator</small>
            </div>
        </div>
        
        <div class="nav-menu-wrapper">
            {{-- DASHBOARD --}}
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> 
                <span>Dashboard</span>
            </a>

            {{-- MASTER DATA --}}
            <div class="nav-section-label">Master Data</div>
            <a href="{{ route('barangjual.index') }}" class="nav-link {{ request()->routeIs('barangjual.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> <span>Barang Jual</span>
            </a>
            <a href="{{ route('bahanbaku.index') }}" class="nav-link {{ request()->routeIs('bahanbaku.*') ? 'active' : '' }}">
                <i class="fas fa-boxes-stacked"></i> <span>Bahan Baku</span>
            </a>
            <a href="{{ route('barangproduksi.index') }}" class="nav-link {{ request()->routeIs('barangproduksi.*') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i> <span>Barang Produksi</span>
            </a>
            <a href="{{ route('barangjadi.index') }}" class="nav-link {{ request()->routeIs('barangjadi.*') ? 'active' : '' }}">
                <i class="fas fa-warehouse"></i> <span>Barang Jadi</span>
            </a>
            <a href="{{ route('konsumen.index') }}" class="nav-link {{ request()->routeIs('konsumen.*') ? 'active' : '' }}">
                <i class="fas fa-users-viewfinder"></i> <span>Konsumen</span>
            </a>
            <a href="{{ route('vendor.index') }}" class="nav-link {{ request()->routeIs('vendor.*') ? 'active' : '' }}">
                <i class="fas fa-truck-ramp-box"></i> <span>Partner Vendor</span>
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-user-gear"></i> <span>Master User</span>
            </a>

            {{-- OPERASIONAL --}}
            <div class="nav-section-label">Operasional</div>
            <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> <span>Penjualan</span>
            </a>
            <a href="{{ route('pemasukan.index') }}" class="nav-link {{ request()->routeIs('pemasukan.*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> <span>Pemasukan</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="nav-link {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> <span>Pengeluaran</span>
            </a>

            {{-- AKUNTANSI (MENU BARU) --}}
            <div class="nav-section-label">Akuntansi</div>
            <a href="{{ route('coa.index') }}" class="nav-link {{ request()->routeIs('coa.*') ? 'active' : '' }}">
                <i class="fas fa-book-journal-whills"></i> <span>Chart of Account</span>
            </a>
            <a href="{{ route('jurnal.index') }}" class="nav-link {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
                <i class="fas fa-pen-nib"></i> <span>Jurnal Umum</span>
            </a>
            <a href="{{ route('labarugi.index') }}" class="nav-link {{ request()->routeIs('labarugi.index') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> <span>Laporan Laba Rugi</span>
            </a>
            <a href="{{ route('neraca.index') }}" class="nav-link {{ request()->routeIs('neraca.index') ? 'active' : '' }}">
                <i class="fas fa-balance-scale"></i> <span>Laporan Posisi Keuangan</span>
            </a>
            <a href="{{ route('ekuitas.index') }}" class="nav-link {{ request()->routeIs('ekuitas.index') ? 'active' : '' }}">
                <i class="fas fa-seedling"></i> <span>Laporan Perubahan Ekuitas</span>
            </a>
            </div>

        <div class="user-panel">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-power-off"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- AREA KONTEN UTAMA --}}
    <main id="content-wrapper">
        
        {{-- Alert Notifikasi Global --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="background: #ecfdf5; border-left: 4px solid #10b981; border-radius: 12px;">
                <i class="fas fa-check-circle text-success me-3 fs-5"></i>
                <div class="fw-bold text-dark">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" style="background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 12px;">
                <i class="fas fa-exclamation-triangle text-danger me-3 fs-5"></i>
                <div class="fw-bold text-dark">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Overlay Gelap (Mobile Only) --}}
    <div id="mobileOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk Mobile Sidebar
        const toggleBtn = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');

        function toggleMenu() {
            sidebar.classList.toggle('active');
            const isActive = sidebar.classList.contains('active');
            overlay.style.display = isActive ? 'block' : 'none';
        }

        if(toggleBtn) toggleBtn.addEventListener('click', toggleMenu);
        if(overlay) overlay.addEventListener('click', toggleMenu);
    </script>
</body>
</html>