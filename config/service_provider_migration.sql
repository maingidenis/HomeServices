-- ServiceProvider Migration for Specific Service Booking
-- This migration creates the service_providers table for location-based service search

CREATE TABLE IF NOT EXISTS service_providers (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    service_category VARCHAR(100) NOT NULL,
    description TEXT,
    address VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    
    -- COVID Safety Fields
    covid_vaccinated BOOLEAN DEFAULT FALSE,
    covid_safe_certified BOOLEAN DEFAULT FALSE,
    covid_test_required BOOLEAN DEFAULT FALSE,
    mask_required BOOLEAN DEFAULT FALSE,
    
    -- Booking Limitations
    max_bookings_per_day INT DEFAULT 5,
    available_days VARCHAR(50) DEFAULT 'Mon,Tue,Wed,Thu,Fri',
    working_hours_start TIME DEFAULT '08:00:00',
    working_hours_end TIME DEFAULT '18:00:00',
    
    -- Status and Timestamps
    is_active BOOLEAN DEFAULT TRUE,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    CONSTRAINT fk_provider_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for efficient geo queries and searches
CREATE INDEX idx_provider_location ON service_providers(latitude, longitude);
CREATE INDEX idx_provider_category ON service_providers(service_category);
CREATE INDEX idx_provider_active ON service_providers(is_active);
CREATE INDEX idx_provider_user ON service_providers(user_id);

-- Sample service categories (for reference)
-- Plumbing, Electrical, Carpentry, Painting, Cleaning, Gardening, HVAC, Roofing, General Maintenance

-- Provider Bookings Table for tracking specific service bookings
CREATE TABLE IF NOT EXISTS provider_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    provider_id INT NOT NULL,
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
    service_latitude DECIMAL(10, 8),
    service_longitude DECIMAL(11, 8),
    
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
    
    -- Foreign Keys
    CONSTRAINT fk_booking_provider FOREIGN KEY (provider_id) REFERENCES service_providers(provider_id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_client FOREIGN KEY (client_user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for provider bookings
CREATE INDEX idx_booking_provider ON provider_bookings(provider_id);
CREATE INDEX idx_booking_client ON provider_bookings(client_user_id);
CREATE INDEX idx_booking_date ON provider_bookings(preferred_date);
CREATE INDEX idx_booking_status ON provider_bookings(status);
CREATE INDEX idx_booking_ref ON provider_bookings(booking_ref);
