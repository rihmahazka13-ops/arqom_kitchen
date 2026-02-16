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

    /* TABLE STYLE */
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
        padding: 18px 20px !important; 
        color: var(--text-dark) !important;
        font-weight: 500;
        font-size: 0.875rem;
        border-bottom: 1px solid #f8fafc;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* AVATAR & IDENTITY */
    .user-avatar {
        width: 38px; 
        height: 38px;
        background-color: #eff6ff;
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 0.9rem;
        border: 1px solid #dbeafe;
    }

    .email-link {
        color: #64748b !important;
        font-size: 0.8rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .email-link:hover {
        color: #3b82f6 !important;
    }

    .id-badge {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 6px;
    }
</style>

<div class="container-fluid py-4">
    {{-- HEADER SECTION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Manajemen Pengguna</h4>
            <p class="page-subtitle mb-0">Otoritas Sistem • Arqom Kitchen</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-tambah">
            <i class="fas fa-user-plus me-2"></i> TAMBAH USER BARU
        </a>
    </div>

    <div class="table-container">
        {{-- SEARCH SECTION --}}
        <div class="search-card shadow-none">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 0.05em;">Cari Akun</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3" style="border-radius: 10px 0 0 10px; border: 1px solid #cbd5e1;">
                            <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 form-control-search" 
                               style="border-radius: 0 10px 10px 0;"
                               placeholder="Cari nama atau email pengguna..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn w-100 py-2" type="submit" style="background: var(--text-dark); color: white; border-radius: 10px; font-weight: 700; font-size: 0.85rem;">CARI USER</button>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="border-radius: 10px; background: #ecfdf5; border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle me-3 text-success fs-5"></i> 
                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ session('success') }}</div>
            </div>
        @endif

        {{-- TABLE SECTION --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">ID</th>
                        <th>IDENTITAS PENGGUNA</th>
                        <th>ALAMAT EMAIL</th>
                        <th class="text-center" style="width: 150px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $u)
                    <tr>
                        <td class="text-center">
                            <span class="id-badge">#{{ $u->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.02em;">{{ $u->name }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">System User</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="mailto:{{ $u->email }}" class="email-link">
                                <i class="fas fa-envelope-open opacity-50"></i>
                                {{ $u->email }}
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="btn-group gap-2">
                                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-outline-warning border-0" title="Edit Akun">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini dari sistem?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus Akun">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted fw-bold bg-light">
                            <i class="fas fa-user-secret d-block mb-3 fa-2x opacity-25"></i>
                            Data pengguna tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if(method_exists($data, 'links'))
        <div class="mt-4 d-flex justify-content-center">
            {{ $data->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection