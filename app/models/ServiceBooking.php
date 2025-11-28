<?php
/**
 * ServiceBooking Model
 * Handles Special General Service bookings with security and privacy features
 */
require_once __DIR__ . '/../../config/Database.php';

class ServiceBooking {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    /**
     * Create a new service booking with sanitized inputs
     */
    public function createBooking($data) {
        // Sanitize all inputs
        $name = $this->sanitize($data['name']);
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $mobile = preg_replace('/[^0-9+]/', '', $data['mobile']);
        $service_type = $this->sanitize($data['service_type']);
        $preferred_date = $this->sanitize($data['preferred_date']);
        $duration = $this->sanitize($data['duration']);
        $preferred_cost = floatval($data['preferred_cost']);
        $address = $this->sanitize($data['address']);
        $additional_details = $this->sanitize($data['additional_details']);
        $covid_vaccinated = isset($data['covid_vaccinated']) ? 1 : 0;
        $covid_test_required = isset($data['covid_test_required']) ? 1 : 0;
        $mask_required = isset($data['mask_required']) ? 1 : 0;
        $user_id = intval($data['user_id']);
        
        // Generate unique booking reference
        $booking_ref = $this->generateBookingRef();
        
        $sql = "INSERT INTO ServiceBooking 
                (booking_ref, user_id, name, email, mobile, service_type, preferred_date, 
                 duration, preferred_cost, address, additional_details, 
                 covid_vaccinated, covid_test_required, mask_required, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $booking_ref, $user_id, $name, $email, $mobile, $service_type,
            $preferred_date, $duration, $preferred_cost, $address, $additional_details,
            $covid_vaccinated, $covid_test_required, $mask_required
        ]) ? $booking_ref : false;
    }
    
    /**
     * Get booking by reference
     */
    public function getByRef($booking_ref) {
        $stmt = $this->conn->prepare("SELECT * FROM ServiceBooking WHERE booking_ref = ?");
        $stmt->execute([$booking_ref]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all bookings for a user
     */
    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ServiceBooking WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all bookings (for admin)
     */
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM ServiceBooking ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update booking status
     */
    public function updateStatus($booking_id, $status) {
        $allowed_statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE ServiceBooking SET status = ? WHERE booking_id = ?");
        return $stmt->execute([$status, $booking_id]);
    }
    
    /**
     * Add inspection findings to booking
     */
    public function addInspectionFindings($booking_id, $findings) {
        $findings = $this->sanitize($findings);
        $stmt = $this->conn->prepare("UPDATE ServiceBooking SET inspection_findings = ? WHERE booking_id = ?");
        return $stmt->execute([$findings, $booking_id]);
    }
    
    /**
     * Generate unique booking reference
     */
    private function generateBookingRef() {
        return 'HSB-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    }
    
    /**
     * Sanitize input
     */
    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Count all bookings
     */
    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM ServiceBooking");
        return $stmt->fetchColumn();
    }
}
?>
