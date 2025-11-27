-- Database Schema Updates for HomeServices Project
-- This file contains all necessary database changes for OAuth, Stripe, and enhanced features

-- Update User table for OAuth support and additional fields
ALTER TABLE User ADD COLUMN IF NOT EXISTS oauth_provider VARCHAR(50) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS oauth_id VARCHAR(255) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS age INT DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS mobile VARCHAR(20) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS country VARCHAR(100) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS language_preferred VARCHAR(50) DEFAULT 'English';
ALTER TABLE User ADD COLUMN IF NOT EXISTS covid_vaccinated BOOLEAN DEFAULT FALSE;
ALTER TABLE User ADD COLUMN IF NOT EXISTS trade VARCHAR(100) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS profession VARCHAR(100) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL;
ALTER TABLE User ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE User ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Create index for OAuth lookups
CREATE INDEX IF NOT EXISTS idx_oauth ON User(oauth_provider, oauth_id);

-- Create Payment table for Stripe integration
CREATE TABLE IF NOT EXISTS Payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_id INT DEFAULT NULL,
    service_package_id INT DEFAULT NULL,
    stripe_payment_intent_id VARCHAR(255) NOT NULL,
    stripe_charge_id VARCHAR(255) DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status VARCHAR(50) DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES Appointment(appointment_id) ON DELETE SET NULL
);

-- Create ServicePackage table
CREATE TABLE IF NOT EXISTS ServicePackage (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    services_included TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    final_price DECIMAL(10, 2) NOT NULL,
    duration_hours INT DEFAULT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Update Service table for enhanced features
ALTER TABLE Service ADD COLUMN IF NOT EXISTS latitude DECIMAL(10, 8) DEFAULT NULL;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS longitude DECIMAL(11, 8) DEFAULT NULL;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS address VARCHAR(255) DEFAULT NULL;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) DEFAULT NULL;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS rating DECIMAL(3, 2) DEFAULT 0.0;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS reviews_count INT DEFAULT 0;
ALTER TABLE Service ADD COLUMN IF NOT EXISTS covid_restrictions TEXT DEFAULT NULL;

-- Create ServiceReview table
CREATE TABLE IF NOT EXISTS ServiceReview (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    user_id INT NOT NULL,
    appointment_id INT DEFAULT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES Service(service_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES Appointment(appointment_id) ON DELETE SET NULL
);

-- Update Appointment table for payment integration
ALTER TABLE Appointment ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'pending';
ALTER TABLE Appointment ADD COLUMN IF NOT EXISTS total_amount DECIMAL(10, 2) DEFAULT 0.0;
ALTER TABLE Appointment ADD COLUMN IF NOT EXISTS preferred_date DATE DEFAULT NULL;
ALTER TABLE Appointment ADD COLUMN IF NOT EXISTS preferred_duration INT DEFAULT NULL;
ALTER TABLE Appointment ADD COLUMN IF NOT EXISTS details TEXT DEFAULT NULL;

-- Create Voucher/Discount table
CREATE TABLE IF NOT EXISTS Voucher (
    voucher_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    min_purchase DECIMAL(10, 2) DEFAULT 0,
    max_discount DECIMAL(10, 2) DEFAULT NULL,
    valid_from TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMP DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create VoucherUsage table
CREATE TABLE IF NOT EXISTS VoucherUsage (
    usage_id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_id INT NOT NULL,
    user_id INT NOT NULL,
    payment_id INT NOT NULL,
    discount_applied DECIMAL(10, 2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voucher_id) REFERENCES Voucher(voucher_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (payment_id) REFERENCES Payment(payment_id) ON DELETE CASCADE
);

-- Insert sample service packages
INSERT INTO ServicePackage (name, description, services_included, base_price, discount_percentage, final_price, duration_hours) VALUES
('Complete Home Inspection', 'Comprehensive inspection of your entire home with detailed report', 'General Inspection, Electrical Check, Plumbing Check, Safety Assessment', 500.00, 15, 425.00, 8),
('Essential Maintenance', 'Essential home maintenance services package', 'Plumbing, Electrical, General Repairs', 300.00, 10, 270.00, 5),
('Emergency Repair Bundle', '24/7 emergency repair services package', 'Emergency Plumbing, Emergency Electrical, Priority Support', 400.00, 20, 320.00, 4);

-- Insert sample vouchers
INSERT INTO Voucher (code, description, discount_type, discount_value, min_purchase, max_discount, valid_until, usage_limit) VALUES
('WELCOME10', 'Welcome discount for new users', 'percentage', 10.00, 100.00, 50.00, DATE_ADD(NOW(), INTERVAL 1 YEAR), 100),
('SAVE50', 'Save $50 on orders over $300', 'fixed', 50.00, 300.00, NULL, DATE_ADD(NOW(), INTERVAL 6 MONTH), 50);
