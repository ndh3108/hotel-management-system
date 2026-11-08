<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Addon;
use App\Models\Room;
use App\Models\RoomType;

class RoomController extends Controller
{
    private RoomType $roomTypeModel;
    private Room $roomModel;
    private Addon $serviceModel;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = BASE_PATH . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Views';
        $this->roomTypeModel = new RoomType();
        $this->roomModel = new Room();
        $this->serviceModel = new Addon();
    }

    public function index(): void
    {
        $cursor = (int) $this->input('cursor', 0);
        $pageNumber = max(1, (int) $this->input('page', 1));
        
        $pageData = $this->roomTypeModel->getPaginatedWithImages($cursor, 9);
        $addons = $this->serviceModel->getAddons();

        $this->useLayout = true;
        $this->render('Room/types', [
            'roomTypes' => $pageData['items'],
            'hasMore' => $pageData['has_more'],
            'nextCursor' => $pageData['next_last_id'] ?? null,
            'pageNumber' => $pageNumber,
            'addons' => $addons,
            'title' => 'Danh sách phòng nghỉ - Booking Hotel',
        ]);
    }

    public function search(): void
    {
        $checkIn = $this->input('check_in');
        $checkOut = $this->input('check_out');
        $roomTypeId = $this->input('room_type_id') !== null && $this->input('room_type_id') !== '' ? (int) $this->input('room_type_id') : null;

        $roomTypes = $this->roomTypeModel->getAll();
        $rooms = [];
        $roomTypeCounts = [];

        if ($checkIn && $checkOut) {
            $checkIn = date('Y-m-d', strtotime($checkIn));
            $checkOut = date('Y-m-d', strtotime($checkOut));
            if ($checkIn < $checkOut) {
                $rooms = $this->roomModel->getAvailableInRange($checkIn, $checkOut, $roomTypeId);

                foreach ($rooms as $room) {
                    $typeId = $room['room_type_id'];
                    $roomTypeCounts[$typeId] = ($roomTypeCounts[$typeId] ?? 0) + 1;
                }
            }
        }

        $this->useLayout = true;
        $this->render('Room/search', [
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
            'roomTypeCounts' => $roomTypeCounts,
            'checkIn' => $checkIn ?? '',
            'checkOut' => $checkOut ?? '',
            'roomTypeId' => $roomTypeId,
            'title' => 'Tìm kiếm phòng - Booking Hotel',
        ]);
    }

    public function about()
    {
        $this->render('Room/about');
    }

    public function detail(int $id): void
    {
        $room = $this->roomModel->findById($id);
        if (!$room) {
            $this->redirect(\App\Core\url('/rooms'));
            return;
        }

        $roomType = $this->roomTypeModel->getFullDetail($room['room_type_id'] ?? 0);
        $checkIn = $this->input('check_in') ?? '';
        $checkOut = $this->input('check_out') ?? '';

        $this->useLayout = true;
        $this->render('Room/detail', [
            'room' => $room,
            'roomType' => $roomType,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'title' => 'Chi tiết phòng ' . ($room['room_number'] ?? ''),
        ]);
    }

    public function showType(?int $id = null): void
    {
        $id = $id ?? (int) $this->input('id', 0);
        if ($id <= 0) {
            $id = (int) $this->input('room_type_id', 0);
        }

        if ($id <= 0) {
            $this->redirect(\App\Core\url('/rooms'));
            return;
        }

        $roomType = $this->roomTypeModel->getFullDetail($id);
        if (!$roomType) {
            $this->redirect(\App\Core\url('/rooms'));
            return;
        }

        $reviewService = new \App\Services\ReviewService();
        $reviews = $reviewService->getReviewsByRoomTypeId($id);
        $avgRating = $reviewService->getAverageRating($id);
        $reviewCount = count($reviews);

        $userId = \App\Core\Auth::user()['id'] ?? null;
        $reviewEligibility = null;
        $canReview = false;

        if ($userId) {
            $reviewEligibility = $reviewService->getReviewableBookingForRoomType((int) $userId, $id);
            $canReview = $reviewEligibility !== null;
        }

        // Inline Availability Logic
        $checkIn = $this->input('check_in');
        $checkOut = $this->input('check_out');
        $availableRooms = [];
        $searchPerformed = false;

        if ($checkIn && $checkOut) {
            $searchPerformed = true;
            $checkInDate = date('Y-m-d', strtotime($checkIn));
            $checkOutDate = date('Y-m-d', strtotime($checkOut));
            if ($checkInDate < $checkOutDate) {
                $availableRooms = $this->roomModel->getAvailableInRange($checkInDate, $checkOutDate, $id);
            }
        }

        $this->useLayout = true;
        $this->render('Room/type-detail', [
            'roomType' => $roomType,
            'reviews' => $reviews,
            'avgRating' => $avgRating,
            'reviewCount' => $reviewCount,
            'canReview' => $canReview,
            'defaultRoomId' => (int) ($reviewEligibility['room_id'] ?? 0),
            'defaultBookingId' => (int) ($reviewEligibility['booking_id'] ?? 0),
            'availableRooms' => $availableRooms,
            'searchPerformed' => $searchPerformed,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'title' => $roomType['name'] . ' - Booking Hotel',
        ]);
    }
}

