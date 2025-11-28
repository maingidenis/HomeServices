<?php
/**
 * ServiceProvider Model
 * Handles service providers with location data for nearby search
 */
require_once __DIR__ . '/../../config/Database.php';

class ServiceProvider {
    private $conn;
    
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    /**
     * Register a new service provider with location
     */
    public function register($data) {
        $sql = "INSERT INTO ServiceProvider 
                (user_id, business_name, service_category, description, 
                 address, latitude, longitude, phone, email,
                 covid_vaccinated, covid_safe_certified, max_bookings_per_day,
                 available_days, working_hours_start, working_hours_end)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['user_id'],
            $this->sanitize($data['business_name']),
            $this->sanitize($data['service_category']),
            $this->sanitize($data['description']),
            $this->sanitize($data['address']),
            floatval($data['latitude']),
            floatval($data['longitude']),
            $this->sanitize($data['phone']),
            filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            isset($data['covid_vaccinated']) ? 1 : 0,
            isset($data['covid_safe_certified']) ? 1 : 0,
            intval($data['max_bookings_per_day']),
            $this->sanitize($data['available_days']),
            $this->sanitize($data['working_hours_start']),
            $this->sanitize($data['working_hours_end'])
        ]);
    }
    
    /**
     * Find nearby providers using Haversine formula
     * @param float $lat User latitude
     * @param float $lng User longitude  
     * @param float $radius Search radius in km
     * @param string $category Optional service category filter
     */
    public function findNearby($lat, $lng, $radius = 25, $category = null) {
        $sql = "SELECT *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude)))) AS distance
                FROM ServiceProvider
                WHERE is_active = 1";
        
        $params = [$lat, $lng, $lat];
        
        if ($category) {
            $sql .= " AND service_category = ?";
            $params[] = $category;
        }
        
        $sql .= " HAVING distance < ? ORDER BY distance ASC";
        $params[] = $radius;
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get provider by ID
     */
    public function getById($provider_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ServiceProvider WHERE provider_id = ?");
        $stmt->execute([$provider_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get provider by user ID
     */
    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ServiceProvider WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all providers by category
     */
    public function getByCategory($category) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM ServiceProvider WHERE service_category = ? AND is_active = 1"
        );
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all active providers
     */
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM ServiceProvider WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update provider COVID status
     */
    public function updateCovidStatus($provider_id, $vaccinated, $certified) {
        $stmt = $this->conn->prepare(
            "UPDATE ServiceProvider SET covid_vaccinated = ?, covid_safe_certified = ? WHERE provider_id = ?"
        );
        return $stmt->execute([$vaccinated ? 1 : 0, $certified ? 1 : 0, $provider_id]);
    }
    
    /**
     * Check booking availability
     */
    public function checkAvailability($provider_id, $date) {
        $provider = $this->getById($provider_id);
        if (!$provider) return false;
        
        // Count existing bookings for that date
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM ServiceBooking 
             WHERE provider_id = ? AND DATE(preferred_date) = ? AND status != 'cancelled'"
        );
        $stmt->execute([$provider_id, $date]);
        $bookings = $stmt->fetchColumn();
        
        return $bookings < $provider['max_bookings_per_day'];
    }
    
    /**
     * Get providers as GeoJSON for map display
     */
    public function getGeoJSON($category = null) {
        $providers = $category ? $this->getByCategory($category) : $this->getAll();
        
        $features = [];
        foreach ($providers as $p) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [floatval($p['longitude']), floatval($p['latitude'])]
                ],
                'properties' => [
                    'id' => $p['provider_id'],
                    'name' => $p['business_name'],
                    'category' => $p['service_category'],
                    'address' => $p['address'],
                    'phone' => $p['phone'],
                    'covid_safe' => $p['covid_safe_certified'] ? true : false,
                    'vaccinated' => $p['covid_vaccinated'] ? true : false
                ]
            ];
        }
        
        return json_encode([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }
    
    private function sanitize($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
?>
