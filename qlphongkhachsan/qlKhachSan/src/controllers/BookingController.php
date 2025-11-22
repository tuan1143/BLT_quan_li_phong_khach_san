<?php
namespace App\Controllers;

use App\Models\Booking;
use App\Models\Room;

class BookingController
{
    public function createBooking($data)
    {
        // Validate booking data
        // Assuming $data contains 'user_id', 'room_id', 'check_in', 'check_out'
        if (empty($data['user_id']) || empty($data['room_id']) || empty($data['check_in']) || empty($data['check_out'])) {
            throw new \Exception("Invalid booking data.");
        }

        // Create a new booking
        $booking = new Booking();
        return $booking->create($data);
    }

    public function listBookings($userId)
    {
        // List all bookings for a specific user
        $booking = new Booking();
        return $booking->getByUserId($userId);
    }

    public function getAvailableRooms($checkIn, $checkOut)
    {
        // Get available rooms for the specified date range
        $room = new Room();
        return $room->getAvailableRooms($checkIn, $checkOut);
    }
}
?>