<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link rel="preload" href="{{ asset('Images/logo.png') }}" as="image">
    <link rel="icon" type="image/x-icon" href="{{ asset('Images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-logo {
            display: block;
            margin: 0 auto 18px auto;
            height: 60px;
            width: auto;
        }
    </style>
    <style>
        body, html {
            height: 100%;
            background-color: #f8f9fa;
        }
        .container-fluid {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .card {
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div aria-live="polite" aria-atomic="true" class="position-relative">
            <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
                @if(session('success'))
                    <div class="toast align-items-center bg-success text-white show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                        <div class="d-flex">
                            <div class="toast-body">{{ session('success') }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="toast align-items-center bg-danger text-white show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                        <div class="d-flex">
                            <div class="toast-body">{{ session('error') }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="toast align-items-center bg-danger text-white show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                        <div class="d-flex">
                            <div class="toast-body">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
    <script>
       
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.forEach(function(toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });

           
            @if ($errors->any() && old('name'))
                document.getElementById('login-form').classList.add('d-none');
                document.getElementById('register-form').classList.remove('d-none');
                document.getElementById('form-title').textContent = 'Register';
                document.getElementById('toggle-form').textContent = "Already have an account? Login here.";
            @endif
        });
    </script>
                @endif
            </div>
        </div>
        <div class="card p-4">
            <img src="Images/logo.png" alt="Logo" class="login-logo">
            <h3 class="text-center mb-4" id="form-title">Login</h3>
            
            <form id="login-form" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="loginEmail" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="loginPassword" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
            </form>

            <form id="register-form" class="d-none" action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="registerUsername" class="form-label">Username</label>
                    <input type="text" name="name" class="form-control" id="registerUsername" required>
                </div>
                <div class="mb-3">
                    <label for="registerDepartment" class="form-label">Department</label>
                    <select class="form-control" name="department_id" id="registerDepartment" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>          
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="registerEmail" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="registerPassword" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="registerPassword" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password"  name="confirm_password" class="form-control" id="confirmPassword" required>
                    <div id="password-match-error" class="text-danger small mt-1" style="display:none;"></div>
                </div>
                <button type="submit" class="btn btn-success w-100 mb-2">Register</button>
            </form>
            
            <p class="text-center mt-3">
                <a href="#" style="text-decoration: none;" id="toggle-form">Don't have an account? Register here.</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            toastElList.forEach(function(toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });

        
            var registerForm = document.getElementById('register-form');
            var passwordInput = document.getElementById('registerPassword');
            var confirmPasswordInput = document.getElementById('confirmPassword');
            var errorDiv = document.getElementById('password-match-error');
            function checkPasswordMatch() {
                var password = passwordInput.value;
                var confirmPassword = confirmPasswordInput.value;
                if (password && confirmPassword && password !== confirmPassword) {
                    errorDiv.textContent = 'Passwords do not match.';
                    errorDiv.style.display = 'block';
                } else {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            }
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    var password = passwordInput.value;
                    var confirmPassword = confirmPasswordInput.value;
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        errorDiv.textContent = 'Passwords do not match.';
                        errorDiv.style.display = 'block';
                    }
                });
                passwordInput.addEventListener('input', checkPasswordMatch);
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            }
        });
    </script>
    <script>
        document.getElementById('toggle-form').addEventListener('click', function(e) {
            e.preventDefault();
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const formTitle = document.getElementById('form-title');
            const toggleLink = document.getElementById('toggle-form');

            if (loginForm.classList.contains('d-none')) {
                loginForm.classList.remove('d-none');
                registerForm.classList.add('d-none');
                formTitle.textContent = 'Login';
                toggleLink.textContent = "Don't have an account? Register here.";
            } else {
                loginForm.classList.add('d-none');
                registerForm.classList.remove('d-none');
                formTitle.textContent = 'Register';
                toggleLink.textContent = "Already have an account? Login here.";
            }
        });
    </script>
</body>
</html>