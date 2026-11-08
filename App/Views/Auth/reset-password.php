<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
        .card { background:#fff; width:100%; max-width:440px; border-radius:20px; padding:32px; box-shadow:0 20px 40px rgba(15,23,42,.08); }
        h1 { font-size:24px; margin-bottom:8px; }
        p { color:#64748b; margin-bottom:24px; }
        input { width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:12px; margin-bottom:16px; }
        button { width:100%; padding:13px; background:#4f46e5; color:#fff; border:none; border-radius:12px; font-weight:600; cursor:pointer; }
        .alert { padding:12px 14px; border-radius:12px; margin-bottom:16px; }
        .err { background:#fef2f2; color:#991b1b; }
        a { display:inline-block; margin-top:16px; color:#4f46e5; text-decoration:none; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Đặt lại mật khẩu</h1>
        <p>Nhập mật khẩu mới cho tài khoản của bạn.</p>

        <?php $error = $_GET['error'] ?? ''; ?>
        <?php if ($error !== ''): ?>
            <div class="alert err">
                <?php
                $msg = 'Yêu cầu không hợp lệ.';
                if ($error === 'empty') $msg = 'Vui lòng nhập đầy đủ thông tin.';
                if ($error === 'mismatch') $msg = 'Mật khẩu xác nhận không khớp.';
                if ($error === 'invalid') $msg = 'Token không hợp lệ hoặc đã hết hạn.';
                echo htmlspecialchars($msg);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= \App\Core\url('/reset-password') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars((string) ($token ?? '')) ?>">
            <input type="password" name="password" placeholder="Mật khẩu mới" required>
            <input type="password" name="password_confirm" placeholder="Xác nhận mật khẩu mới" required>
            <button type="submit">Cập nhật mật khẩu</button>
        </form>

        <a href="<?= \App\Core\url('/login') ?>">Quay lại đăng nhập</a>
    </div>
</body>
</html>


