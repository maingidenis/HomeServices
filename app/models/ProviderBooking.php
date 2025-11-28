<?php
/**
 * ProviderBooking Model
 * Handles bookings for specific services using existing 'service' table
 * Links bookings to service_id from the existing service table
 */
class ProviderBooking {
    private $conn;
    private $table = 'provider_bookings';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new provider booking linked to existing service
     */
    public function createBooking($data) {
        $bookingRef = $this->generateBookingRef();
        
        $query = "INSERT INTO " . $this->table . " 
            (booking_ref, service_id, client_user_id, client_name, client_email, client_mobile,
             service_type, service_description, preferred_date, preferred_time, estimated_duration,
             service_address, client_vaccinated, client_test_provided, mask_agreement, client_notes)
            VALUES
            (:booking_ref, :service_id, :client_user_id, :client_name, :client_email, :client_mobile,
             :service_type, :service_description, :preferred_date, :preferred_time, :estimated_duration,
             :service_address, :client_vaccinated, :client_test_provided, :mask_agreement, :client_notes)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':booking_ref', $bookingRef);
            $stmt->bindParam(':service_id', $data['service_id']);
            $stmt->bindParam(':client_user_id', $data['client_user_id']);
            $stmt->bindParam(':client_name', $this->sanitize($data['client_name']));
            $stmt->bindParam(':client_email', filter_var($data['client_email'], FILTER_SANITIZE_EMAIL));
            $stmt->bindParam(':client_mobile', preg_replace('/[^0-9+]/', '', $data['client_mobile']));
            $stmt->bindParam(':service_type', $this->sanitize($data['service_type']));
            $stmt->bindParam(':service_description', $this->sanitize($data['service_description'] ?? ''));
            $stmt->bindParam(':preferred_date', $data['preferred_date']);
            $stmt->bindParam(':preferred_time', $data['preferred_time'] ?? null);
            $stmt->bindParam(':estimated_duration', $data['estimated_duration'] ?? null);
            $stmt->bindParam(':service_address', $this->sanitize($data['service_address']));
            $stmt->bindParam(':client_vaccinated', $data['client_vaccinated'] ?? 0);
            $stmt->bindParam(':client_test_provided', $data['client_test_provided'] ?? 0);
            $stmt->bindParam(':mask_agreement', $data['mask_agreement'] ?? 0);
            $stmt->bindParam(':client_notes', $this->sanitize($data['client_notes'] ?? ''));
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'booking_id' => $this->conn->lastInsertId(),
                    'booking_ref' => $bookingRef
                ];
            }
            return false;
        } catch (PDOException $e) {
            error_log('ProviderBooking createBooking error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get booking by reference with service details
     */
    public function getByRef($bookingRef) {
        $query = "SELECT pb.*, s.title as service_title, s.category, s.address as service_location,
                         s.provider_id, s.covid_restrictions
                  FROM " . $this->table . " pb
                  JOIN service s ON pb.service_id = s.service_id
                  WHERE pb.booking_ref = :booking_ref";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_ref', $bookingRef);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get bookings by client user ID
     */
    public function getByClientId($clientUserId) {
        $query = "SELECT pb.*, s.title as service_title, s.category
                  FROM " . $this->table . " pb
                  JOIN service s ON pb.service_id = s.service_id
                  WHERE pb.client_user_id = :client_user_id
                  ORDER BY pb.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_user_id', $clientUserId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get bookings by service ID
     */
    public function getByServiceId($serviceId) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE service_id = :service_id
                  ORDER BY preferred_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get bookings by provider ID (through service table)
     */
    public function getByProviderId($providerId) {
        $query = "SELECT pb.*, s.title as service_title
                  FROM " . $this->table . " pb
                  JOIN service s ON pb.service_id = s.service_id
                  WHERE s.provider_id = :provider_id
                  ORDER BY pb.preferred_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':provider_id', $providerId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update booking status
     */
    public function updateStatus($bookingId, $status, $notes = null) {
        $validStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET status = :status";
        
        if ($status === 'confirmed') {
            $query .= ", confirmed_at = NOW()";
        } elseif ($status === 'completed') {
            $query .= ", completed_at = NOW()";
        }
        
        if ($notes) {
            $query .= ", provider_notes = :notes";
        }
        
        $query .= " WHERE booking_id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':booking_id', $bookingId);
        
        if ($notes) {
            $stmt->bindParam(':notes', $this->sanitize($notes));
        }
        
        return $stmt->execute();
    }
    
    /**
     * Set price for booking
     */
    public function setPrice($bookingId, $quotedPrice, $finalPrice = null) {
        $query = "UPDATE " . $this->table . "
                  SET quoted_price = :quoted_price, final_price = :final_price
                  WHERE booking_id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quoted_price', $quotedPrice);
        $stmt->bindParam(':final_price', $finalPrice ?? $quotedPrice);
        $stmt->bindParam(':booking_id', $bookingId);
        
        return $stmt->execute();
    }
    
    /**
     * Count bookings for service on a date
     */
    public function countServiceBookings($serviceId, $date) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . "
                  WHERE service_id = :service_id AND preferred_date = :date
                  AND status NOT IN ('cancelled')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    /**
     * Get all bookings (admin)
     */
    public function getAll($limit = 50) {
        $query = "SELECT pb.*, s.title as service_title, s.category
                  FROM " . $this->table . " pb
                  JOIN service s ON pb.service_id = s.service_id
                  ORDER BY pb.created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Generate unique booking reference
     */
    private function generateBookingRef() {
        return 'PB-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(100, 999);
    }
    
    /**
     * Sanitize input
     */
    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
