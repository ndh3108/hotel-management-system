<?php

namespace App\Models;

use App\Core\Model;

/**
 * Notification Model
 * Represents in-app notifications for booking status changes
 */
class Notification extends Model
{
    protected string $table = 'notifications';
    protected array $fillable = [
        'user_id',
        'booking_id',
        'type',
        'subject',
        'message',
        'old_status',
        'new_status',
        'is_read',
        'read_at'
    ];
    protected bool $timestamps = true;

    /**
     * Get all notifications for a user
     */
    public function getByUserId($userId, $limit = 50, $offset = 0)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get() ?? [];
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadByUserId($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->get() ?? [];
    }

    /**
     * Get unread notification count for a user
     */
    public function countUnreadByUserId($userId)
    {
        $notifications = $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->get() ?? [];
        return count($notifications);
    }

    /**
     * Get notifications for a booking
     */
    public function getByBookingId($bookingId)
    {
        return $this->where('booking_id', $bookingId)
            ->orderBy('created_at', 'DESC')
            ->get() ?? [];
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark multiple notifications as read for a user
     */
    public function markUserNotificationsAsRead($userId)
    {
        $notifications = $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->get() ?? [];

        foreach ($notifications as $notif) {
            $this->markAsRead($notif['id']);
        }
        return true;
    }

    /**
     * Delete old notifications (keeps last 90 days)
     */
    public function deleteOldNotifications($daysOld = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        $notifications = $this->getAll();

        foreach ($notifications as $notif) {
            if (strtotime($notif['created_at'] ?? 'now') < strtotime($cutoffDate)) {
                $this->delete($notif['id']);
            }
        }
        return true;
    }

    /**
     * Get notification details by ID
     */
    public function getDetailById($notificationId)
    {
        return $this->findById($notificationId);
    }

    /**
     * Create notification record
     */
    public function create($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        return parent::create($data);
    }

    /**
     * Relationship: User
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    /**
     * Relationship: Booking
     */
    public function booking()
    {
        return $this->belongsTo('Booking', 'booking_id');
    }
}

