<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Booking Hotel</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
        }

        .header {
            background: #333;
            color: #fff;
            padding: 10px 20px;
        }

        .wrap {
            display: flex;
            min-height: 80vh;
        }

        .sidebar {
            width: 200px;
            background: #eee;
            padding: 15px;
        }

        .sidebar a {
            display: block;
            padding: 8px 0;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            text-decoration: none;
            background: #333;
            color: #fff;
            border-radius: 4px;
        }

        .btn:hover {
            background: #555;
        }

        .btn-small {
            font-size: 12px;
            padding: 4px 8px;
        }

        form label {
            display: inline-block;
            min-width: 120px;
        }

        form input,
        form select,
        form textarea {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">Admin | <a href="admin.php?page=auth&action=logout" style="color:#fff">Đăng xuất</a></div>
