<?php
/**
 * OAuth Configuration for HomeServices
 * 
 * This file contains OAuth configuration for Google and Facebook authentication.
 * Replace the placeholder values with your actual OAuth credentials.
 * 
 * IMPORTANT SECURITY NOTE:
 * - Never commit real OAuth credentials to public repositories
 * - Use environment variables in production
 * - Keep this file out of public directory
 */

return [
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID',
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri' => getenv('APP_URL') . '/public/oauth_callback.php?provider=google',
        'scopes' => ['email', 'profile'],
        'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        'user_info_url' => 'https://www.googleapis.com/oauth2/v2/userinfo'
    ],
    
    'facebook' => [
        'app_id' => getenv('FACEBOOK_APP_ID') ?: 'YOUR_FACEBOOK_APP_ID',
        'app_secret' => getenv('FACEBOOK_APP_SECRET') ?: 'YOUR_FACEBOOK_APP_SECRET',
        'redirect_uri' => getenv('APP_URL') . '/public/oauth_callback.php?provider=facebook',
        'scopes' => ['email', 'public_profile'],
        'auth_url' => 'https://www.facebook.com/v18.0/dialog/oauth',
        'token_url' => 'https://graph.facebook.com/v18.0/oauth/access_token',
        'user_info_url' => 'https://graph.facebook.com/v18.0/me'
    ],
    
    // Session configuration
    'session' => [
        'oauth_state_key' => 'oauth_state',
        'oauth_provider_key' => 'oauth_provider',
        'session_lifetime' => 3600 // 1 hour
    ]
];
