<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Booking Hotel') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= \App\Core\asset('css/style.css') ?>">
    <style>
        .list-load-more {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        .list-load-more a {
            min-width: 180px;
            text-align: center;
        }

        .responsive-table {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .pagination-nav {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination-link,
        .pagination-ellipsis {
            min-width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            background: var(--white);
            color: var(--dark);
            font-weight: 600;
            text-decoration: none;
        }

        .pagination-link.is-active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .pagination-ellipsis {
            border-style: dashed;
            color: var(--text-light);
        }

        @media (max-width: 1024px) {
            .container {
                width: min(100% - 32px, 1200px);
            }
        }

        @media (max-width: 768px) {
            main {
                overflow: hidden;
            }

            .container {
                width: min(100% - 24px, 1200px);
            }

            .section-header {
                margin-bottom: 28px;
            }

            .section-title {
                font-size: clamp(1.9rem, 6vw, 2.6rem);
            }

            .section-desc {
                font-size: 0.95rem;
                padding: 0 6px;
            }

            .rooms-grid {
                grid-template-columns: 1fr !important;
                gap: 18px !important;
            }

            .room-card,
            .service-card,
            .booking-card {
                border-radius: 18px;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main>
        <?= $content ?> 
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function () {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.remove('transparent');
            } else {
                header.classList.add('transparent');
            }
        });

        // Initialize header as transparent on hero pages
        document.addEventListener('DOMContentLoaded', function () {
            const hero = document.querySelector('.hero');
            if (hero) {
                document.getElementById('header').classList.add('transparent');
            }

            const navToggle = document.querySelector('[data-nav-toggle]');
            const nav = document.querySelector('[data-main-nav]');
            if (navToggle && nav) {
                navToggle.addEventListener('click', function () {
                    nav.classList.toggle('is-open');
                });
            }
        });
    </script>
</body>

</html>

