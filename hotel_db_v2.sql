SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) UNIQUE,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;;

INSERT INTO Users (id, first_name, last_name, username, email, password, phone_number, is_admin) 
VALUES (1, 'Francis', 'Manibad', 'francis', 'francis@admin.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', '09650000001', TRUE);

CREATE TABLE Rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) NOT NULL
    price_per_night INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO Rooms (id, name, type, price_per_night, image_url) 
VALUES (1, 'Room #1', 'Single', 500, 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=3270&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'),
       (2, 'Room #2', 'Couple', 1000, 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'),
       (3, 'Room #3', 'Twin Bed', 1500, 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'),
       (4, 'Room #4', 'Family', 2000, 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=1674&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'),
       (5, 'Room #5', 'Deluxe', 2500, 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?q=80&w=1674&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');


CREATE TABLE Bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    adult_count INT NOT NULL CHECK (adult_count >= 1),
    children_count INT NOT NULL DEFAULT 0,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES Rooms(id) ON DELETE CASCADE,
    CHECK (check_out > check_in)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;;

INSERT INTO Bookings (id, user_id, room_id, adult_count, children_count, check_in, check_out) 
VALUES (1, 1, 1, 2, 1, '2024-05-10', '2024-05-15');
INSERT INTO Bookings (id, user_id, room_id, adult_count, children_count, check_in, check_out) 
VALUES (2, 1, 4, 5, 2, '2024-05-13', '2024-05-16');
INSERT INTO Bookings (id, user_id, room_id, adult_count, children_count, check_in, check_out) 
VALUES (3, 1, 3, 3, 3, '2024-05-12', '2024-05-17');


CREATE TABLE Messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20),
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO Messages (id, name, email, contact_number, content) 
VALUES (1, 'John Doe', 'johndoe@email.com', '09650000002', 'Hello! Can you let me know if you are open during holidays?');
