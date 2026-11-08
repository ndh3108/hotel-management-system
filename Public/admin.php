<?php
/**
 * Redirect legacy admin.php to unified admin panel.
 * Tất cả admin dùng /admin qua index.php (Router).
 */
require __DIR__ . '/../bootstrap.php';
header('Location: ' . \App\Core\url('/admin'), true, 302);
exit;

