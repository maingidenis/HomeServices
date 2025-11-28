<?php
/**
 * ServiceProvider Model
 * Uses existing 'service' table for provider/service data with location-based search
 * Integrated with existing schema - service table has latitude, longitude, address, city fields
 */
class ServiceProvider {
    private $conn;
    private $table = 'service';  // Using existing service table
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Find nearby services using Haversine formula
     * Uses existing service table with latitude/longitude columns
     */
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
    
    /**
     * Get service by ID
     */
    public function getById($serviceId) {
        $query = "SELECT * FROM " . $this->table . " WHERE service_id = :service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get services by provider ID
     */
    public function getByProviderId($providerId) {
        $query = "SELECT * FROM " . $this->table . " WHERE provider_id = :provider_id ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':provider_id', $providerId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get services by category
     */
    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " WHERE category = :category ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all services
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY rating DESC, created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get unique categories
     */
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table . " WHERE category IS NOT NULL ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Get services by city
     */
    public function getByCity($city) {
        $query = "SELECT * FROM " . $this->table . " WHERE city = :city ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':city', $city);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Search services by title or description
     */
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE title LIKE :keyword OR description LIKE :keyword2
                  ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->bindParam(':keyword2', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get COVID restrictions for a service
     */
    public function getCovidRestrictions($serviceId) {
        $query = "SELECT covid_restrictions FROM " . $this->table . " WHERE service_id = :service_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['covid_restrictions'] : null;
    }
    
    /**
     * Check availability (placeholder - can be enhanced with booking table)
     */
    public function checkAvailability($serviceId, $date) {
        // Count existing bookings for this service on the given date
        $query = "SELECT COUNT(*) as booked FROM service_bookings 
                  WHERE service_id = :service_id AND preferred_date = :date 
                  AND status NOT IN ('cancelled')";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':service_id', $serviceId);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $maxBookings = 5; // Default max bookings per day
            $booked = $result['booked'] ?? 0;
            
            return [
                'is_available' => $booked < $maxBookings,
                'available_slots' => max(0, $maxBookings - $booked),
                'booked_slots' => $booked
            ];
        } catch (PDOException $e) {
            // Table might not exist yet, return default availability
            return [
                'is_available' => true,
                'available_slots' => 5,
                'booked_slots' => 0
            ];
        }
    }
    
    /**
     * Get GeoJSON for map display
     */
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
