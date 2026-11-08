<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Services\ReviewService;
use App\Models\Room;
use function App\Core\url;

class ReviewController extends Controller
{
    private ReviewService $reviewService;
    private Room $roomModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->reviewService = new ReviewService();
        $this->roomModel = new Room();
    }

    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect(url('/rooms'));
            return;
        }

        $userId = Auth::user()['id'] ?? null;
        $roomId = (int) $this->input('room_id');
        $roomTypeId = (int) $this->input('room_type_id');
        $rating = (int) $this->input('rating');
        $comment = $this->input('comment') ?? '';

        // Redirect URL
        $redirectUrl = $roomTypeId ? url('/room-types?id=' . $roomTypeId) : url('/rooms');

        // Check auth
        if (!$userId) {
            $this->redirect($redirectUrl . '?error=unauthorized');
            return;
        }

        if ($roomTypeId > 0 && !$this->reviewService->canUserReviewRoomType((int) $userId, $roomTypeId)) {
            $this->redirect($redirectUrl . '?error=review_not_allowed#review-form');
            return;
        }

        $submitToken = (string) $this->input('submit_token');

        // Create review
        $result = $this->reviewService->createReview([
            'user_id' => $userId,
            'room_id' => $roomId,
            'rating' => $rating,
            'comment' => $comment,
            'submit_token' => $submitToken,
        ]);

        if ($result['success']) {
            $this->redirect($redirectUrl . '?review=success#reviews');
        } else {
            $this->redirect($redirectUrl . '?error=' . ($result['error'] ?? 'failed') . '#review-form');
        }
    }
}

