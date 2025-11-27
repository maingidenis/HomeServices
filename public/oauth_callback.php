<?php
/**
 * OAuth Callback Handler
 * Handles OAuth callback from Google/Facebook and logs user in
 */

session_start();

require_once __DIR__ . '/../app/models/User.php';

// Load OAuth configuration
$oauth_config = require __DIR__ . '/../config/oauth.php';

// Verify state token (CSRF protection)
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Error: Invalid state token. Possible CSRF attack.');
}

// Get authorization code
$code = isset($_GET['code']) ? $_GET['code'] : '';
if (empty($code)) {
    die('Error: No authorization code received.');
}

// Get provider from session
$provider = $_SESSION['oauth_provider'];
$config = $oauth_config[$provider];

try {
    if ($provider === 'google') {
        // Exchange code for access token
        $token_params = [
            'code' => $code,
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init($config['token_url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $token_data = json_decode($response, true);
        $access_token = $token_data['access_token'];
        
        // Get user info
        $ch = curl_init($config['user_info_url'] . '?access_token=' . $access_token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $user_data = json_decode($response, true);
        $oauth_id = $user_data['id'];
        $email = $user_data['email'];
        $name = $user_data['name'];
        $picture = isset($user_data['picture']) ? $user_data['picture'] : null;
        
    } elseif ($provider === 'facebook') {
        // Exchange code for access token
        $token_url = $config['token_url'] . '?' . http_build_query([
            'client_id' => $config['app_id'],
            'client_secret' => $config['app_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'code' => $code
        ]);
        
        $ch = curl_init($token_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $token_data = json_decode($response, true);
        $access_token = $token_data['access_token'];
        
        // Get user info
        $user_url = $config['user_info_url'] . '?fields=id,name,email,picture&access_token=' . $access_token;
        $ch = curl_init($user_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $user_data = json_decode($response, true);
        $oauth_id = $user_data['id'];
        $email = isset($user_data['email']) ? $user_data['email'] : null;
        $name = $user_data['name'];
        $picture = isset($user_data['picture']['data']['url']) ? $user_data['picture']['data']['url'] : null;
    }
    
    // Login or register user
    $userModel = new User();
    $user_id = $userModel->loginWithOAuth($provider, $oauth_id, $email, $name, $picture);
    
    if ($user_id) {
        // Save user ID in session
        $_SESSION['user_id'] = $user_id;
        
        // Clear OAuth session variables
        unset($_SESSION['oauth_state']);
        unset($_SESSION['oauth_provider']);
        
        // Redirect to dashboard
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        die('Error: Failed to login/register user.');
    }
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
