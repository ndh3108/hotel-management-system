<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Room;
use App\Models\RoomStatusLog;
use App\Domain\Services\RoomStateService;

class RoomManagementController extends Controller
{
    private Room $roomModel;
    private RoomStatusLog $roomLogModel;
    private RoomStateService $stateService;

    public function __construct()
    {
        parent::__construct();
        $this->roomModel = new Room();
        $this->roomLogModel = new RoomStatusLog();
        $this->stateService = new RoomStateService();
    }

    private function getUserRole(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Return Manager for testing if no role assigned, assuming admin session
        return $_SESSION['user']['role_name'] ?? $_SESSION['role_name'] ?? RoomStateService::ROLE_MANAGER;
    }

    public function index()
    {
        $rooms = $this->roomModel->getAllWithType();
        $this->json(['success' => true, 'data' => $rooms]);
    }

    public function updateStatus($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['user'])) {
             return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userRole = $this->getUserRole();
        $userId = $_SESSION['user']['id'] ?? null;

        $input = json_decode(file_get_contents('php://input'), true);
        $newStatus = $input['status'] ?? $_POST['status'] ?? null;
        $notes = $input['notes'] ?? $_POST['notes'] ?? null;

        if (!$newStatus) {
            return $this->json(['success' => false, 'message' => 'Status is required'], 400);
        }

        $room = $this->roomModel->findById($id);
        if (!$room) {
            return $this->json(['success' => false, 'message' => 'Room not found'], 404);
        }

        $currentStatus = $room['status'];

        if (!$this->stateService->canTransition($currentStatus, $newStatus, $userRole)) {
            return $this->json(['success' => false, 'message' => "Transition from $currentStatus to $newStatus denied for role $userRole"], 403);
        }

        $success = $this->roomModel->updateStatus($id, $newStatus, $userId, $notes);

        if ($success) {
            return $this->json(['success' => true, 'message' => 'Status updated']);
        }

        return $this->json(['success' => false, 'message' => 'Internal server error'], 500);
    }

    public function available()
    {
        $checkIn = $_GET['check_in'] ?? null;
        $checkOut = $_GET['check_out'] ?? null;
        $roomTypeId = $_GET['room_type_id'] ?? null;

        if (!$checkIn || !$checkOut) {
            return $this->json(['success' => false, 'message' => 'check_in and check_out required'], 400);
        }

        $rooms = $this->roomModel->getAvailableInRange($checkIn, $checkOut, $roomTypeId ? (int)$roomTypeId : null);
        $this->json(['success' => true, 'data' => $rooms]);
    }
}

