<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Absensi Sekolah</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- CSS VARS --- */
        :root {
            --dark-green: #2D5128;
            --mid-green:  #537B2F;
            --light-green:#8DA750;
        }

        * {
            box-sizing: border-box; /* Wajib agar padding tidak merusak lebar */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef2eb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px; /* Padding luar agar kartu tidak nempel tepi HP */
        }

        /* --- ANIMASI --- */
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes logoPulse {
            0% { transform: scale(1); box-shadow: 0 8px 25px rgba(45, 81, 40, 0.4); }
            50% { transform: scale(1.03); box-shadow: 0 10px 30px rgba(45, 81, 40, 0.6); }
            100% { transform: scale(1); box-shadow: 0 8px 25px rgba(45, 81, 40, 0.4); }
        }

        /* --- CONTAINER UTAMA --- */
        .login-container {
            display: flex;
            width: 900px;
            max-width: 100%; /* Agar tidak melebar di layar kecil */
            min-height: 550px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(45, 81, 40, 0.15);
            background-color: #fff;
            opacity: 0;
            animation: fadeSlideIn 0.8s ease-out forwards;
        }

        /* PANEL KIRI (BRANDING) */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--dark-green), var(--mid-green));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Dekorasi Pattern */
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 20%),
                              radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 20%);
            pointer-events: none;
        }

        .logo-wrapper {
            position: relative;
            width: 220px;
            height: 220px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            transition: transform 0.3s ease; /* Transisi halus saat resize */
        }

        .logo-box {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--mid-green), var(--light-green));
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            animation: logoPulse 4s infinite alternate ease-in-out;
        }

        .logo-placeholder {
            position: absolute;
            z-index: 10;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .logo-placeholder img {
            width: 80%;
            height: auto;
            object-fit: contain;
        }

        .left-panel h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .left-panel p {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 300;
            margin-top: 10px;
            line-height: 1.5;
        }

        /* PANEL KANAN (FORM) */
        .right-panel {
            flex: 1.2;
            background-color: #ffffff;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-panel h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 5px;
        }

        .right-panel h3 {
            font-size: 1rem;
            font-weight: 400;
            margin-bottom: 40px;
            color: #6b7280;
        }

        /* INPUT STYLING */
        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark-green);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-line {
            width: 100%;
            border: none;
            border-bottom: 2px solid #e5e7eb;
            background-color: transparent;
            padding: 10px 5px;
            outline: none;
            font-size: 1rem;
            color: #333;
            transition: all 0.3s;
        }

        .input-line:focus {
            border-bottom-color: var(--mid-green);
            background-color: #f0fdf4;
        }

        /* BUTTONS */
        .login-btn {
            border: none;
            border-radius: 12px;
            padding: 15px 0;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            background: linear-gradient(to right, var(--dark-green), var(--mid-green));
            color: white;
            box-shadow: 0 4px 15px rgba(45, 81, 40, 0.3);
            transition: all 0.3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(45, 81, 40, 0.4);
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* --- MEDIA QUERIES (RESPONSIVE) --- */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column; /* Ubah jadi atas-bawah */
                height: auto;
                min-height: auto;
                width: 100%;
            }

            .left-panel {
                padding: 40px 20px;
                /* Membuat lengkungan di bawah panel hijau */
                border-bottom-left-radius: 30px;
                border-bottom-right-radius: 30px;
            }

            .right-panel {
                padding: 40px 30px; /* Padding dikurangi untuk layar kecil */
            }

            /* Perkecil Logo di Mobile */
            .logo-wrapper { width: 140px; height: 140px; margin-bottom: 15px; }
            .logo-box { width: 120px; height: 120px; }
            .logo-placeholder { width: 70px; height: 70px; }
            
            .left-panel h2 { font-size: 1.5rem; }
            .right-panel h1 { font-size: 1.8rem; }
        }

        @media (max-width: 480px) {
            body { padding: 10px; } /* Mepetin ke pinggir dikit di HP kecil */
            
            .right-panel {
                padding: 30px 20px;
            }

            .input-line { font-size: 0.9rem; }
        }
    </style>
</head>

<body>

    <div class="login-container">
        
        <!-- PANEL KIRI (ATAS DI MOBILE) -->
        <div class="left-panel">
            <div class="logo-wrapper">
                <div class="logo-box"></div>
                <div class="logo-placeholder">
                    <img src="{{ asset('images/logo2.png') }}" alt="Logo Sekolah"
                        onerror="this.onerror=null; this.src='https://placehold.co/100x100?text=LOGO';">
                </div>
            </div>
            <h2>Absensi Online</h2>
            <p>Sistem Absensi QR Code & Geotagging<br>Terintegrasi & Realtime</p>
        </div>

        <!-- PANEL KANAN (BAWAH DI MOBILE) -->
        <div class="right-panel">
            <h1>Selamat Datang</h1>
            <h3>Silakan login untuk melanjutkan.</h3>

            <!-- Pesan Error -->
            @if ($errors->any())
                <div class="message alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Login Gagal. Periksa inputan Anda.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Login ID -->
                <div class="input-group">
                    <label for="login_id" class="input-label">Email / NIP / NIS</label>
                    <input id="login_id" name="login_id" type="text" required autofocus
                        class="input-line @error('login_id') is-invalid @enderror"
                        placeholder="Masukkan Email, NIP, atau NIS" 
                        value="{{ old('login_id') }}">
                    
                    @error('login_id')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password" class="input-label">Password</label>
                    <input id="password" name="password" type="password" required
                        class="input-line @error('password') is-invalid @enderror"
                        placeholder="Masukkan Password">
                    
                    @error('password')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tombol -->
                <button type="submit" class="login-btn">
                    Masuk Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>

            </form>
            <br>

            <div class="mt-8 text-center text-xs text-gray-400" style="color:rgb(177, 174, 174);">
                &copy; {{ date('Y') }} Sistem Absensi Sekolah. All rights reserved By Shin's Yura.
            </div>
        </div>
    </div>

</body>
</html>