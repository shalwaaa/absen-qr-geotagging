<aside class="sidebar" id="sidebar">
<style>
    /* ===============================
       SIDEBAR FLOATING NATURE GREEN
    ================================ */
    :root {
        --p-dark: #142C14;
        --p-cal: #2D5128;
        --p-fern: #537B2F;
        --p-aspar: #8DA750;
        --p-light: #E4EB9C;
    }

    /* SIDEBAR MAIN STYLES */
    .sidebar {
        width: 280px;
        height: calc(100vh - 40px); 
        position: fixed;
        left: 20px;
        top: 20px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        color: var(--p-dark);
        display: flex;
        flex-direction: column;
        padding: 2rem 1.2rem;
        border-radius: 24px; 
        border: 1px solid rgba(45, 81, 40, 0.15);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        z-index: 1050;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Logo Section */
    .sidebar-header {
        margin-bottom: 2.5rem;
        display: flex;
        justify-content: flex-start;
        padding-left: 0.5rem;
    }

    .sidebar-logo {
        display: flex;
        align-items: center; 
        gap: 0.8rem;
        text-decoration: none;
    }

    .logo-icon {
        height: 52px; 
        width: auto;
    }

    .logo-text-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-top: 6px; 
        line-height: 1;
    }

    .logo-text {
        font-size: 1.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--p-cal), var(--p-fern));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.03em;
        margin: 0;
    }

    .logo-subtitle {
        font-size: 0.6rem;
        font-weight: 700;
        color: var(--p-aspar);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Menu Styling */
    .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
    }

    .sidebar-section {
        margin: 1.5rem 0 0.5rem 1rem;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.05em;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.9rem 1.2rem;
        border-radius: 16px;
        color: #64748b;
        text-decoration: none;
        margin-bottom: 0.4rem;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    /* --- INI KUNCI BIAR RAPI --- */
    .sidebar-link i {
        width: 30px; /* Saya besarkan dikit biar aman */        
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-right: 10px;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .sidebar-link:hover {
        background: var(--p-light);
        color: var(--p-dark);
        transform: translateX(5px);
    }

    .sidebar-link.active {
        background: linear-gradient(135deg, var(--p-cal), var(--p-fern));
        color: white;
        box-shadow: 0 8px 20px rgba(45, 81, 40, 0.25);
    }

    /* User Info */
    .sidebar-user {
        background: #f8faf7;
        border-radius: 20px;
        padding: 1rem;
        border: 1px solid rgba(45, 81, 40, 0.08);
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 12px rgba(45, 81, 40, 0.05);
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--p-fern), var(--p-aspar));
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 8px 15px rgba(45, 81, 40, 0.2);
        line-height: 1;
    }

    .user-avatar .month {
        font-size: 0.55rem;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.9;
    }
    
    .user-avatar .day {
        font-size: 1.1rem;
        font-weight: 800;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .user-name {
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--p-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-role {
        font-size: 0.65rem;
        color: var(--p-aspar);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Responsive Logic */
    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
            left: 0;
            top: 0;
            height: 100vh;
            width: 300px;
            border-radius: 0 24px 24px 0;
            border-left: none;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 280px;
            padding: 1.5rem 1rem;
        }
        .logo-icon { height: 45px; }
        .logo-text { font-size: 1.2rem; }
    }

    @media (max-width: 480px) {
        .sidebar { width: 260px; }
        .sidebar-header { margin-bottom: 2rem; }
        .sidebar-section { margin: 1.2rem 0 0.5rem 0.8rem; font-size: 0.6rem; }
        .sidebar-user { padding: 0.8rem; }
        .user-avatar { width: 40px; height: 40px; }
        .user-avatar .day { font-size: 1rem; }
    }
</style>

<div class="sidebar-header">
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
        <img src="{{ asset('images/logo.png') }}" class="logo-icon" alt="Logo">
        <div class="logo-text-wrapper">
            <span class="logo-text">ClockIn</span>
            <span class="logo-subtitle">Digital Presence</span>
        </div>
    </a>
</div>

<nav class="sidebar-menu">
    <div class="sidebar-section">Utama</div>
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    @if(Auth::user()->role === 'admin')
        <div class="sidebar-section">Manajemen</div>
        <a href="{{ route('users.index', ['type' => 'teacher']) }}" class="sidebar-link {{ request()->fullUrlIs(route('users.index', ['type'=>'teacher']).'*') ? 'active' : '' }}">
            <i class="fa-solid fa-chalkboard-user"></i> Data Guru
        </a>
        
        <a href="{{ route('users.index', ['type' => 'student']) }}" class="sidebar-link {{ request()->fullUrlIs(route('users.index', ['type'=>'student']).'*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-graduate"></i> Data Siswa
        </a>
        
        <a href="{{ route('classrooms.index') }}" class="sidebar-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
            <i class="fa-solid fa-school"></i> Kelas
        </a>
        <a href="{{ route('promotions.index') }}" class="sidebar-link {{ request()->routeIs('promotions.*') ? 'active' : '' }}">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Kenaikan Kelas
        </a>
        <a href="{{ route('subjects.index') }}" class="sidebar-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
            <i class="fa-solid fa-book"></i> Mapel
        </a>
        <a href="{{ route('schedules.index') }}" class="sidebar-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i> Jadwal
        </a>
        <a href="{{ route('academic-years.index') }}" class="sidebar-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check"></i> Tahun Ajaran
        </a>
    @endif
</nav>

<div class="sidebar-user">
    <div class="user-avatar">
        <span class="month">{{ date('M') }}</span>
        <span class="day">{{ date('d') }}</span>
    </div>
    
    <div class="user-details">
        <span class="user-name">{{ date('l') }}</span>
        <span class="user-role">Tahun {{ date('Y') }}</span>
    </div>
</div>
</aside>