<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Login') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px 32px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        .login-logo {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: white;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
        }
        .login-title {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-title h2 {
            font-size: 24px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: -0.5px;
        }
        .login-title p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }
        .form-label { font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 6px; }
        .form-control {
            padding: 12px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.2s;
            min-height: 48px;
        }
        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 10px;
            min-height: 48px;
            transition: all 0.2s;
        }
        .btn-login:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35); }
        .alert { border-radius: 10px; border: none; padding: 12px 16px; font-size: 14px; }
        .login-footer { text-align: center; margin-top: 24px; font-size: 13px; color: #9ca3af; }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
        }
        .input-group .form-control { padding-left: 40px; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo"><i class="fas fa-cash-register"></i></div>
            <div class="login-title">
                <h2><?= e(setting('store_name', 'POS System')) ?></h2>
                <p>Sign in to your account</p>
            </div>

            <?php if ($error = getFlash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout'])): ?>
            <div class="alert alert-warning"><i class="fas fa-clock me-2"></i>Session expired. Please login again.</div>
            <?php endif; ?>

            <form action="<?= url('authenticate') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="position-relative">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control" name="username" placeholder="Enter username" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" required id="passwordField">
                        <button type="button" class="btn btn-ghost btn-sm position-absolute" style="right:8px;top:50%;transform:translateY(-50%);min-height:auto;padding:8px;" onclick="togglePwd()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="login-footer">Default: admin / admin123</div>
        </div>
    </div>
    <script>
    function togglePwd() {
        const f = document.getElementById('passwordField');
        const i = document.getElementById('toggleIcon');
        if (f.type === 'password') { f.type = 'text'; i.classList.replace('fa-eye', 'fa-eye-slash'); }
        else { f.type = 'password'; i.classList.replace('fa-eye-slash', 'fa-eye'); }
    }
    </script>
</body>
</html>
