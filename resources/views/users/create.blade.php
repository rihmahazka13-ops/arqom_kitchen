@extends('layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    /* Styling Header & Judul */
    .page-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        color: #0f172a !important;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        margin-bottom: 25px;
    }

    .form-container {
        background: #ffffff;
        padding: 40px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        max-width: 850px;
    }

    .form-label {
        font-weight: 700;
        color: #475569 !important;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: block;
    }

    /* POSISI LOGO & INPUT MENYATU SEMPURNA */
    .input-group-custom {
        display: flex;
        width: 100%;
        position: relative;
    }

    .input-group-text-custom {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-right: none;
        border-radius: 12px 0 0 12px !important;
        color: #64748b;
        min-width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-control-custom {
        border-radius: 0 12px 12px 0 !important;
        border: 1px solid #cbd5e1;
        padding: 12px 18px;
        font-size: 0.95rem;
        color: #0f172a !important;
        font-weight: 500;
        background-color: #fff;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control-password {
        padding-right: 45px !important;
    }

    .form-control-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* Toggle Password Icon */
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        transition: 0.2s;
        z-index: 10;
    }

    .toggle-password:hover {
        color: #0f172a;
    }

    .section-subtitle {
        font-weight: 800;
        color: #3b82f6; 
        font-size: 0.95rem;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-save {
        background-color: #0f172a; 
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 15px 25px;
        transition: 0.3s;
        letter-spacing: 1px;
    }

    .btn-save:hover {
        background-color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        color: white;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="page-title"><i class="fas fa-user-plus me-2 text-primary"></i> Tambah Pengguna Baru</h4>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm mb-4 border-0" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <ul class="mb-0 small fw-bold">
                @foreach ($errors->all() as $err)
                    <li><i class="fas fa-exclamation-triangle me-1"></i> {{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="section-subtitle">Informasi Profil Akun</div>
            
            <div class="row">
                {{-- Nama Lengkap --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Nama Lengkap Pengguna</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control-custom" 
                               value="{{ old('name') }}" placeholder="Masukkan nama lengkap staf" required autofocus>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Alamat Email --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Alamat Email Resmi</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control-custom" 
                               value="{{ old('email') }}" placeholder="contoh: staf@arqomkitchen.com" required>
                    </div>
                </div>
            </div>

            <div class="section-subtitle mt-2">Kredensial Login</div>
            
            <div class="row">
                {{-- Password --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label">Kata Sandi (Minimal 4 Karakter)</label>
                    <div class="input-group-custom">
                        <span class="input-group-text-custom"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="passwordField" class="form-control-custom form-control-password" 
                               placeholder="Buat kata sandi baru" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-shield-alt me-1"></i> Gunakan kombinasi karakter yang unik untuk keamanan.
                    </small>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-save flex-grow-1 shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> Daftarkan User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 fw-bold" 
                   style="border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT UNTUK TOGGLE PASSWORD --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>
@endsection