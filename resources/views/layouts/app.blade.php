<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PharmaSync') }}</title>
    <link rel="preload" href="{{ asset('Images/logo.png') }}" as="image">
    <link rel="icon" type="image/x-icon" href="{{ asset('Images/logo.png') }}">
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
        .profile-img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
        .profile-img-dropdown { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff; /* Or a color that matches your theme */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Ensure it's on top of everything */
            transition: opacity 0.5s ease-out; /* Smooth fade out */
        }

        #preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .preloader-logo {
            width: 100px; /* Adjust size as needed */
            height: auto;
            animation: pulse 1.5s infinite alternate; /* Simple pulse animation */
        }

        @keyframes pulse {
            from { transform: scale(0.9); opacity: 0.7; }
            to { transform: scale(1.1); opacity: 1; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #ecececff;">

    <div id="preloader">
        <img src="{{ asset('Images/logo.png') }}" alt="Loading..." class="preloader-logo">
    </div>

    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2 fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('Images/logo.png') }}" alt="Logo" style="height:40px; width:auto; margin-right:10px;">
                <span class="fw-bold">PharmaSync</span>
            </a>
            <div class="dropdown">
                <button class="btn d-flex align-items-center dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}" alt="Profile" class="profile-img me-1">
                    <span>{{ Auth::user()->name ?? 'User' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2" aria-labelledby="profileDropdown" style="min-width: 150px;">
                    <li class="text-center mb-1">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}" alt="Profile" class="profile-img-dropdown mb-1">
                        <div class="fw-semibold small">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-muted" style="font-size: 0.75em;">Role: <span class="fw-semibold">{{ Auth::user()->role ?? '-' }}</span></div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="get" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 btn-sm">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>




    <main class="main-content container my-4">
        @yield('main')
    </main>

    <footer class="footer bg-light text-center py-3 mt-auto border-top">
        <div class="container">
            <span class="text-muted">&copy; {{ date('Y') }} PharmaSync. All rights reserved.</span>
        </div>
    </footer>

</main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        
</script>
@yield('scripts')
    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.classList.add('hidden');
                // Optional: Remove preloader from DOM after transition
                preloader.addEventListener('transitionend', function() {
                    preloader.remove();
                });
            }
        });
    </script>
</body>
</html>
