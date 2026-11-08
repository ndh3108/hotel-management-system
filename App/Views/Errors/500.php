<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi máy chủ</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 72px;
            color: #d32f2f;
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

        pre {
            text-align: left;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h1>500</h1>
    <p>Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.</p>
    <p><a href="<?= \App\Core\url('/') ?>">Về trang chủ</a></p>
    <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($exception)): ?>
        <pre><?= htmlspecialchars($exception->getMessage() . "\n\n" . $exception->getTraceAsString()) ?></pre>
    <?php endif; ?>
</body>

</html>
