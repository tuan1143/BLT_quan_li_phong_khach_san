INSERT INTO users (username, password_hash, ho_ten, chuc_vu) VALUES
('admin', '$2y$10$EIXZ1y1g0Z8zY5g1F1K8eO5t1Z5g1F1K8eO5t1Z5g1F1K8eO5t1Z5', 'Admin User', 'Administrator'),
('user1', '$2y$10$EIXZ1y1g0Z8zY5g1F1K8eO5t1Z5g1F1K8eO5t1Z5g1F1K8eO5t1Z5', 'User One', 'Customer');

INSERT INTO rooms (room_number, room_type, price, status) VALUES
(101, 'Single', 100.00, 'available'),
(102, 'Double', 150.00, 'available'),
(103, 'Suite', 250.00, 'available');

INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date) VALUES
(1, 1, '2023-10-01', '2023-10-05'),
(2, 2, '2023-10-10', '2023-10-15');