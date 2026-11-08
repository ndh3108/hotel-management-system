<?php $error = $_GET['error'] ?? ''; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Đăng nhập</title>
</head>

<body>
    <h1>Đăng nhập Admin</h1>
    <?php if ($error === 'empty'): ?> <p style="color:red">Vui lòng nhập email và mật khẩu.</p> <?php endif; ?>
    <?php if ($error === 'invalid'): ?> <p style="color:red">Email hoặc mật khẩu sai.</p> <?php endif; ?>
    <form method="post" action="admin.php?page=auth&action=login">
        <p><label>Email: <input type="email" name="email" required></label></p>
        <p><label>Mật khẩu: <input type="password" name="password" required></label></p>
        <p><button type="submit">Đăng nhập</button></p>
    </form>
</body>

</html>
