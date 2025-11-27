<?php
/**
 * Stripe Payment Configuration for HomeServices
 * 
 * This file contains Stripe API configuration for payment processing.
 * Uses Stripe sandbox/test API keys for development.
 * 
 * IMPORTANT SECURITY NOTES:
 * - Never commit real API keys to public repositories
 * - Use environment variables in production
 * - Keep this file out of public directory
 * - Test keys start with 'pk_test_' and 'sk_test_'
 * - Live keys start with 'pk_live_' and 'sk_live_'
 */

return [
    // Stripe API Keys (Test/Sandbox Mode)
    'publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_YOUR_PUBLISHABLE_KEY_HERE',
    'secret_key' => getenv('STRIPE_SECRET_KEY') ?: 'sk_test_YOUR_SECRET_KEY_HERE',
    
    // API Version
    'api_version' => '2023-10-16',
    
    // Currency Settings
    'currency' => 'usd',
    'currency_symbol' => '$',
    
    // Payment Settings
    'payment_methods' => ['card'], // Add 'alipay', 'wechat_pay', etc. as needed
    'capture_method' => 'automatic', // or 'manual'
    
    // Webhook Settings
    'webhook_secret' => getenv('STRIPE_WEBHOOK_SECRET') ?: 'whsec_YOUR_WEBHOOK_SECRET',
    'webhook_events' => [
        'payment_intent.succeeded',
        'payment_intent.payment_failed',
        'charge.succeeded',
        'charge.failed',
        'charge.refunded'
    ],
    
    // Application Settings
    'app_name' => 'HomeServices',
    'app_url' => getenv('APP_URL') ?: 'http://localhost',
    
    // Success/Cancel URLs
    'success_url' => getenv('APP_URL') . '/public/payment_success.php',
    'cancel_url' => getenv('APP_URL') . '/public/payment_cancel.php',
    
    // Sandbox Mode Flag
    'sandbox_mode' => true, // Set to false in production
    
    // Receipt Email
    'send_receipt' => true,
    
    // Statement Descriptor (appears on customer's credit card statement)
    'statement_descriptor' => 'HomeServices',
    
    // Metadata (useful for tracking)
    'metadata' => [
        'integration' => 'homeservices_php',
        'environment' => getenv('APP_ENV') ?: 'development'
    ]
];
