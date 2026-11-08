<?php

namespace App\Domain\Services;

class RoomStateService
{
    private const STATUS_AVAILABLE = 'Available';
    private const STATUS_HOLD = 'Hold';
    private const STATUS_OCCUPIED = 'Occupied';
    private const STATUS_CLEANING = 'Cleaning';
    private const STATUS_MAINTENANCE = 'Maintenance';

    public const ROLE_MANAGER = 'Manager';
    public const ROLE_RECEPTIONIST = 'Receptionist';
    public const ROLE_HOUSEKEEPING = 'Housekeeping';

    public function getValidStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE,
            self::STATUS_HOLD,
            self::STATUS_OCCUPIED,
            self::STATUS_CLEANING,
            self::STATUS_MAINTENANCE
        ];
    }

    public function canTransition(string $currentStatus, string $newStatus, string $userRole): bool
    {
        if ($currentStatus === $newStatus) {
            return true; // No change
        }
        $allowed = $this->getAllowedTransitions($currentStatus, $userRole);
        return in_array($newStatus, $allowed, true);
    }

    public function getAllowedTransitions(string $currentStatus, string $userRole): array
    {
        if ($userRole === self::ROLE_MANAGER) {
            return match ($currentStatus) {
                self::STATUS_AVAILABLE => [self::STATUS_HOLD, self::STATUS_OCCUPIED, self::STATUS_MAINTENANCE, self::STATUS_CLEANING],
                self::STATUS_HOLD => [self::STATUS_OCCUPIED, self::STATUS_AVAILABLE],
                self::STATUS_OCCUPIED => [self::STATUS_CLEANING, self::STATUS_AVAILABLE],
                self::STATUS_CLEANING => [self::STATUS_AVAILABLE, self::STATUS_MAINTENANCE],
                self::STATUS_MAINTENANCE => [self::STATUS_AVAILABLE, self::STATUS_CLEANING],
                default => []
            };
        }

        if ($userRole === self::ROLE_RECEPTIONIST) {
            return match ($currentStatus) {
                self::STATUS_AVAILABLE => [self::STATUS_HOLD, self::STATUS_OCCUPIED],
                self::STATUS_HOLD => [self::STATUS_OCCUPIED, self::STATUS_AVAILABLE],
                self::STATUS_OCCUPIED => [self::STATUS_CLEANING],
                default => []
            };
        }

        if ($userRole === self::ROLE_HOUSEKEEPING) {
            return match ($currentStatus) {
                self::STATUS_CLEANING => [self::STATUS_AVAILABLE],
                default => []
            };
        }

        return [];
    }
}

