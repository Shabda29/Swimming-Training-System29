CREATE DATABASE IF NOT EXISTS pool_booking;
USE pool_booking;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE coaches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(200) NOT NULL,
    image_emoji VARCHAR(10) DEFAULT '🏊'
);

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    coach_id INT NOT NULL,
    booking_date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (coach_id) REFERENCES coaches(id)
);

CREATE TABLE coach_timeslots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coach_id INT NOT NULL,
  slot_date DATE NOT NULL,
  slot_time TIME NOT NULL,
  FOREIGN KEY (coach_id) REFERENCES coaches(id)
);


-- Insert demo data
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@pool.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Doe', 'user@pool.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
-- Password for both: password

INSERT INTO coaches (name, specialty) VALUES
('Sarah Johnson', 'Freestyle & Backstroke'),
('Mike Chen', 'Butterfly & Breaststroke'),
('Emma Wilson', 'Beginners & Kids');