<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ITP Digital System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc2626;
            --primary-dark: #991b1b;
            --accent: #ef4444;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-glass: rgba(255, 255, 255, 0.95);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            overflow-x: hidden;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Side Panel - Desktop Only */
        .side-panel {
            flex: 1.2;
            position: relative;
            background: var(--primary-dark);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 4rem;
            color: white;
            overflow: hidden;
        }

        .side-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('/ship_construction_background_1776958569512.png') center/cover no-repeat;
            opacity: 0.6;
            mix-blend-mode: luminosity;
            transform: scale(1.05);
            animation: slowZoom 20s infinite alternate ease-in-out;
        }

        .side-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(153, 27, 27, 0.95), transparent);
        }

        .side-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .side-content > * {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .side-badge { animation-delay: 0.2s; }
        .side-title { 
            font-size: 3.5rem; font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; letter-spacing: -1px; 
            animation-delay: 0.4s;
        }
        .side-subtitle { 
            font-size: 1.1rem; opacity: 0.9; line-height: 1.6; font-weight: 400; 
            animation-delay: 0.6s;
        }

        /* Form Panel */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }

        /* Abstract Background Elements with Movement */
        .shape { 
            position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.08; z-index: 0;
            animation: floatShape 15s infinite ease-in-out alternate;
        }
        .shape-1 { width: 350px; height: 350px; background: var(--primary); top: -50px; right: -50px; }
        .shape-2 { width: 250px; height: 250px; background: var(--accent); bottom: 10%; left: -50px; animation-delay: -5s; }

        .login-card {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
            background: var(--bg-glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            animation: slideInRight 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .brand-header { margin-bottom: 2rem; }
        .brand-logo-anim {
            max-width: 150px;
            margin-bottom: 1.5rem;
            animation: floatLogo 6s infinite ease-in-out;
            mix-blend-mode: multiply;
        }
        
        .welcome-text h2 { font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.5px; }
        .welcome-text p { color: var(--text-muted); font-size: 0.95rem; }

        .form-group { position: relative; margin-bottom: 1.5rem; }
        .form-group label { 
            display: block; 
            font-size: 0.75rem; 
            font-weight: 800; 
            color: var(--text-main); 
            margin-bottom: 0.5rem; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); transition: color 0.3s; font-size: 1.1rem; }
        
        .form-control-custom {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid transparent;
            border-radius: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) inset;
        }

        .form-control-custom:hover {
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .form-control-custom:focus {
            background: #ffffff;
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.15);
        }
        .form-control-custom:focus + i { color: var(--primary); }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 1rem;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 2rem;
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.25);
        }

        .btn-submit:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 15px 25px rgba(220, 38, 38, 0.35); 
            background: linear-gradient(135deg, #ef4444, #b91c1c);
        }
        .btn-submit:active { transform: scale(0.98) translateY(0); }
        
        .btn-submit i { transition: transform 0.3s; }
        .btn-submit:hover i { transform: translateX(5px); }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        /* KEYFRAMES */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
            100% { transform: translate(-20px, 20px) scale(0.95); }
        }

        @keyframes floatLogo {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes slowZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.15); }
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        /* Mobile Adjustments */
        @media (max-width: 991.98px) {
            .side-panel { display: none; }
            .form-panel { background: #f8fafc; padding: 1.5rem; }
            .login-card { 
                padding: 2.5rem; 
            }
        }

        @media (max-width: 575.98px) {
            .login-card { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Desktop Left Side -->
        <div class="side-panel">
            <div class="side-content">
                <div class="mb-4 side-badge">
                    <span class="badge bg-white text-danger px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.7rem; letter-spacing: 1px;">SISTEM MONITORING TERPADU</span>
                </div>
                <h1 class="side-title">ITP Digital<br>System</h1>
                <p class="side-subtitle">
                    Solusi digital cerdas untuk manajemen <strong>Inspection and Test Plan (ITP)</strong> yang presisi. Kami menghadirkan transparansi, akurasi data, dan efisiensi kolaborasi dalam satu ekosistem terpadu untuk memastikan standar kualitas tertinggi pada setiap proyek konstruksi maritim Anda.
                </p>
            </div>
        </div>

        <!-- Form Side -->
        <div class="form-panel">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>

            <div class="login-card">
                <div class="brand-header text-center text-sm-start">
                    <img src="{{ asset('assets/images/brin-logo.png') }}" alt="BRIN Logo" class="brand-logo-anim">
                    <div class="welcome-text">
                        <h2>Selamat Datang</h2>
                        <p>Masukkan kredensial Anda untuk mengakses sistem</p>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="/login">
                    @csrf
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-wrapper">
                            <input type="text" name="username" class="form-control-custom" placeholder="Nama pengguna" required autofocus>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" class="form-control-custom" placeholder="Kata sandi" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Masuk ke Sistem</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="mt-5 text-center">
                    <p style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 0.5px;">
                        &copy; 2026 ITP Monitoring System &bull; Version 2.0.1
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>