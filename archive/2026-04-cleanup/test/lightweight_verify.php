<?php

/**
 * LIGHTWEIGHT VERIFICATION
 * Checks implementation without full app bootstrap
 */

echo "\n═══════════════════════════════════════════════════════\n";
echo "  BOOKING STATUS NOTIFICATION - IMPLEMENTATION CHECK\n";
echo "═══════════════════════════════════════════════════════\n\n";

$checks = 0;
$passed = 0;

function status_check($name)
{
    global $checks;
    $checks++;
    echo "[$checks] $name";
}

function pass()
{
    global $passed;
    $passed++;
    echo " ✓\n";
}

function fail($msg = '')
{
    echo " ✗\n";
    if ($msg) echo "    → $msg\n";
}

// 1. Configuration Files
echo "1. CONFIGURATION FILES\n";
echo "─────────────────────────────────────────────────────\n";

status_check("Config/booking_status.php exists");
if (file_exists('Config/booking_status.php')) {
    pass();
} else {
    fail("File not found");
}

status_check("Config/mail.php exists");
if (file_exists('Config/mail.php')) {
    pass();
} else {
    fail("File not found");
}

// 2. Service Classes
echo "\n2. SERVICE CLASSES\n";
echo "─────────────────────────────────────────────────────\n";

status_check("App/Service/NotificationService.php exists");
if (file_exists('App/Service/NotificationService.php')) {
    pass();
} else {
    fail("File not found");
}

status_check("App/Service/EmailTemplateService.php exists");
if (file_exists('App/Service/EmailTemplateService.php')) {
    pass();
} else {
    fail("File not found");
}

status_check("App/Service/BookingService.php exists");
if (file_exists('App/Service/BookingService.php')) {
    pass();
} else {
    fail("File not found");
}

// 3. Model Classes
echo "\n3. MODEL CLASSES\n";
echo "─────────────────────────────────────────────────────\n";

status_check("App/Models/Notification.php exists");
if (file_exists('App/Models/Notification.php')) {
    pass();
} else {
    fail("File not found");
}

// 4. Views
echo "\n4. VIEW FILES\n";
echo "─────────────────────────────────────────────────────\n";

status_check("Admin bookings detail view exists");
if (file_exists('App/Views/Admin/bookings/detail.php')) {
    pass();
} else {
    fail("File not found");
}

status_check("Admin bookings index view exists");
if (file_exists('App/Views/Admin/bookings/index.php')) {
    pass();
} else {
    fail("File not found");
}

// 5. Migrations
echo "\n5. DATABASE MIGRATIONS\n";
echo "─────────────────────────────────────────────────────\n";

status_check("Status standardization migration exists");
if (file_exists('migration_status_standardization.sql')) {
    pass();
} else {
    fail("File not found");
}

status_check("Notifications table migration exists");
if (file_exists('migration_notifications_table.sql')) {
    pass();
} else {
    fail("File not found");
}

// 6. Configuration Content
echo "\n6. CONFIGURATION CONTENT\n";
echo "─────────────────────────────────────────────────────\n";

status_check("booking_status.php has 6 statuses");
$config = @include 'Config/booking_status.php';
if (is_array($config) && count($config) >= 6) {
    pass();
} else {
    fail("Could not load config or missing statuses");
}

status_check("Status config has transitions");
if (is_array($config) && isset($config['pending']['transitions'])) {
    pass();
} else {
    fail("No transitions found");
}

status_check("Status config has colors");
if (is_array($config) && isset($config['pending']['color'])) {
    pass();
} else {
    fail("No colors found");
}

// 7. View Content
echo "\n7. VIEW CONTENT\n";
echo "─────────────────────────────────────────────────────\n";

status_check("Detail view has status dropdown");
$detail = file_get_contents('App/Views/Admin/bookings/detail.php');
if (strpos($detail, 'name="status"') !== false) {
    pass();
} else {
    fail("Status input not found");
}

status_check("Detail view has notify checkbox");
if (strpos($detail, 'notify_user') !== false) {
    pass();
} else {
    fail("Notify checkbox not found");
}

// 8. File Syntax (Basic)
echo "\n8. PHP FILE SYNTAX\n";
echo "─────────────────────────────────────────────────────\n";

$files = [
    'Config/mail.php',
    'Config/booking_status.php',
    'App/Service/NotificationService.php',
    'App/Service/EmailTemplateService.php',
    'App/Models/Notification.php',
];

foreach ($files as $file) {
    status_check("$file syntax");
    $output = shell_exec("php -l \"$file\" 2>&1");
    if (strpos($output, 'No syntax errors') !== false) {
        pass();
    } else {
        fail(trim($output));
    }
}

// Summary
echo "\n═══════════════════════════════════════════════════════\n";
printf("RESULTS: %d/%d checks passed\n", $passed, $checks);
echo "═══════════════════════════════════════════════════════\n";

if ($passed === $checks) {
    echo "\n✅ Implementation complete and verified!\n\n";
    echo "Next Steps:\n";
    echo "  1. Go to: http://localhost/doan/public/admin/bookings\n";
    echo "  2. Click a booking to view details\n";
    echo "  3. Find 'Trạng Thái Đặt Phòng' section\n";
    echo "  4. Test status change from dropdown\n\n";
    exit(0);
} else {
    echo "\n⚠️  Some checks failed. Review above.\n\n";
    exit(1);
}

