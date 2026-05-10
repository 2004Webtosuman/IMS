<?php
require_once 'includes/functions.php';
if (is_logged_in()) {
    redirect('home.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IMS Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        body { margin: 0; padding: 0; background-color: var(--surface); }
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .login-brand {
            flex: 1;
            background: var(--primary);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
        .brand-logo {
            font-size: 5rem;
            margin-bottom: 2rem;
            color: var(--accent);
            position: relative;
        }
        .brand-content {
            text-align: center;
            position: relative;
        }
        .brand-content h1 {
            font-size: 3.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        .brand-content p {
            font-size: 1.25rem;
            opacity: 0.8;
        }
        .login-form-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background-color: var(--bg);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            margin-bottom: 2rem;
        }
        .login-header h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .form-floating input {
            width: 100%;
            padding: 1.5rem 1rem 0.5rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
        }
        .form-floating label {
            position: absolute;
            top: 0;
            left: 1rem;
            padding: 1rem 0;
            color: var(--text-muted);
            pointer-events: none;
            transition: 0.2s ease all;
        }
        .form-floating input:focus ~ label,
        .form-floating input:not(:placeholder-shown) ~ label {
            top: -5px;
            font-size: 0.75rem;
            color: var(--primary);
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }
        .btn-login:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }
        @media (max-width: 992px) {
            .login-brand { display: none; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-brand">
            <div class="brand-logo"><i class="fas fa-cubes"></i></div>
            <div class="brand-content">
                <h1>IMS Pro</h1>
                <p>Manage your inventory with ease</p>
            </div>
        </div>
        <div class="login-form-container">
            <div class="login-card">
                <div class="login-header">
                    <h2>Welcome Back</h2>
                    <p class="text-muted">Enter your credentials to access your account</p>
                </div>
                
                <?php echo display_flash(); ?>

                <form action="logincheck.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    
                    <div class="form-floating">
                        <input type="text" name="Username" id="Username" placeholder=" " value="<?= e($_COOKIE['username'] ?? '') ?>" required autofocus>
                        <label for="Username">Username</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" name="Password" id="Password" placeholder=" " required>
                        <label for="Password">Password</label>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remember"> <span style="font-size: 0.9rem; color: var(--text-muted);">Remember me</span>
                        </label>
                        <a href="newaccountregistration.php" style="font-size: 0.9rem; color: var(--primary); text-decoration: none; font-weight: 500;">Register</a>
                    </div>
                    
                    <button type="submit" class="btn-login">Sign In</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#Password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>