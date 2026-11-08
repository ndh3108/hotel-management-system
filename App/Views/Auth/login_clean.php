<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dang Nhap | Booking Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --error: #ef4444;
            --success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            background-image:
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .logo h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .logo p {
            color: var(--text-muted);
            font-size: 15px;
            margin-top: 8px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fef2f2;
            color: var(--error);
            border: 1px solid #fee2e2;
        }

        .alert-success {
            background: #f0fdf4;
            color: var(--success);
            border: 1px solid #dcfce7;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            background: var(--white);
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            color: var(--text-main);
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .forgot-pass {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }

        .register-link {
            text-align: center;
            margin-top: 32px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <?php
    $error = $_GET['error'] ?? null;
    $success = $_GET['success'] ?? null;
    $email = $_GET['email'] ?? '';
    $errorMessage = null;

    if ($error === 'empty') {
        $errorMessage = 'Vui long nhap day du email va mat khau.';
    } elseif ($error === 'invalid') {
        $errorMessage = 'Email hoac mat khau khong dung. Vui long kiem tra lai.';
    } elseif ($error === 'locked') {
        $errorMessage = 'Tai khoan tam thoi bi khoa do nhap sai nhieu lan. Vui long thu lai sau.';
    }

    $successMessage = null;
    if ($success === 'registered') {
        $successMessage = 'Dang ky thanh cong. Vui long dang nhap.';
    } elseif ($success === 'password_reset') {
        $successMessage = 'Dat lai mat khau thanh cong. Vui long dang nhap.';
    }
    ?>

    <div class="login-card">
        <div class="logo">
            <i class="fas fa-hotel"></i>
            <h1>Chao mung tro lai</h1>
            <p>Vui long dang nhap vao tai khoan cua ban</p>
        </div>

        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage !== null): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= \App\Core\url('/login') ?>">
            <div class="form-group">
                <label for="email">Dia chi Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com" value="<?= htmlspecialchars($email) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mat khau</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <div class="options">
                <label class="remember">
                    <input type="checkbox" name="remember"> Ghi nho dang nhap
                </label>
                <a href="<?= \App\Core\url('/forgot-password') ?>" class="forgot-pass">Quen mat khau?</a>
            </div>

            <button type="submit" class="btn-submit">
                Dang Nhap <i class="fas fa-arrow-right"></i>
            </button>

            <div class="register-link">
                Ban chua co tai khoan? <a href="<?= \App\Core\url('/register') ?>">Dang ky ngay</a>
            </div>
        </form>
    </div>
</body>
</html>

