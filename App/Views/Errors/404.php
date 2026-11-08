<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 72px;
            color: #333;
            margin: 0;
        }

        p {
            font-size: 18px;
            color: #666;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <h1>404</h1>
    <p>Trang bạn tìm kiếm không tồn tại.</p>
    <p><a href="<?= \App\Core\url('/') ?>">Về trang chủ</a></p>
    <?php if (defined('APP_DEBUG') && APP_DEBUG && !empty($message)): ?>
        <p style="color: #999; font-size: 12px;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</body>

</html>
