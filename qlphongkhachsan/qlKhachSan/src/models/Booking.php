<?php
class Booking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createBooking($userId, $roomId, $checkInDate, $checkOutDate) {
        $stmt = $this->db->prepare("INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $roomId, $checkInDate, $checkOutDate]);
    }

    public function getBookingsByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBookings() {
        $stmt = $this->db->query("SELECT * FROM bookings");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelBooking($bookingId) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$bookingId]);
    }
}
?>