# Hotel Booking System

## Overview
The Hotel Booking System is a web application designed to manage hotel room bookings and user authentication. It allows users to register, log in, view available rooms, and make bookings. The application is built using PHP and follows the MVC (Model-View-Controller) architecture.

## Features
- User registration and login functionality
- Dashboard for users to manage their bookings
- Room listing with availability status
- Booking management including creating and viewing bookings

## Project Structure
```
hotel-booking-system
├── public
│   ├── index.php            # Entry point for the application
│   ├── login.php            # User login form
│   ├── register.php         # User registration form
│   ├── logout.php           # User logout functionality
│   ├── dashboard.php        # User dashboard
│   └── assets
│       ├── css
│       │   └── styles.css   # CSS styles for the application
│       └── js
│           └── app.js       # JavaScript functionality
├── src
│   ├── controllers
│   │   ├── AuthController.php    # Manages user authentication
│   │   ├── BookingController.php  # Manages room bookings
│   │   └── RoomController.php     # Manages room operations
│   ├── models
│   │   ├── User.php          # User model
│   │   ├── Booking.php       # Booking model
│   │   └── Room.php          # Room model
│   ├── views
│   │   ├── auth
│   │   │   ├── login.php     # Login view
│   │   │   └── register.php  # Registration view
│   │   ├── bookings
│   │   │   ├── list.php      # List of bookings
│   │   │   └── form.php      # Booking form
│   │   └── rooms
│   │       └── list.php      # List of rooms
│   └── helpers
│       ├── db.php           # Database helper functions
│       └── auth.php         # Authentication helper functions
├── config
│   └── database.php         # Database configuration
├── migrations
│   ├── 001_create_users.sql  # Migration for users table
│   ├── 002_create_rooms.sql   # Migration for rooms table
│   └── 003_create_bookings.sql # Migration for bookings table
├── sql
│   └── seed.sql              # Seed data for the database
├── tests
│   ├── AuthTest.php          # Unit tests for authentication
│   └── BookingTest.php       # Unit tests for bookings
├── .env.example               # Example environment configuration
├── composer.json              # Composer configuration
└── README.md                  # Project documentation
```

## Installation
1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd hotel-booking-system
   ```
3. Install dependencies using Composer:
   ```
   composer install
   ```
4. Configure your database settings in `config/database.php`.
5. Run the migrations to set up the database:
   ```
   php migrations/001_create_users.sql
   php migrations/002_create_rooms.sql
   php migrations/003_create_bookings.sql
   ```
6. Optionally, seed the database with initial data:
   ```
   php sql/seed.sql
   ```

## Usage
- Access the application by navigating to `public/index.php` in your web browser.
- Register a new account or log in with an existing account to manage bookings.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License.