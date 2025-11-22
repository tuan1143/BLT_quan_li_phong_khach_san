<?php
use PHPUnit\Framework\TestCase;
use App\Models\Booking;

class BookingTest extends TestCase
{
    protected function setUp(): void
    {
        // Code to set up the test environment, e.g., database connection
    }

    public function testCreateBooking()
    {
        $booking = new Booking();
        $result = $booking->create([
            'user_id' => 1,
            'room_id' => 1,
            'check_in' => '2023-10-01',
            'check_out' => '2023-10-05',
        ]);

        $this->assertTrue($result);
    }

    public function testGetBookingById()
    {
        $booking = new Booking();
        $result = $booking->getById(1);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testListBookings()
    {
        $booking = new Booking();
        $result = $booking->list();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testDeleteBooking()
    {
        $booking = new Booking();
        $result = $booking->delete(1);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        // Code to clean up after tests, e.g., closing database connection
    }
}
?>