<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/dashboard') }}">
                <img src="{{ asset('custom/assetsFoto/logo.jpeg') }}" alt="Logo Klinik" class="logo-fluid"
                    style="height: 50px; width: auto; margin-right: 15px;margin-left: 15px;">
                <span class="brand-text" style="color: white; font-size: 20px; font-weight: bold; line-height: 1.2;">
                    Griya Sehat<br>Pahlawan
                </span>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Dashboard Menu</h4>
                </li>
                <li class="nav-item {{ Request::path() == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ url('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Main Menu</h4>
                </li>
                <li class="nav-item {{ Request::path() == 'users' ? 'active' : '' }}">
                    <a href="{{ url('users') }}">
                        <i class="fas fa-stethoscope"></i>
                        <p>Tenaga Ahli</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::path() == 'pasien' ? 'active' : '' }}">
                    <a href="{{ url('pasien') }}">
                        <i class="fas fa-procedures"></i>
                        <p>Pasien</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::path() == 'obat' ? 'active' : '' }}">
                    <a href="{{ url('obat') }}">
                        <i class="fas fa-pills"></i>
                        <p>Obat</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">SubMenu</h4>
                </li>
                <li class="nav-item {{ Request::path() == 'obatmasuk' ? 'active' : '' }}">
                    <a href="{{ url('obatmasuk') }}">
                        <i class="fas fa-pills"></i>
                        <p>Obat Masuk</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::path() == 'kunjungan' ? 'active' : '' }}">
                    <a href="{{ url('kunjungan') }}">
                        <i class="fas fa-stethoscope"></i>
                        <p>Kunjungan</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::path() == 'pemeriksaan' ? 'active' : '' }}">
                    <a href="{{ url('pemeriksaan') }}">
                        <i class="fas fa-notes-medical"></i>
                        <p>Pemeriksaan</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Pembayaran & Riwayat</h4>
                </li>
                <li class="nav-item {{ Request::path() == 'pembayaran' ? 'active' : '' }}">
                    <a href="{{ url('pembayaran') }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <p>Pembayaran</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::path() == 'riwayat' ? 'active' : '' }}">
                    <a href="{{ url('riwayat') }}">
                        <i class="fas fa-history"></i>
                        <p>Riwayat Kunjungan</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->

<style>
    .sidebar {
        transition: all 0.3s ease;
    }

    .brand-text {
        transition: opacity 0.3s ease, width 0.3s ease;
    }

    .sidebar.collapsed .brand-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }

    @media (max-width: 991px) {
        .logo-header .navbar-brand {
            max-width: calc(100% - 60px);
        }

        .brand-text {
            font-size: 18px !important;
        }
    }

    @media (max-width: 575px) {
        .brand-text {
            font-size: 16px !important;
        }
    }

    @media (max-width: 350px) {
        .brand-text {
            display: none;
        }

        .logo-fluid {
            margin: 0;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: none !important;
        }

        .logo-header {
            position: relative;
            justify-content: center;
            padding: 10px 0;
        }

        .navbar-brand {
            width: 100%;
            justify-content: center;
        }

        .nav-toggle,
        .topbar-toggler {
            visibility: hidden;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('admin/assets/js/your-sidebar-script.js') }}"></script>

<script>
    $(document).ready(function() {
        $('.toggle-sidebar, .sidenav-toggler, .topbar-toggler').on('click', function() {
            $('.sidebar').toggleClass('collapsed');
        });
    });
</script>
