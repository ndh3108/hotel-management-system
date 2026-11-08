<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
        .card { background:#fff; width:100%; max-width:440px; border-radius:20px; padding:32px; box-shadow:0 20px 40px rgba(15,23,42,.08); }
        h1 { font-size:24px; margin-bottom:8px; }
        p { color:#64748b; margin-bottom:24px; }
        input { width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:12px; margin-bottom:16px; }
        button { width:100%; padding:13px; background:#4f46e5; color:#fff; border:none; border-radius:12px; font-weight:600; cursor:pointer; }
        .alert { padding:12px 14px; border-radius:12px; margin-bottom:16px; }
        .ok { background:#ecfdf5; color:#065f46; }
        .err { background:#fef2f2; color:#991b1b; }
        a { display:inline-block; margin-top:16px; color:#4f46e5; text-decoration:none; }
        code { background:#f1f5f9; padding:2px 6px; border-radius:6px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Quên mật khẩu</h1>
        <p>Nhập email để tạo yêu cầu đặt lại mật khẩu.</p>

        <?php if (($_GET['error'] ?? '') === 'empty'): ?>
            <div class="alert err">Vui lòng nhập email.</div>
        <?php endif; ?>

        <?php if (($_GET['success'] ?? '') === '1'): ?>
            <div class="alert ok">
                Yêu cầu đã được ghi nhận.
                <?php $resetLink = \App\Core\Session::getFlash('_password_reset_link'); ?>
                <?php if (!empty($resetLink)): ?>
                    <div style="margin-top:8px;">Link dev: <code><?= htmlspecialchars((string) $resetLink) ?></code></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= \App\Core\url('/forgot-password') ?>">
            <?= csrf_field() ?>
            <input type="email" name="email" placeholder="example@email.com" required>
            <button type="submit">Tạo link đặt lại mật khẩu</button>
        </form>

        <a href="<?= \App\Core\url('/login') ?>">Quay lại đăng nhập</a>
    </div>
</body>
</html>


