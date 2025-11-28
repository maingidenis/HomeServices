<?php
/**
 * ProviderBooking Model
 * Handles bookings for specific service providers
 */
class ProviderBooking {
    private $conn;
    private $table = 'provider_bookings';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new provider booking
     */
    public function createBooking($data) {
        $bookingRef = $this->generateBookingRef();
        
        $query = "INSERT INTO " . $this->table . " 
            (booking_ref, provider_id, client_user_id, client_name, client_email, client_mobile,
             service_type, service_description, preferred_date, preferred_time, estimated_duration,
             service_address, service_latitude, service_longitude, client_vaccinated,
             client_test_provided, mask_agreement, client_notes)
            VALUES
            (:booking_ref, :provider_id, :client_user_id, :client_name, :client_email, :client_mobile,
             :service_type, :service_description, :preferred_date, :preferred_time, :estimated_duration,
             :service_address, :service_latitude, :service_longitude, :client_vaccinated,
             :client_test_provided, :mask_agreement, :client_notes)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':booking_ref', $bookingRef);
            $stmt->bindParam(':provider_id', $data['provider_id']);
            $stmt->bindParam(':client_user_id', $data['client_user_id']);
            $stmt->bindParam(':client_name', $this->sanitize($data['client_name']));
            $stmt->bindParam(':client_email', $this->sanitize($data['client_email']));
            $stmt->bindParam(':client_mobile', $this->sanitize($data['client_mobile']));
            $stmt->bindParam(':service_type', $this->sanitize($data['service_type']));
            $stmt->bindParam(':service_description', $this->sanitize($data['service_description'] ?? ''));
            $stmt->bindParam(':preferred_date', $data['preferred_date']);
            $stmt->bindParam(':preferred_time', $data['preferred_time'] ?? null);
            $stmt->bindParam(':estimated_duration', $data['estimated_duration'] ?? null);
            $stmt->bindParam(':service_address', $this->sanitize($data['service_address']));
            $stmt->bindParam(':service_latitude', $data['service_latitude'] ?? null);
            $stmt->bindParam(':service_longitude', $data['service_longitude'] ?? null);
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
     * Get booking by reference
     */
    public function getByRef($bookingRef) {
        $query = "SELECT pb.*, sp.business_name, sp.phone as provider_phone
                  FROM " . $this->table . " pb
                  JOIN service_providers sp ON pb.provider_id = sp.provider_id
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
        $query = "SELECT pb.*, sp.business_name
                  FROM " . $this->table . " pb
                  JOIN service_providers sp ON pb.provider_id = sp.provider_id
                  WHERE pb.client_user_id = :client_user_id
                  ORDER BY pb.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_user_id', $clientUserId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get bookings by provider ID
     */
    public function getByProviderId($providerId) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE provider_id = :provider_id
                  ORDER BY preferred_date ASC";
        
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
        
        $query = "UPDATE " . $this->table . "
                  SET status = :status";
        
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
     * Set final price for booking
     */
    public function setPrice($bookingId, $quotedPrice, $finalPrice = null) {
        $query = "UPDATE " . $this->table . "
                  SET quoted_price = :quoted_price,
                      final_price = :final_price
                  WHERE booking_id = :booking_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quoted_price', $quotedPrice);
        $stmt->bindParam(':final_price', $finalPrice ?? $quotedPrice);
        $stmt->bindParam(':booking_id', $bookingId);
        
        return $stmt->execute();
    }
    
    /**
     * Count bookings for provider on a date
     */
    public function countProviderBookings($providerId, $date) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . "
                  WHERE provider_id = :provider_id
                  AND preferred_date = :date
                  AND status NOT IN ('cancelled')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':provider_id', $providerId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
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
