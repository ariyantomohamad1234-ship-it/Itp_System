<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ITP System Mini LNG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #050d1a;
            position: relative;
            overflow: hidden;
        }

        /* Animated gradient orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: #3b82f6; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 350px; height: 350px; background: #8b5cf6; bottom: -80px; right: -80px; animation-delay: 2s; }
        .orb-3 { width: 200px; height: 200px; background: #06b6d4; top: 50%; left: 60%; animation-delay: 4s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Grid lines overlay */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }

        /* Floating particles */
        .particles {
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(59,130,246,0.4);
            border-radius: 50%;
            animation: particleFloat 6s linear infinite;
        }

        @keyframes particleFloat {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        .login-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1.75rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 60px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05);
            animation: cardIn 0.6s ease-out;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .brand-section { text-align: center; margin-bottom: 2rem; }

        .brand-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #06b6d4);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 8px 30px rgba(59,130,246,0.35);
            animation: logoGlow 3s ease-in-out infinite alternate;
        }

        @keyframes logoGlow {
            from { box-shadow: 0 8px 30px rgba(59,130,246,0.25); }
            to { box-shadow: 0 8px 40px rgba(139,92,246,0.4); }
        }

        .brand-logo i { color: #fff; font-size: 2rem; }

        .brand-title {
            color: #fff;
            font-weight: 900;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .brand-subtitle {
            color: #475569;
            font-size: 0.7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .form-floating-custom { position: relative; margin-bottom: 1.25rem; }

        .form-floating-custom .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            font-size: 0.9rem;
            z-index: 2;
            transition: color 0.3s;
        }

        .form-floating-custom input {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.08);
            color: #fff;
            border-radius: 0.875rem;
            padding: 0.9rem 1rem 0.9rem 2.75rem;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            outline: none;
        }

        .form-floating-custom input:focus {
            background: rgba(255,255,255,0.06);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
        }

        .form-floating-custom input:focus ~ .input-icon { color: #3b82f6; }
        .form-floating-custom input::placeholder { color: #334155; }

        .form-floating-custom label {
            display: block;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none;
            color: #fff;
            padding: 1rem;
            border-radius: 0.875rem;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-login:hover::before { opacity: 1; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(59,130,246,0.4); }
        .btn-login:active { transform: scale(0.98); }
        .btn-login span { position: relative; z-index: 1; }

        .footer-text {
            text-align: center;
            color: #334155;
            font-size: 0.65rem;
            margin-top: 2rem;
            letter-spacing: 1px;
        }

        .alert-custom {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #fca5a5;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="grid-overlay"></div>

    <div class="particles" id="particles"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-logo">
                    <i class="fas fa-ship"></i>
                </div>
                <h1 class="brand-title">ITP System</h1>
                <p class="brand-subtitle">Mini LNG Vessel</p>
            </div>

            @if(session('error'))
                <div class="alert-custom">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="form-floating-custom">
                    <label>Username</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>

                <div class="form-floating-custom">
                    <label>Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn-login">
                    <span><i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem</span>
                </button>
            </form>

            <p class="footer-text">&copy; 2026 ITP Monitoring System &mdash; All Rights Reserved</p>
        </div>
    </div>

    <script>
        // Generate floating particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + '%';
            p.style.animationDelay = Math.random() * 6 + 's';
            p.style.animationDuration = (4 + Math.random() * 4) + 's';
            p.style.width = p.style.height = (2 + Math.random() * 3) + 'px';
            container.appendChild(p);
        }
    </script>
</body>
</html>