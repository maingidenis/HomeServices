-- User Table
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('client', 'provider', 'admin') NOT NULL,
    health_status ENUM('healthy', 'infected', 'recovered', 'quarantined'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Service Table
CREATE TABLE Service (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    provider_id INT NOT NULL,
    category ENUM('cleaning', 'repairs', 'maintenance', 'other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES User(user_id)
);

-- Appointment Table
CREATE TABLE Appointment (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_time DATETIME NOT NULL,
    status ENUM('requested', 'confirmed', 'completed', 'cancelled') DEFAULT 'requested',
    location VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES User(user_id),
    FOREIGN KEY (service_id) REFERENCES Service(service_id)
);

-- VisitLog Table
CREATE TABLE VisitLog (
    visit_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_id INT NOT NULL,
    check_in_time DATETIME,
    checkout_time DATETIME,
    covid_status ENUM('healthy', 'infected', 'quarantined', 'unknown'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (appointment_id) REFERENCES Appointment(appointment_id)
);

-- Notification Table
CREATE TABLE Notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);
