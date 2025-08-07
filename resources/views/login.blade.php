<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            background-color: #f8f9fa; /* Light gray background */
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
        <div class="card p-4">
            <h3 class="text-center mb-4" id="form-title">Login</h3>
            
            <form id="login-form">
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="loginEmail" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
            </form>

            <form id="register-form" class="d-none">
                <div class="mb-3">
                    <label for="registerUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="registerUsername" required>
                </div>
                <div class="mb-3">
                    <label for="registerDepartment" class="form-label">Department</label>
                    <select class="form-control" id="registerDepartment" required>
                        <option value="">Select Department</option>
                        <option value="pharmacy">Pharmacy</option>
                        <option value="sales">Sales</option>
                        <option value="inventory">Inventory</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="registerEmail" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="registerPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="registerPassword" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" required>
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

        // Registration form validation
        document.getElementById('register-form').addEventListener('submit', function(e) {
            let valid = true;
            let messages = [];

            const username = document.getElementById('registerUsername').value.trim();
            const department = document.getElementById('registerDepartment').value;
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (username.length < 3) {
                valid = false;
                messages.push('Username must be at least 3 characters.');
            }

            if (!department) {
                valid = false;
                messages.push('Please select a department.');
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                valid = false;
                messages.push('Please enter a valid email address.');
            }

            if (password.length < 6) {
                valid = false;
                messages.push('Password must be at least 6 characters.');
            }

            if (password !== confirmPassword) {
                valid = false;
                messages.push('Passwords do not match.');
            }

            if (!valid) {
                e.preventDefault();
                alert(messages.join('\n'));
            }
        });
    </script>
</body>
</html>