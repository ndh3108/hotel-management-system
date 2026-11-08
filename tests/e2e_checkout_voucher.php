<?php

// End-to-end scenario: create booking (with voucher + optional points), checkout, verify invoice and PDF.
// Run:
//   php tests/e2e_checkout_voucher.php

use App\Core\Database;
use App\Services\BookingService;
use App\Services\CheckoutService;
use App\Services\PdfInvoiceService;

require __DIR__ . '/../Public/bootstrap.php';

$pdo = Database::getInstance()->getConnection();
$bookingService = new BookingService();
$checkoutService = new CheckoutService();
$pdfService = new PdfInvoiceService();

// Adjust to match an existing user, room and active voucher in your DB.
$testUserId = 1;
$testRoomId = 1;
$testVoucherId = null; // e.g. 1 if you have voucher id=1 active
$pointsToRedeem = 0;

echo "=== E2E: booking -> checkout -> invoice/PDF ===\n";

// 1) Create booking via service (single room)
$createResult = $bookingService->createBookingFromRequest([
    'user_id' => $testUserId,
    'room_id' => $testRoomId,
    'check_in' => '2026-05-01 12:00',
    'check_out' => '2026-05-03 12:00',
    'voucher_id' => $testVoucherId,
    'points_to_redeem' => $pointsToRedeem,
    'addons' => [],
]);

if (!($createResult['success'] ?? false)) {
    echo "[FAIL] Create booking: " . ($createResult['error'] ?? 'unknown') . PHP_EOL;
    exit(1);
}

$bookingId = (int)$createResult['booking_id'];
echo "[OK] Booking created with ID={$bookingId}\n";

// 2) Checkout booking (will create invoice, update status, update room + loyalty)
$checkoutResult = $checkoutService->checkoutBooking($bookingId);
if (!($checkoutResult['success'] ?? false)) {
    echo "[FAIL] Checkout: " . ($checkoutResult['error'] ?? 'unknown') . PHP_EOL;
    exit(1);
}

$invoiceId = (int)($checkoutResult['invoice_id'] ?? 0);
$idempotent = (bool)($checkoutResult['idempotent'] ?? false);
echo "[OK] Checkout done. Invoice ID={$invoiceId}, idempotent=" . ($idempotent ? 'true' : 'false') . PHP_EOL;

// 3) Check invoice data from DB
$stmtInv = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
$stmtInv->execute([$invoiceId]);
$invoice = $stmtInv->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    echo "[FAIL] Invoice not found in DB\n";
    exit(1);
}

echo "[OK] Invoice loaded. final_total=" . ($invoice['final_total'] ?? 0) . " status=" . ($invoice['status'] ?? '') . PHP_EOL;

// 4) Render PDF (will stream HTML if library is missing)
echo "[INFO] Rendering invoice PDF/HTML (check browser if running via web; here we just ensure no exception).\n";
try {
    // For CLI, we don't actually send headers; just ensure method can run without fatal.
    ob_start();
    $pdfService->streamByBookingId($bookingId);
    ob_end_clean();
    echo "[OK] PdfInvoiceService::streamByBookingId executed without fatal error.\n";
} catch (Throwable $e) {
    echo "[FAIL] PDF render failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo "=== DONE: E2E scenario completed successfully ===\n";


