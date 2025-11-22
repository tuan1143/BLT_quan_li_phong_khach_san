<?php
namespace App\Controllers;

use App\Models\Room;

class RoomController
{
    protected $roomModel;

    public function __construct()
    {
        $this->roomModel = new Room();
    }

    public function listRooms()
    {
        $rooms = $this->roomModel->getAllRooms();
        include '../src/views/rooms/list.php';
    }

    public function showRoom($id)
    {
        $room = $this->roomModel->getRoomById($id);
        include '../src/views/rooms/detail.php'; // Assuming you will create a detail view
    }

    public function createRoom($data)
    {
        $this->roomModel->createRoom($data);
        header('Location: /rooms'); // Redirect to the list of rooms
        exit;
    }

    public function updateRoom($id, $data)
    {
        $this->roomModel->updateRoom($id, $data);
        header('Location: /rooms'); // Redirect to the list of rooms
        exit;
    }

    public function deleteRoom($id)
    {
        $this->roomModel->deleteRoom($id);
        header('Location: /rooms'); // Redirect to the list of rooms
        exit;
    }
}
?>