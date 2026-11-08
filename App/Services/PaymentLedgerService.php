<?php

namespace App\Services;

use App\Core\Database;
use App\Models\PaymentTransaction;
use PDO;

class PaymentLedgerService
{
    private PDO $pdo;
    private PaymentTransaction $paymentTransactionModel;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->paymentTransactionModel = new PaymentTransaction();
    }

    public function writeTransaction(
        int $invoiceId,
        string $gateway,
        string $externalTxnId,
        float $amount,
        string $status,
        array $payload
    ): array {
        $existing = $this->paymentTransactionModel->findByGatewayTxn($gateway, $externalTxnId);
        if ($existing) {
            return ['idempotent' => true, 'transaction' => $existing];
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO payment_transactions
            (invoice_id, gateway, external_txn_id, amount, status, paid_at, raw_payload)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)
        ");
        $stmt->execute([
            $invoiceId,
            $gateway,
            $externalTxnId,
            $amount,
            $status,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
        return [
            'idempotent' => false,
            'transaction' => [
                'id' => (int) $this->pdo->lastInsertId(),
                'invoice_id' => $invoiceId,
                'gateway' => $gateway,
                'external_txn_id' => $externalTxnId,
                'amount' => $amount,
                'status' => $status,
            ],
        ];
    }
}

