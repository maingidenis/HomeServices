<?php
/**
 * ServiceBooking.php - Updated for SQLite + MySQL Dual-Mode
 * Location: app/models/ServiceBooking.php
 */

class ServiceBooking {
    private $conn;
    private $table = 'service_bookings';

    public function __construct() {
        require_once __DIR__ . '/../../config/Database.php';
        $this->conn = $GLOBALS['db']->getConnection();
    }

    public function createBooking($data) {
        $bookingRef = $this->generateBookingRef();
        $name = $this->sanitize($data['name']);
        $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $mobile = preg_replace('/[^0-9+]/', '', $data['mobile']);
        $preferred_date = $data['preferred_date'];
        $duration = $this->sanitize($data['duration'] ?? '');
        $preferred_cost = $data['preferred_cost'] ?? null;
        $address = $this->sanitize($data['address']);
        $additional_details = $this->sanitize($data['additional_details'] ?? '');
        $covid_vaccinated = $data['covid_vaccinated'] ?? 0;
        $covid_test_required = $data['covid_test_required'] ?? 0;
        $mask_required = $data['mask_required'] ?? 0;
        $user_id = $data['user_id'];
        $service_id = $data['service_id'];
        $service_type = $data['service_type'];
        $package_id = $data['package_id'];
        
        $query = "INSERT INTO " . $this->table . "
        (booking_ref, user_id, service_id, service_type, package_id, name, email, mobile,
        preferred_date, duration, preferred_cost, address, additional_details,
        covid_vaccinated, covid_test_required, mask_required, status)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            if ($stmt->execute([
                $bookingRef, $user_id, $service_id, $service_type, $package_id,
                $name, $email, $mobile, $preferred_date, $duration, $preferred_cost,
                $address, $additional_details, $covid_vaccinated, $covid_test_required, $mask_required
            ])) {
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

    public function getByRef($bookingRef) {
        $query = "SELECT sb.*, s.title as service_title, sb.service_type, s.category, s.provider_id
        FROM " . $this->table . " sb
        LEFT JOIN service s ON sb.service_id = s.service_id
        WHERE sb.booking_ref = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$bookingRef]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId) {
        $query = "SELECT sb.*, s.title as service_title, s.category
        FROM " . $this->table . " sb
        LEFT JOIN service s ON sb.service_id = s.service_id
        WHERE sb.user_id = ?
        ORDER BY sb.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServicesByCategory($category = null) {
        $query = "SELECT * FROM service WHERE 1=1";
        if ($category) {
            $query .= " AND category = ?";
        }
        $query .= " ORDER BY rating DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($category) {
            $stmt->execute([$category]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($bookingId, $status) {
        $validStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET status = ? WHERE booking_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $bookingId]);
    }

    public function addInspectionFindings($bookingId, $findings) {
        $query = "UPDATE " . $this->table . "
        SET inspection_findings = ?
        WHERE booking_id = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->sanitize($findings), $bookingId]);
    }

    public function getAll($limit = 50) {
        $query = "SELECT sb.*, s.title as service_title
        FROM " . $this->table . " sb
        LEFT JOIN service s ON sb.service_id = s.service_id
        ORDER BY sb.created_at DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    private function generateBookingRef() {
        return 'SB-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(100, 999);
    }

    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
?>
