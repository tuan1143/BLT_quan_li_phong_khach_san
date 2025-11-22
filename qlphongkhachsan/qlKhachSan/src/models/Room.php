<?php
// Room model class representing the room entity and its related database operations

class Room {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Method to get all available rooms
    public function getAllRooms() {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE available = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to get room by ID
    public function getRoomById($id) {
        $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to create a new room
    public function createRoom($data) {
        $stmt = $this->db->prepare("INSERT INTO rooms (name, description, price, available) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $data['available']]);
    }

    // Method to update room information
    public function updateRoom($id, $data) {
        $stmt = $this->db->prepare("UPDATE rooms SET name = ?, description = ?, price = ?, available = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $data['available'], $id]);
    }

    // Method to delete a room
    public function deleteRoom($id) {
        $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>