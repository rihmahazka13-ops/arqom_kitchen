<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Arqom Kitchen POS</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('gambar/logo.jpg') }}">    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --navy-dark: #0f172a;
            --navy-light: #1e293b;
            --soft-slate: #f1f5f9;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-slate);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            display: flex;
            width: 100%;
            max-width: 950px;
            min-height: 550px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
        }

        /* KOLOM KIRI: LOGO & NAVY THEME */
        .login-side-brand {
            flex: 1;
            background-color: var(--navy-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .brand-logo-large {
            width: 180px;
            height: 180px;
            object-fit: cover; 
            border-radius: 50%; 
            border: 4px solid rgba(255, 255, 255, 0.1);
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
            z-index: 2;
        }

        .brand-title {
            color: white;
            font-weight: 800;
            font-size: 1.6rem;
            margin-top: 25px;
            letter-spacing: 1px;
            z-index: 2;
            text-transform: uppercase;
        }

        .decor-circle {
            position: absolute;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            z-index: 1;
        }

        /* KOLOM KANAN: FORM LOGIN */
        .login-side-form {
            flex: 1.2;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .login-header {
            margin-bottom: 35px;
            width: 100%;
        }

        .login-header h2 {
            font-weight: 800;
            font-size: 2.2rem;
            color: var(--navy-dark);
            margin-bottom: 8px;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        form {
            width: 100%;
            max-width: 380px;
        }

        .form-label-custom {
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 8px;
            display: block;
            text-align: left;
        }

        /* PASSWORD TOGGLE STYLING */
        .password-container {
            position: relative;
        }

        .form-control-pos {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control-pos:focus {
            background-color: #fff;
            border-color: var(--navy-dark);
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
            outline: none;
        }

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
            color: var(--navy-dark);
        }

        .btn-signin {
            background: var(--navy-dark);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 800;
            width: 100%;
            transition: 0.3s;
            letter-spacing: 0.5px;
            margin-top: 15px;
        }

        .btn-signin:hover {
            background: var(--navy-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }

        .footer-copyright {
            margin-top: 40px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        @media (max-width: 991px) {
            .login-side-brand { display: none; }
            .login-wrapper { max-width: 450px; }
            .login-side-form { padding: 40px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-side-brand">
            <div class="decor-circle" style="width: 300px; height: 300px; top: -100px; right: -50px;"></div>
            <div class="decor-circle" style="width: 200px; height: 200px; bottom: -50px; left: -50px;"></div>
            
            <img src="{{ asset('gambar/logo.jpg') }}" alt="Logo Arqom Kitchen" class="brand-logo-large">
            <h1 class="brand-title">ARQOM KITCHEN</h1>
        </div>

        <div class="login-side-form">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masukkan detail akun Anda untuk masuk ke sistem.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 small mb-4 w-100" style="border-radius: 10px; max-width: 380px;">
                    <i class="fas fa-circle-exclamation me-2"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ url('login') }}" method="POST">
                @csrf
                <div class="mb-4 text-start">
                    <label class="form-label-custom">Alamat Email</label>
                    <input type="email" name="email" class="form-control form-control-pos" 
                           placeholder="nama@email.com" required autofocus value="{{ old('email') }}">
                </div>
                
                <div class="mb-3 text-start">
                    <label class="form-label-custom">Kata Sandi</label>
                    <div class="password-container">
                        <input type="password" name="password" id="passwordField" class="form-control form-control-pos" 
                               placeholder="••••••••" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-signin">
                    Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="footer-copyright">
                &copy; {{ date('Y') }} <strong>Arqom Kitchen</strong>. Sistem Administrasi & POS.
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');

        togglePassword.addEventListener('click', function () {
            // Toggle tipe input antara 'password' dan 'text'
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle ikon mata
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>