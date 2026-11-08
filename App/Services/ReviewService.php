<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{
    private Review $reviewModel;
    private BookingService $bookingService;

    public function __construct()
    {
        $this->reviewModel = new Review();
        $this->bookingService = new BookingService();
    }

    public function getReviewsByRoomTypeId(int $roomTypeId): array
    {
        return $this->reviewModel->getByRoomTypeId($roomTypeId);
    }

    public function getAverageRating(int $roomTypeId): float
    {
        return $this->reviewModel->getAverageByRoomTypeId($roomTypeId);
    }

    public function createReview(array $data): array
    {
        if (empty($data['user_id'])) {
            return ['success' => false, 'error' => 'unauthorized'];
        }

        if (empty($data['room_id'])) {
            return ['success' => false, 'error' => 'room_required'];
        }

        if (!isset($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            return ['success' => false, 'error' => 'invalid_rating'];
        }

        $content = trim((string) ($data['content'] ?? $data['comment'] ?? ''));
        if ($content !== '' && mb_strlen($content) > 1200) {
            return ['success' => false, 'error' => 'comment_too_long'];
        }

        // Chống spam: mỗi user chỉ gửi tối đa 1 review mỗi 60 giây.
        if ($this->reviewModel->hasRecentByUser((int) $data['user_id'], 60)) {
            return ['success' => false, 'error' => 'review_spam_limited'];
        }

        $room = (new \App\Models\Room())->findById((int) $data['room_id']);
        if (!$room || empty($room['room_type_id'])) {
            return ['success' => false, 'error' => 'room_invalid'];
        }

        $roomTypeId = (int) $room['room_type_id'];
        $eligibleBooking = $this->getReviewableBookingForRoomType((int) $data['user_id'], $roomTypeId);

        if (!$eligibleBooking) {
            return ['success' => false, 'error' => 'review_not_allowed'];
        }

        $submitToken = $data['submit_token'] ?? null;
        if ($submitToken && $this->reviewModel->hasSubmitToken($submitToken)) {
            return ['success' => false, 'error' => 'duplicate_submission'];
        }

        // Chỉ review trong cửa sổ cho phép (mặc định 30 ngày sau check-out).
        $checkOutDate = $eligibleBooking['check_out_date'] ?? null;
        if (!$checkOutDate) {
            return ['success' => false, 'error' => 'review_not_allowed'];
        }
        $checkoutTs = strtotime((string) $checkOutDate);
        if ($checkoutTs === false || $checkoutTs > time()) {
            return ['success' => false, 'error' => 'review_not_allowed'];
        }
        if ($checkoutTs < strtotime('-30 days')) {
            return ['success' => false, 'error' => 'review_window_closed'];
        }

        $reviewData = [
            'booking_id' => (int) $eligibleBooking['booking_id'],
            'user_id' => (int) $data['user_id'],
            'rating' => (int) $data['rating'],
            'content' => $content,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        if (!empty($submitToken)) {
            $reviewData['submit_token'] = $submitToken;
        }

        $reviewId = $this->reviewModel->create($reviewData);

        if ($reviewId) {
            return ['success' => true, 'review_id' => $reviewId];
        }

        return ['success' => false, 'error' => 'create_failed'];
    }

    public function getReviewableBookingForRoomType(int $userId, int $roomTypeId): ?array
    {
        return $this->bookingService->getReviewableBookingForRoomType($userId, $roomTypeId);
    }

    public function canUserReviewRoomType(int $userId, int $roomTypeId): bool
    {
        return $this->bookingService->canUserReviewRoomType($userId, $roomTypeId);
    }
}

