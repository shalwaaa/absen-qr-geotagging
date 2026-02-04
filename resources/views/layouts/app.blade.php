<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


<title>Presensi SMAKZIE QR and Geotagging</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo3.png') }}">



    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Palette: Nature Green */
            --primary-dark: #142C14;
            --primary-cal: #2D5128;
            --primary-fern: #537B2F;
            --primary-aspar: #8DA750;
            --primary-light: #E4EB9C;
            
            /* Gradient & UI Colors */
            --primary-gradient: linear-gradient(135deg, #2D5128 0%, #537B2F 100%);
            --soft-green: #f1f7ed;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --text-main: #142C14;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            background: 
                radial-gradient(circle at 10% 20%, rgba(83, 123, 47, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(228, 235, 156, 0.1) 0%, transparent 40%),
                #fcfdfa;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: var(--primary-cal);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(45, 81, 40, 0.3);
            transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle:hover {
            background: var(--primary-fern);
        }
        
        .mobile-menu-toggle.active {
            left: 320px;
            transform: rotate(180deg);
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Responsive Main Content */
        .app-main {
            margin-left: 320px; /* Jarak untuk sidebar mengambang */
            width: calc(100% - 320px);
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .welcome-banner {
            background: var(--primary-gradient);
            border-radius: 24px;
            padding: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 20px 40px rgba(45, 81, 40, 0.15);
        }

        .welcome-banner::after {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .app-headbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-pill {
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }

        .avatar-circle {
            width: 35px;
            height: 35px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .app-content {
            background: white;
            border-radius: 30px;
            padding: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
            min-height: 60vh;
            transition: all 0.3s ease;
        }

        /* Mobile Notification Bell */
        .mobile-notification {
            display: none;
            background: white;
            border-radius: 12px;
            padding: 0.6rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            margin-right: 10px;
            cursor: pointer;
        }

        /* Search Box */
        .search-box-placeholder {
            min-width: 200px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.7rem 1.2rem;
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            color: #64748b;
        }

        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(3px);
        z-index: 1000;
        opacity: 0;
        visibility: hidden; 
        transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
        }

        /* Efek blur pada content saat sidebar terbuka */
        body.sidebar-open .app-main {
            filter: blur(2px);
            opacity: 0.7;
            /* pointer-events: none; */
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 1024px) {
            .mobile-menu-toggle {
                display: flex;
            }
            
            .mobile-notification {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .app-main { 
                margin-left: 0; 
                width: 100%; 
                padding: 1.5rem; 
                padding-top: 5rem;
            }
            
            /* Sidebar akan diatur oleh JavaScript */
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050 !important;
            }
            
            .sidebar-open {
                transform: translateX(0) !important;
            }
            
            .sidebar-overlay {
                /* display: none; */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(3px);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
            }

            .app-content {
                padding: 1.5rem;
                border-radius: 20px;
            }

            .welcome-banner {
                padding: 1.5rem;
                border-radius: 20px;
            }

            .welcome-banner h1 {
                font-size: 1.4rem !important;
            }
        }

        @media (max-width: 768px) {
            .app-main {
                padding: 1.2rem;
                padding-top: 4.5rem;
            }
            
            .mobile-menu-toggle {
                top: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
                /* display: none; */
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1100;
            }
            
            .mobile-menu-toggle.active {
                left: 295px;
            }
            
            .search-box-placeholder {
                min-width: 150px;
                padding: 0.6rem 1rem;
            }
            
            .user-pill {
                padding: 0.4rem 0.8rem;
            }
            
            .avatar-circle {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }
            
            .user-name {
                font-size: 0.8rem !important;
            }

            .sidebar.sidebar-open {
            transform: translateX(0) !important;
            }
        }

        @media (max-width: 640px) {
            .app-main {
                padding: 1rem;
                padding-top: 4rem;
            }
            
            .app-headbar {
                flex-direction: row;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .headbar-right {
                display: flex;
                align-items: center;
            }
            
            .welcome-banner h1 {
                font-size: 1.3rem !important;
            }
            
            .welcome-banner p {
                font-size: 0.85rem !important;
            }
            
            .app-content {
                padding: 1.2rem;
                border-radius: 18px;
            }
            
            .app-footer {
                font-size: 0.75rem !important;
            }
        }

        @media (max-width: 480px) {
            .app-main {
                padding-top: 3.5rem;
            }
            
            .mobile-menu-toggle {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .mobile-menu-toggle.active {
                left: 280px;
            }
            
            .user-name {
                display: none;
            }
            
            .welcome-banner {
                padding: 1.2rem;
            }
            
            .welcome-banner::after {
                display: none;
            }
        }

        @media (max-width: 360px) {
            .app-main {
                padding: 0.8rem;
                padding-top: 3.5rem;
            }
            
            .mobile-menu-toggle {
                top: 10px;
                left: 10px;
            }
            
            .mobile-menu-toggle.active {
                left: 260px;
            }
            
            .welcome-banner {
                padding: 1rem;
                border-radius: 16px;
            }
            
            .app-content {
                padding: 1rem;
            }
            
            .app-footer {
                font-size: 0.7rem !important;
            }
        }
    </style>
</head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<body>
     
    <!-- Single Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fa-solid fa-bars"></i>
    </button>
    
    <!-- Single Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-layout">
        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="app-main" id="mainContent">
            <div class="app-main-inner">

                <div class="app-headbar">
                    <div class="headbar-left">
                        <!-- Mobile Notification Bell -->
                        <div class="mobile-notification" id="notificationBell">
                            <i class="fa-solid fa-bell" style="color: var(--primary-fern); font-size: 1rem;"></i>
                        </div>
                        
                        <div class="user-pill">
                            <div class="avatar-circle">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="user-name" style="font-weight: 700; font-size: 0.85rem; color: var(--primary-dark);">
                                {{ Auth::user()->name }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}" style="margin-left: 10px; display: flex; align-items: center;">
                                @csrf
                                <button type="submit" style="border:none; background:none; cursor:pointer; color:#ef4444; font-size: 1.1rem; padding: 0;">
                                    <i class="fa-solid fa-power-off"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="welcome-banner">
                    <div style="position: relative; z-index: 2;">
                        @if(isset($header))
                            <h1 id="greeting" style="font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.02em;">
                                Hi, {{ Auth::user()->name }}!
                            </h1>
                            <p style="opacity: 1 !important; font-size: 0.9rem; margin-top: 5px; font-weight: 500; color: rgba(255,255,255,0.9) !important;">
                                Sedang di halaman: 
                                <span style="color: #E4EB9C !important; font-weight: 800; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); display: inline-block;">
                                    {{ $header }}
                                </span>
                            </p>
                        @endif
                    </div>
                </div>

                <main class="app-content">
                    {{ $slot }}
                </main>

                <footer class="app-footer" style="margin-top: 3rem; padding-bottom: 2rem; text-align: center; color: #94a3b8; font-size: 0.8rem;">
                     Presences Geotagging • Created by Shin's Yura <br> 
                     <span style="color: var(--primary-aspar); font-weight: 600;">&copy; {{ date('Y') }} ClockIn</span>
                </footer>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileMenuToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;
            const mainContent = document.getElementById('mainContent');
            
            // Toggle sidebar function
            function toggleSidebar() {
                if (sidebar) {
                    sidebar.classList.toggle('sidebar-open');
                }
                mobileToggle.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                body.classList.toggle('sidebar-open');
                
                // Change icon
                const icon = mobileToggle.querySelector('i');
                if (body.classList.contains('sidebar-open')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            // Mobile menu toggle
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }
            
            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (body.classList.contains('sidebar-open')) {
                        toggleSidebar();
                    }
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 1024 && 
                    body.classList.contains('sidebar-open') && 
                    !sidebar.contains(e.target) && 
                    e.target !== mobileToggle) {
                    toggleSidebar();
                }
            });
            
            // Close sidebar with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
                    toggleSidebar();
                }
            });
            
            // Auto-close sidebar when clicking a link on mobile
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        setTimeout(() => {
                            if (body.classList.contains('sidebar-open')) {
                                toggleSidebar();
                            }
                        }, 300);
                    }
                });
            });
            
            // Dynamic greeting based on time of day
            function updateGreeting() {
                const greetingElement = document.getElementById('greeting');
                if (!greetingElement) return;
                
                const hour = new Date().getHours();
                let greeting = 'Hi';
                
                if (hour >= 5 && hour < 11) greeting = 'Selamat Pagi';
                else if (hour >= 11 && hour < 15) greeting = 'Selamat Siang';
                else if (hour >= 15 && hour < 19) greeting = 'Selamat Sore';
                else greeting = 'Selamat Malam';
                
                // Extract user name from current text
                const currentText = greetingElement.textContent;
                const nameMatch = currentText.match(/(?:Hi|Selamat\s+\w+),\s+(.+)!/);
                if (nameMatch && nameMatch[1]) {
                    greetingElement.textContent = `${greeting}, ${nameMatch[1]}!`;
                }
            }
            
            // Initialize and update greeting every hour
            updateGreeting();
            setInterval(updateGreeting, 3600000);
            
            // Notification bell click
            const notificationBell = document.getElementById('notificationBell');
            if (notificationBell) {
                notificationBell.addEventListener('click', function() {
                    alert('Notifikasi: Fitur akan segera hadir!');
                });
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024 && body.classList.contains('sidebar-open')) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>