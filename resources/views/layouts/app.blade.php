<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Inventaris Gudang</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #1e293b;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background-color: #334155;
            color: #ffffff;
        }

        .sidebar .nav-link.active {
            background-color: #2563eb; /* biru elegan */
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            margin-right: 8px;
        }
    </style>

</head>

<body>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <aside class="sidebar p-3">
    <h4 class="text-white mb-4">Sistem Inventaris </h4>

    <ul class="nav nav-pills flex-column gap-1">
        @role('Admin')
        <li class="nav-item">
            <a href="{{ route('dashboard.admin') }}"
               class="nav-link {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
               <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('products.index') }}"
            class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Daftar Produk
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('products.katalog') }}"
            class="nav-link {{ request()->routeIs('products.katalog') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Katalog
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('barang-masuk.index') }}"
               class="nav-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
               <i class="bi bi-arrow-down-square"></i> Barang Masuk
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('barang-keluar.index') }}"
               class="nav-link {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
               <i class="bi bi-arrow-up-square"></i> Barang Keluar
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Laporan
            </a>
        </li>

        @endrole

        @role('Gudang')
        <li class="nav-item">
            <a href="{{ route('dashboard.gudang') }}"
               class="nav-link {{ request()->routeIs('dashboard.gudang') ? 'active' : '' }}">
               <i class="bi bi-speedometer2"></i> Dashboard Gudang
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('products.katalog') }}"
                class="nav-link {{ request()->routeIs('products.katalog') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Katalog
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('barang-masuk.index') }}"
               class="nav-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
               <i class="bi bi-arrow-down-square"></i> Barang Masuk
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('barang-keluar.index') }}"
               class="nav-link {{ request()->routeIs('barang-keluar.*') ? 'active' : '' }}">
               <i class="bi bi-arrow-up-square"></i> Barang Keluar
            </a>
        </li>
        @endrole

        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</aside>


        <!-- MAIN CONTENT -->
        <main class="flex-grow-1 bg-light">
            <div class="p-4">
                @yield('content')
            </div>
        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
