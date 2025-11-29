<?php
/**
 * ServiceBooking Model
 * Handles Special General Service bookings using existing service and servicepackage tables
 * Integrated with existing schema for home_services database
 */
class ServiceBooking {
    private $conn;
    private $table = 'service_bookings';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new service booking linked to existing service/package
     */
    public function createBooking($data) {
        $bookingRef = $this->generateBookingRef();
        
        $query = "INSERT INTO " . $this->table . " 
            (booking_ref, user_id, service_id, package_id, name, email, mobile,
             preferred_date, duration, preferred_cost, address, additional_details,
             covid_vaccinated, covid_test_required, mask_required, status)
            VALUES
            (:booking_ref, :user_id, :service_id, :package_id, :name, :email, :mobile,
             :preferred_date, :duration, :preferred_cost, :address, :additional_details,
             :covid_vaccinated, :covid_test_required, :mask_required, 'pending')";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':booking_ref', $bookingRef);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':service_id', $data['service_id']);
            $stmt->bindParam(':package_id', $data['package_id']);
            $stmt->bindParam(':name', $this->sanitize($data['name']));
            $stmt->bindParam(':email', filter_var($data['email'], FILTER_SANITIZE_EMAIL));
            $stmt->bindParam(':mobile', preg_replace('/[^0-9+]/', '', $data['mobile']));
            $stmt->bindParam(':preferred_date', $data['preferred_date']);
            $stmt->bindParam(':duration', $this->sanitize($data['duration'] ?? ''));
            $stmt->bindParam(':preferred_cost', $data['preferred_cost'] ?? null);
            $stmt->bindParam(':address', $this->sanitize($data['address']));
            $stmt->bindParam(':additional_details', $this->sanitize($data['additional_details'] ?? ''));
            $stmt->bindParam(':covid_vaccinated', $data['covid_vaccinated'] ?? 0);
            $stmt->bindParam(':covid_test_required', $data['covid_test_required'] ?? 0);
            $stmt->bindParam(':mask_required', $data['mask_required'] ?? 0);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'booking_id' => $this->conn->lastInsertId(),
                    'booking_ref' => $bookingRef
                ];
            }
            return false;
        } catch (PDOException $e) {
            error_log('ServiceBooking createBooking error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get booking by reference with service and package details
     */
    public function getByRef($bookingRef) {
        $query = "SELECT sb.*, s.title as service_title, s.category, s.provider_id,
                         sp.name as package_name, sp.final_price as package_price
                  FROM " . $this->table . " sb
                  LEFT JOIN service s ON sb.service_id = s.service_id
                  LEFT JOIN servicepackage sp ON sb.package_id = sp.package_id
                  WHERE sb.booking_ref = :booking_ref";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_ref', $bookingRef);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all bookings for a user with service details
     */
    public function getByUserId($userId) {
        $query = "SELECT sb.*, s.title as service_title, s.category,
                  COALESCE(sp.name, 'No Package') as package_name, COALESCE(sp.final_price, 0) as package_price
                  FROM " . $this->table . " sb
                  LEFT JOIN service s ON sb.service_id = s.service_id
                  LEFT JOIN servicepackage sp ON sb.package_id = sp.package_id
                  WHERE sb.user_id = :user_id
                  ORDER BY sb.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all service packages for booking selection
     */
    public function getActivePackages() {
        $query = "SELECT * FROM servicepackage WHERE is_active = 1 ORDER BY final_price ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all services with location data for nearby search
     */
    public function getServicesByCategory($category = null) {
        $query = "SELECT * FROM service WHERE 1=1";
        if ($category) {
            $query .= " AND category = :category";
        }
        $query .= " ORDER BY rating DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update booking status
     */
    public function updateStatus($bookingId, $status) {
        $validStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET status = :status WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':booking_id', $bookingId);
        
        return $stmt->execute();
    }
    
    /**
     * Add inspection findings after service completion
     */
    public function addInspectionFindings($bookingId, $findings) {
        $query = "UPDATE " . $this->table . " 
                  SET inspection_findings = :findings 
                  WHERE booking_id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':findings', $this->sanitize($findings));
        $stmt->bindParam(':booking_id', $bookingId);
        
        return $stmt->execute();
    }
    
    /**
     * Get all bookings (admin function)
     */
    public function getAll($limit = 50) {
        $query = "SELECT sb.*, s.title as service_title, sp.name as package_name
                  FROM " . $this->table . " sb
                  LEFT JOIN service s ON sb.service_id = s.service_id
                  LEFT JOIN servicepackage sp ON sb.package_id = sp.package_id
                  ORDER BY sb.created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count total bookings
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Generate unique booking reference
     */
    private function generateBookingRef() {
        return 'SB-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(100, 999);
    }
    
    /**
     * Sanitize input
     */
    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
