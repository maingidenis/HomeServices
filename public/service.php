<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

ERROR_REPORTING(E_ALL);
ini_set('display_errors', 1);

// Authorization check
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../app/models/ServiceBooking.php';
require_once __DIR__ . '/../app/models/ServiceProvider.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->getConnection();
$serviceBooking = new ServiceBooking($db);
$serviceProvider = new ServiceProvider($db);

function getServiceTypeDisplayName($serviceType) {
    $serviceTypes = [
        'full_inspection' => 'Full House Inspection',
        'plumbing_check' => 'Plumbing Check',
        'electrical_check' => 'Electrical Check',
        'structural_check' => 'Structural Assessment',
        'safety_audit' => 'Safety Audit',
        'general_maintenance' => 'General Maintenance'
    ];
    return $serviceTypes[$serviceType] ?? ucfirst(str_replace('_', ' ', $serviceType));
}

$message = '';
$messageType = '';
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

// Handle Special General Service Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_type'])) {
    if ($_POST['booking_type'] === 'general') {
        $bookingData = [
            'user_id' => $_SESSION['user_id'],
            'service_id' => null,
            'package_id' => null,
            'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'mobile' => filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_SPECIAL_CHARS),
            'service_type' => filter_input(INPUT_POST, 'service_type', FILTER_SANITIZE_SPECIAL_CHARS),
            'preferred_date' => filter_input(INPUT_POST, 'preferred_date', FILTER_SANITIZE_SPECIAL_CHARS),
            'duration' => filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_SPECIAL_CHARS),
            'preferred_cost' => filter_input(INPUT_POST, 'preferred_cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'address' => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS),
            'additional_details' => filter_input(INPUT_POST, 'additional_details', FILTER_SANITIZE_SPECIAL_CHARS),
            'covid_vaccinated' => isset($_POST['covid_vaccinated']) ? 1 : 0,
            'covid_test_required' => isset($_POST['covid_test_required']) ? 1 : 0,
            'mask_required' => isset($_POST['mask_required']) ? 1 : 0
        ];
        
        $result = $serviceBooking->createBooking($bookingData);
        if ($result) {
            $message = 'Booking created successfully! Reference: ' . $result['booking_ref'];
            $messageType = 'success';
        } else {
            $message = 'Failed to create booking. Please try again.';
            $messageType = 'danger';
        }
    }
}

// Get user's bookings
$userBookings = $serviceBooking->getByUserId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - HomeServices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; border-radius: 8px; }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php?page=dashboard"><i class="bi bi-house-door"></i> HomeServices</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php?page=dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="index.php?page=service">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?page=appointment">Appointments</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php?page=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Alert Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Service Type Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'general' ? 'active' : '' ?>" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="bi bi-clipboard-check"></i> Special General Service
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'specific' ? 'active' : '' ?>" id="specific-tab" data-bs-toggle="tab" data-bs-target="#specific" type="button" role="tab">
                    <i class="bi bi-geo-alt"></i> Specific Service
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab">
                    <i class="bi bi-calendar-check"></i> My Bookings
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Special General Service Tab -->
            <div class="tab-pane fade <?= $activeTab === 'general' ? 'show active' : '' ?>" id="general" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Book General House Inspection</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Book a general inspection to identify urgent maintenance needs for your property.</p>
                                <form method="POST" action="service.php?tab=general">
                                    <input type="hidden" name="booking_type" value="general">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Mobile Number *</label>
                                            <input type="tel" class="form-control" name="mobile" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Service Type *</label>
                                            <select class="form-select" name="service_type" required>
                                                <option value="">Select service type</option>
                                                <option value="full_inspection">Full House Inspection</option>
                                                <option value="plumbing_check">Plumbing Check</option>
                                                <option value="electrical_check">Electrical Check</option>
                                                <option value="structural_check">Structural Assessment</option>
                                                <option value="safety_audit">Safety Audit</option>
                                                <option value="general_maintenance">General Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Preferred Date *</label>
                                            <input type="date" class="form-control" name="preferred_date" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Duration</label>
                                            <select class="form-select" name="duration">
                                                <option value="2-4 hours">2-4 Hours</option>
                                                <option value="half_day">Half Day</option>
                                                <option value="full_day">Full Day</option>
                                                <option value="2-3 days">2-3 Days</option>
                                                <option value="1 week">1 Week</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Budget (AUD)</label>
                                            <input type="number" class="form-control" name="preferred_cost" min="0" step="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Property Address *</label>
                                        <textarea class="form-control" name="address" rows="2" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Additional Details</label>
                                        <textarea class="form-control" name="additional_details" rows="3" placeholder="Describe any specific concerns or requirements..."></textarea>
                                    </div>
                                    
                                    <!-- COVID Safety Options -->
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title"><i class="bi bi-shield-check"></i> COVID Safety Preferences</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="covid_vaccinated" id="covid_vaccinated">
                                                <label class="form-check-label" for="covid_vaccinated">I am fully vaccinated</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="covid_test_required" id="covid_test_required">
                                                <label class="form-check-label" for="covid_test_required">Require service provider to show negative COVID test</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="mask_required" id="mask_required">
                                                <label class="form-check-label" for="mask_required">Mask wearing required during service</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-calendar-plus"></i> Submit Booking Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> About This Service</h6>
                            </div>
                            <div class="card-body">
                                <p>Our general inspection service helps identify:</p>
                                <ul>
                                    <li>Urgent repairs needed</li>
                                    <li>Safety hazards</li>
                                    <li>Maintenance priorities</li>
                                    <li>Cost estimates</li>
                                </ul>
                                <hr>
                                <p class="small text-muted">After inspection, you'll receive a detailed report with recommendations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specific Service Tab -->
            <div class="tab-pane fade <?= $activeTab === 'specific' ? 'show active' : '' ?>" id="specific" role="tabpanel">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-search"></i> Find Service Providers</h5>
                            </div>
                            <div class="card-body">
                                <form id="searchForm">
                                    <div class="mb-3">
                                        <label class="form-label">Your Location</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="userLocation" placeholder="Enter address or use GPS">
                                            <button type="button" class="btn btn-outline-secondary" onclick="getLocation()">
                                                <i class="bi bi-geo-alt"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" id="userLat">
                                        <input type="hidden" id="userLng">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Service Category</label>
                                        <select class="form-select" id="serviceCategory">
                                            <option value="">All Categories</option>
                                            <option value="plumbing">Plumbing</option>
                                            <option value="electrical">Electrical</option>
                                            <option value="carpentry">Carpentry</option>
                                            <option value="painting">Painting</option>
                                            <option value="cleaning">Cleaning</option>
                                            <option value="gardening">Gardening</option>
                                            <option value="hvac">HVAC</option>
                                            <option value="roofing">Roofing</option>
                                            <option value="general">General Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Search Radius (km)</label>
                                        <select class="form-select" id="searchRadius">
                                            <option value="5">5 km</option>
                                            <option value="10" selected>10 km</option>
                                            <option value="25">25 km</option>
                                            <option value="50">50 km</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">COVID Safety Filter</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="filterVaccinated">
                                            <label class="form-check-label" for="filterVaccinated">Vaccinated providers only</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="filterCertified">
                                            <label class="form-check-label" for="filterCertified">COVID-safe certified</label>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-success w-100" onclick="searchProviders()">
                                        <i class="bi bi-search"></i> Search Providers
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <!-- Map Container -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-map"></i> Service Providers Map</h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="map"></div>
                            </div>
                        </div>
                        
                        <!-- Provider Results -->
                        <div id="providerResults">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-geo-alt display-4"></i>
                                <p>Enter your location and search to find nearby service providers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Bookings Tab -->
            <div class="tab-pane fade" id="bookings" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> My Booking History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($userBookings)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-4 text-muted"></i>
                            <p class="text-muted">No bookings found. Book a service to get started!</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Service Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userBookings as $booking): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($booking['booking_ref']) ?></code></td>
                                        <td><?= htmlspecialchars(getServiceTypeDisplayName($booking['service_type']))  ?? 'General Service' ?></td>
                                        <td><?= htmlspecialchars($booking['preferred_date']) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $class = $statusClass[$booking['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $class ?>"><?= ucfirst($booking['status']) ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewBooking('<?= $booking['booking_ref'] ?>')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let markers = [];
        
        // Initialize map
        document.addEventListener('DOMContentLoaded', function() {
            map = L.map('map').setView([-34.9285, 138.6007], 13); // Adelaide, South Australia
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
        });
        
        // Get user's location
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        document.getElementById('userLat').value = lat;
                        document.getElementById('userLng').value = lng;
                        document.getElementById('userLocation').value = lat.toFixed(4) + ', ' + lng.toFixed(4);
                        map.setView([lat, lng], 13);
                        L.marker([lat, lng]).addTo(map).bindPopup('Your Location').openPopup();
                    },
                    function(error) {
                        alert('Unable to get location: ' + error.message);
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        
        // Search providers
        function searchProviders() {
            const lat = document.getElementById('userLat').value;
            const lng = document.getElementById('userLng').value;
            const category = document.getElementById('serviceCategory').value;
            const radius = document.getElementById('searchRadius').value;
            
            if (!lat || !lng) {
                alert('Please enter your location or use GPS.');
                return;
            }
            
            // AJAX call to get providers
            fetch(`api/providers.php?lat=${lat}&lng=${lng}&radius=${radius}&category=${category}`)
                .then(response => response.json())
                .then(data => displayProviders(data))
                .catch(error => console.error('Error:', error));
        }
        
        // Display providers on map and list
        function displayProviders(providers) {
            // Clear existing markers
            markers.forEach(m => map.removeLayer(m));
            markers = [];
            
            let html = '';
            providers.forEach(p => {
                // Add marker
                const marker = L.marker([p.latitude, p.longitude]).addTo(map)
                    .bindPopup(`<b>${p.business_name}</b><br>${p.service_category}`);
                markers.push(marker);
                
                // Build card HTML
                html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6>${p.business_name}</h6>
                            <span class="badge bg-primary">${p.distance} km</span>
                        </div>
                        <p class="text-muted small mb-2">${p.address}</p>
                        <div class="mb-2">
                            ${p.covid_vaccinated ? '<span class="badge bg-success me-1"><i class="bi bi-shield-check"></i> Vaccinated</span>' : ''}
                            ${p.covid_safe_certified ? '<span class="badge bg-info"><i class="bi bi-patch-check"></i> COVID-Safe</span>' : ''}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Available slots: ${p.available_slots}</small>
                            <button class="btn btn-sm btn-primary" onclick="bookProvider(${p.provider_id})">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>`;
            });
            
            document.getElementById('providerResults').innerHTML = html || '<p class="text-center text-muted">No providers found in this area.</p>';
        }

                // Add placeholder provider markers around Adelaide
        const adelaideCenter = [-34.9285, 138.6007];
        const placeholderProviders = [
            { lat: -34.9185, lng: 138.6007, name: 'Provider 1 - North' },
            { lat: -34.9385, lng: 138.6007, name: 'Provider 2 - South' },
            { lat: -34.9285, lng: 138.5907, name: 'Provider 3 - West' },
            { lat: -34.9285, lng: 138.6107, name: 'Provider 4 - East' },
            { lat: -34.9235, lng: 138.5957, name: 'Provider 5 - Northwest' },
            { lat: -34.9235, lng: 138.6057, name: 'Provider 6 - Northeast' },
            { lat: -34.9335, lng: 138.5957, name: 'Provider 7 - Southwest' },
            { lat: -34.9335, lng: 138.6057, name: 'Provider 8 - Southeast' }
        ];

        placeholderProviders.forEach(provider => {
            const marker = L.marker([provider.lat, provider.lng]).addTo(map)
                .bindPopup(`<b>${provider.name}</b><br>Sample service provider<br><small>Click to book</small>`);
            markers.push(marker);
        });
        
        function bookProvider(providerId) {
            window.location.href = 'book_provider.php?id=' + providerId;
        }
        
        function viewBooking(ref) {
            window.location.href = 'booking_details.php?ref=' + ref;
        }
    </script>
</body>
</html>
