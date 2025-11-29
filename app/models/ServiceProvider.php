<?php
/**
 * ServiceProvider.php - Updated for SQLite + MySQL Dual-Mode
 * Location: app/models/ServiceProvider.php
 */

class ServiceProvider {
    private $conn;
    private $table = 'Service';

    public function __construct() {
        require_once __DIR__ . '/../../config/Database.php';
        $this->conn = $GLOBALS['db']->getConnection();
    }

    public function findNearby($lat, $lng, $radiusKm = 10, $category = null) {
        $query = "SELECT *,
        (6371 * acos(cos(radians(:lat)) * cos(radians(latitude)) *
        cos(radians(longitude) - radians(:lng)) + sin(radians(:lat2)) *
        sin(radians(latitude)))) AS distance
        FROM " . $this->table . "
        WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
        
        if ($category && $category !== '') {
            $query .= " AND category = :category";
        }
        
        $query .= " HAVING distance <= :radius ORDER BY distance ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->bindParam(':lat2', $lat);
            $stmt->bindParam(':radius', $radiusKm);
            if ($category && $category !== '') {
                $stmt->bindParam(':category', $category);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('ServiceProvider findNearby error: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($serviceId) {
        $query = "SELECT * FROM " . $this->table . " WHERE service_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$serviceId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByProviderId($providerId) {
        $query = "SELECT * FROM " . $this->table . " WHERE provider_id = ? ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$providerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " WHERE category = ? ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY rating DESC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table . " WHERE category IS NOT NULL ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getByCity($city) {
        $query = "SELECT * FROM " . $this->table . " WHERE city = ? ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$city]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . "
        WHERE title LIKE ? OR description LIKE ?
        ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $keyword . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCovidRestrictions($serviceId) {
        $query = "SELECT covid_restrictions FROM " . $this->table . " WHERE service_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$serviceId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['covid_restrictions'] : null;
    }

    public function checkAvailability($serviceId, $date) {
        $query = "SELECT COUNT(*) as booked FROM service_bookings
        WHERE service_id = ? AND preferred_date = ?
        AND status NOT IN ('cancelled')";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$serviceId, $date]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $maxBookings = 5;
            $booked = $result['booked'] ?? 0;
            
            return [
                'is_available' => $booked < $maxBookings,
                'available_slots' => max(0, $maxBookings - $booked),
                'booked_slots' => $booked
            ];
        } catch (PDOException $e) {
            return [
                'is_available' => true,
                'available_slots' => 5,
                'booked_slots' => 0
            ];
        }
    }

    public function getGeoJSON($services = null) {
        if ($services === null) {
            $services = $this->getAll();
        }
        
        $features = [];
        foreach ($services as $s) {
            if ($s['latitude'] && $s['longitude']) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [floatval($s['longitude']), floatval($s['latitude'])]
                    ],
                    'properties' => [
                        'id' => $s['service_id'],
                        'title' => $s['title'],
                        'category' => $s['category'],
                        'address' => $s['address'],
                        'city' => $s['city'],
                        'rating' => $s['rating'],
                        'covid_restrictions' => $s['covid_restrictions']
                    ]
                ];
            }
        }
        
        return json_encode([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }
}
?>
