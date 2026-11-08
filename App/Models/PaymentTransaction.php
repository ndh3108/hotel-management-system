<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class PaymentTransaction extends Model
{
    protected string $table = 'payment_transactions';

    protected array $fillable = [
        'invoice_id',
        'gateway',
        'external_txn_id',
        'amount',
        'status',
        'paid_at',
        'raw_payload',
    ];

    public function findByGatewayTxn(string $gateway, string $externalTxnId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM payment_transactions
            WHERE gateway = ? AND external_txn_id = ?
            LIMIT 1
        ");
        $stmt->execute([$gateway, $externalTxnId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function listByInvoiceId(int $invoiceId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM payment_transactions
            WHERE invoice_id = ?
            ORDER BY id DESC
        ");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

