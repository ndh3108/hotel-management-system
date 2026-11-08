<?php

namespace App\Core;

class StatusLabel
{
    public static function booking(string $status): array
    {
        $key = strtolower(trim($status));
        $map = [
            'draft' => ['label' => 'Draft', 'badge' => 'badge-secondary'],
            'pending_payment' => ['label' => 'Pending payment', 'badge' => 'badge-warning'],
            'confirmed' => ['label' => 'Confirmed', 'badge' => 'badge-info'],
            'checked_in' => ['label' => 'Checked in', 'badge' => 'badge-primary'],
            'checked_out' => ['label' => 'Checked out', 'badge' => 'badge-info'],
            'completed' => ['label' => 'Completed', 'badge' => 'badge-success'],
            'cancelled' => ['label' => 'Cancelled', 'badge' => 'badge-danger'],
            'expired' => ['label' => 'Expired', 'badge' => 'badge-dark'],
            'no_show' => ['label' => 'No show', 'badge' => 'badge-secondary'],
            // legacy compatibility
            'pending' => ['label' => 'Pending payment', 'badge' => 'badge-warning'],
            'processing' => ['label' => 'Pending payment', 'badge' => 'badge-warning'],
            'paid' => ['label' => 'Confirmed', 'badge' => 'badge-info'],
        ];
        return $map[$key] ?? ['label' => ucfirst(str_replace('_', ' ', $key)), 'badge' => 'badge-secondary'];
    }

    public static function payment(string $status): array
    {
        $key = strtolower(trim($status));
        $map = [
            'unpaid' => ['label' => 'Unpaid', 'badge' => 'badge-secondary'],
            'deposit_pending' => ['label' => 'Deposit pending', 'badge' => 'badge-warning'],
            'partial_paid' => ['label' => 'Partial paid', 'badge' => 'badge-info'],
            'paid' => ['label' => 'Paid', 'badge' => 'badge-success'],
            'overdue' => ['label' => 'Overdue', 'badge' => 'badge-danger'],
            'refunding' => ['label' => 'Refunding', 'badge' => 'badge-warning'],
            'refunded' => ['label' => 'Refunded', 'badge' => 'badge-info'],
            'failed' => ['label' => 'Failed', 'badge' => 'badge-danger'],
        ];
        return $map[$key] ?? ['label' => ucfirst(str_replace('_', ' ', $key)), 'badge' => 'badge-secondary'];
    }

    public static function serviceBooking(string $status): array
    {
        $key = strtolower(trim($status));
        $map = [
            'requested' => ['label' => 'Requested', 'badge' => 'badge-warning'],
            'reserved' => ['label' => 'Reserved', 'badge' => 'badge-primary'],
            'active' => ['label' => 'Active', 'badge' => 'badge-info'],
            'completed' => ['label' => 'Completed', 'badge' => 'badge-success'],
            'cancelled' => ['label' => 'Cancelled', 'badge' => 'badge-danger'],
            'expired' => ['label' => 'Expired', 'badge' => 'badge-secondary'],
        ];
        return $map[$key] ?? ['label' => ucfirst(str_replace('_', ' ', $key)), 'badge' => 'badge-secondary'];
    }
}

