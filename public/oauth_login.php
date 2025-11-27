<?php
/**
 * OAuth Login Initiator
 * Handles Google and Facebook OAuth login initiation
 */

session_start();

// Load OAuth configuration
$oauth_config = require_once __DIR__ . '/../config/oauth.php';

// Get provider from URL parameter
$provider = isset($_GET['provider']) ? strtolower($_GET['provider']) : '';

// Validate provider
if (!in_array($provider, ['google', 'facebook'])) {
    die('Invalid OAuth provider. Supported: google, facebook');
}

// Get provider configuration
$config = $oauth_config[$provider];

// Generate and store state token for CSRF protection
$state = bin2hex(random_bytes(32));
$_SESSION['oauth_state'] = $state;
$_SESSION['oauth_provider'] = $provider;

// Build authorization URL
if ($provider === 'google') {
    $params = [
        'client_id' => $config['client_id'],
        'redirect_uri' => $config['redirect_uri'],
        'response_type' => 'code',
        'scope' => implode(' ', $config['scopes']),
        'state' => $state,
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    $auth_url = $config['auth_url'] . '?' . http_build_query($params);
    
} elseif ($provider === 'facebook') {
    $params = [
        'client_id' => $config['app_id'],
        'redirect_uri' => $config['redirect_uri'],
        'state' => $state,
        'scope' => implode(',', $config['scopes'])
    ];
    $auth_url = $config['auth_url'] . '?' . http_build_query($params);
}

// Redirect to OAuth provider
header('Location: ' . $auth_url);
exit;
?>
