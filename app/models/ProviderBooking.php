<?php
/**
 * ProviderBooking.php - Updated for SQLite + MySQL Dual-Mode
 * Location: app/models/ProviderBooking.php
 */

class ProviderBooking {
    private $conn;
    private $table = 'provider_bookings';

    public function __construct() {
        require_once __DIR__ . '/../../config/Database.php';
        $this->conn = $GLOBALS['db']->getConnection();
    }

    public function createBooking($data) {
        $bookingRef = $this->generateBookingRef();
        
        $query = "INSERT INTO " . $this->table . "
        (booking_ref, service_id, client_user_id, client_name, client_email, client_mobile,
        service_type, service_description, preferred_date, preferred_time, estimated_duration,
        service_address, client_vaccinated, client_test_provided, mask_agreement, client_notes)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([
                $bookingRef,
                $data['service_id'],
                $data['client_user_id'],
                $this->sanitize($data['client_name']),
                filter_var($data['client_email'], FILTER_SANITIZE_EMAIL),
                preg_replace('/[^0-9+]/', '', $data['client_mobile']),
                $this->sanitize($data['service_type']),
                $this->sanitize($data['service_description'] ?? ''),
                $data['preferred_date'],
                $data['preferred_time'] ?? null,
                $data['estimated_duration'] ?? null,
                $this->sanitize($data['service_address']),
                $data['client_vaccinated'] ?? 0,
                $data['client_test_provided'] ?? 0,
                $data['mask_agreement'] ?? 0,
                $this->sanitize($data['client_notes'] ?? '')
            ])) {
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

    public function getByRef($bookingRef) {
        $query = "SELECT pb.*, s.title as service_title, s.category, s.address as service_location,
        s.provider_id, s.covid_restrictions
        FROM " . $this->table . " pb
        JOIN service s ON pb.service_id = s.service_id
        WHERE pb.booking_ref = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$bookingRef]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByClientId($clientUserId) {
        $query = "SELECT pb.*, s.title as service_title, s.category
        FROM " . $this->table . " pb
        JOIN service s ON pb.service_id = s.service_id
        WHERE pb.client_user_id = ?
        ORDER BY pb.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$clientUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByServiceId($serviceId) {
        $query = "SELECT * FROM " . $this->table . "
        WHERE service_id = ?
        ORDER BY preferred_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$serviceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProviderId($providerId) {
        $query = "SELECT pb.*, s.title as service_title
        FROM " . $this->table . " pb
        JOIN service s ON pb.service_id = s.service_id
        WHERE s.provider_id = ?
        ORDER BY pb.preferred_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$providerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($bookingId, $status, $notes = null) {
        $validStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET status = ?";
        
        if ($status === 'confirmed') {
            $query .= ", confirmed_at = datetime('now')";
        } elseif ($status === 'completed') {
            $query .= ", completed_at = datetime('now')";
        }
        
        if ($notes) {
            $query .= ", provider_notes = ?";
        }
        
        $query .= " WHERE booking_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $params = [$status];
        if ($notes) {
            $params[] = $this->sanitize($notes);
        }
        $params[] = $bookingId;
        
        return $stmt->execute($params);
    }

    public function setPrice($bookingId, $quotedPrice, $finalPrice = null) {
        $query = "UPDATE " . $this->table . "
        SET quoted_price = ?, final_price = ?
        WHERE booking_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quotedPrice, $finalPrice ?? $quotedPrice, $bookingId]);
    }

    public function countServiceBookings($serviceId, $date) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . "
        WHERE service_id = ? AND preferred_date = ?
        AND status NOT IN ('cancelled')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$serviceId, $date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function getAll($limit = 50) {
        $query = "SELECT pb.*, s.title as service_title, s.category
        FROM " . $this->table . " pb
        JOIN service s ON pb.service_id = s.service_id
        ORDER BY pb.created_at DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function generateBookingRef() {
        return 'PB-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(100, 999);
    }

    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
?>
