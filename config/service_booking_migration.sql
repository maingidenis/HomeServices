-- ServiceBooking Table Migration
-- Run this SQL to create the ServiceBooking table for Special General Service

CREATE TABLE IF NOT EXISTS ServiceBooking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    
    -- Contact Information
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    
    -- Service Details
    service_type ENUM('general_inspection', 'plumbing', 'electrical', 'carpentry', 'painting', 'cleaning', 'hvac', 'roofing', 'landscaping', 'other') NOT NULL,
    preferred_date DATE NOT NULL,
    duration VARCHAR(50) NOT NULL COMMENT 'e.g., 2 hours, 1 day, 3 days',
    preferred_cost DECIMAL(10, 2) DEFAULT 0.00,
    address TEXT NOT NULL,
    additional_details TEXT,
    
    -- Inspection Results (filled by service provider)
    inspection_findings TEXT COMMENT 'List of urgent work identified during inspection',
    
    -- COVID Safety Options
    covid_vaccinated TINYINT(1) DEFAULT 0 COMMENT 'Worker vaccination requirement',
    covid_test_required TINYINT(1) DEFAULT 0 COMMENT 'Recent COVID test required',
    mask_required TINYINT(1) DEFAULT 0 COMMENT 'Mask wearing required',
    
    -- Booking Status
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    
    -- Indexes for faster queries
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_service_type (service_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample service types for the Special General Service dropdown
-- These can be used in the service.php form
