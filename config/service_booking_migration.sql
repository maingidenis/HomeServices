-- Booking Tables Migration for HomeServices
-- These tables work with existing 'service' and 'servicepackage' tables
-- Run this SQL to add booking functionality

-- Service Bookings Table (for Special General Service - package-based bookings)
CREATE TABLE IF NOT EXISTS service_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    
    -- Links to existing tables
    service_id INT,
    package_id INT,
    
    -- Contact Information
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    
    -- Service Details
    preferred_date DATE NOT NULL,
    duration VARCHAR(50),
    preferred_cost DECIMAL(10, 2) DEFAULT 0.00,
    address TEXT NOT NULL,
    additional_details TEXT,
    
    -- Inspection Results
    inspection_findings TEXT COMMENT 'List of urgent work identified during inspection',
    
    -- COVID Safety Options
    covid_vaccinated BOOLEAN DEFAULT FALSE,
    covid_test_required BOOLEAN DEFAULT FALSE,
    mask_required BOOLEAN DEFAULT FALSE,
    
    -- Booking Status
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys (references existing tables)
    CONSTRAINT fk_sb_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_sb_service FOREIGN KEY (service_id) REFERENCES service(service_id) ON DELETE SET NULL,
    CONSTRAINT fk_sb_package FOREIGN KEY (package_id) REFERENCES servicepackage(package_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for service_bookings
CREATE INDEX idx_sb_user ON service_bookings(user_id);
CREATE INDEX idx_sb_service ON service_bookings(service_id);
CREATE INDEX idx_sb_package ON service_bookings(package_id);
CREATE INDEX idx_sb_status ON service_bookings(status);
CREATE INDEX idx_sb_date ON service_bookings(preferred_date);


-- Provider Bookings Table (for Specific Service - direct service bookings)
CREATE TABLE IF NOT EXISTS provider_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    
    -- Links to existing service table
    service_id INT NOT NULL,
    client_user_id INT NOT NULL,
    
    -- Client Information
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(255) NOT NULL,
    client_mobile VARCHAR(20) NOT NULL,
    
    -- Service Details
    service_type VARCHAR(100) NOT NULL,
    service_description TEXT,
    preferred_date DATE NOT NULL,
    preferred_time TIME,
    estimated_duration VARCHAR(50),
    
    -- Location
    service_address VARCHAR(255) NOT NULL,
    
    -- COVID Safety
    client_vaccinated BOOLEAN DEFAULT FALSE,
    client_test_provided BOOLEAN DEFAULT FALSE,
    mask_agreement BOOLEAN DEFAULT FALSE,
    
    -- Booking Status
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    provider_notes TEXT,
    client_notes TEXT,
    
    -- Pricing
    quoted_price DECIMAL(10, 2),
    final_price DECIMAL(10, 2),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    
    -- Foreign Keys (references existing tables)
    CONSTRAINT fk_pb_service FOREIGN KEY (service_id) REFERENCES service(service_id) ON DELETE CASCADE,
    CONSTRAINT fk_pb_client FOREIGN KEY (client_user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for provider_bookings
CREATE INDEX idx_pb_service ON provider_bookings(service_id);
CREATE INDEX idx_pb_client ON provider_bookings(client_user_id);
CREATE INDEX idx_pb_date ON provider_bookings(preferred_date);
CREATE INDEX idx_pb_status ON provider_bookings(status);
CREATE INDEX idx_pb_ref ON provider_bookings(booking_ref);
