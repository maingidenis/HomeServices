<?php
/**
 * Providers API Endpoint
 * Returns nearby service providers based on location and filters
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../app/models/ServiceProvider.php';
require_once __DIR__ . '/../../config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $serviceProvider = new ServiceProvider($db);
    
    // Get and validate parameters
    $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
    $lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
    $radius = filter_input(INPUT_GET, 'radius', FILTER_VALIDATE_INT) ?: 10;
    $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
    $vaccinated = filter_input(INPUT_GET, 'vaccinated', FILTER_VALIDATE_BOOLEAN);
    $certified = filter_input(INPUT_GET, 'certified', FILTER_VALIDATE_BOOLEAN);
    
    if ($lat === false || $lng === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid latitude or longitude']);
        exit;
    }
    
    // Search for nearby providers
    $providers = $serviceProvider->findNearby($lat, $lng, $radius, $category);
    
    // Apply additional filters
    if ($vaccinated || $certified) {
        $providers = array_filter($providers, function($p) use ($vaccinated, $certified) {
            if ($vaccinated && !$p['covid_vaccinated']) return false;
            if ($certified && !$p['covid_safe_certified']) return false;
            return true;
        });
        $providers = array_values($providers);
    }
    
    // Add availability info to each provider
    foreach ($providers as &$provider) {
        $availability = $serviceProvider->checkAvailability(
            $provider['provider_id'],
            date('Y-m-d')
        );
        $provider['available_slots'] = $availability['available_slots'] ?? 0;
        $provider['is_available'] = $availability['is_available'] ?? false;
    }
    
    echo json_encode($providers);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'message' => $e->getMessage()]);
}
?>
