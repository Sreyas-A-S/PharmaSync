<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PharmaSync') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        body, html { min-height: 100vh; }
        body { padding-top: 74px; } 
        .main-content { flex: 1 0 auto; }
        .footer { flex-shrink: 0; }
        .profile-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #d9d9d9;">
    <!-- Preloader -->
    <div id="preloader" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:2000;background:#fff;display:flex;align-items:center;justify-content:center;transition:opacity 0.3s;">
        <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100">
            <img src="Images/logo.png" alt="Logo" style="height:70px;width:auto;margin-bottom:28px;display:block;">
            <div class="text-primary fs-5 fw-semibold">Loading...</div>
        </div>
    </div>
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2 fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="Images/logo.png" alt="Logo" style="height:40px; width:auto; margin-right:10px;">
                <span class="fw-bold">PharmaSync</span>
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary d-flex align-items-center dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}" alt="Profile" class="profile-img me-2">
                    <span>{{ Auth::user()->name ?? 'User' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-3" aria-labelledby="profileDropdown" style="min-width: 220px;">
                    <li class="text-center mb-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}" alt="Profile" class="profile-img mb-2">
                        <div class="fw-semibold">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-muted small">Role: <span class="fw-semibold">{{ Auth::user()->role ?? '-' }}</span></div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="get" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>



    <!-- Main Content -->
    <main class="main-content container my-4">
        @yield('main')
    </main>

    <!-- Footer -->
    <footer class="footer bg-light text-center py-3 mt-auto border-top">
        <div class="container">
            <span class="text-muted">&copy; {{ date('Y') }} PharmaSync. All rights reserved.</span>
        </div>
    </footer>

</main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS CDN -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        // Preloader hide on page load
        window.addEventListener('load', function() {
            var preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 300);
            }
        });
</script>
@yield('scripts')
</body>
</html>
