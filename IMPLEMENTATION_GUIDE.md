# HomeServices Implementation Guide

This guide provides step-by-step instructions to implement the required features for the HomeServices project based on the PDF requirements.

## ✅ Completed Steps

1. **Database Schema** (`database_updates.sql`) - Run this SQL file to update your database
2. **OAuth Configuration** (`config/oauth.php`) - Configure Google/Facebook login credentials
3. **Stripe Configuration** (`config/stripe.php`) - Configure Stripe payment sandbox API keys

## 🚀 Next Implementation Steps

### Step 1: Setup Environment Variables

Create a `.env` file in the root directory (DO NOT commit to GitHub):

```env
# Application
APP_URL=http://localhost
APP_ENV=development

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here

# Facebook OAuth
FACEBOOK_APP_ID=your_facebook_app_id_here
FACEBOOK_APP_SECRET=your_facebook_app_secret_here

# Stripe (Test Keys)
STRIPE_PUBLISHABLE_KEY=pk_test_your_publishable_key
STRIPE_SECRET_KEY=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# Database
DB_HOST=localhost
DB_NAME=homeservices
DB_USER=your_db_user
DB_PASS=your_db_password
```

### Step 2: Install Dependencies

```bash
# Install Stripe PHP Library
composer require stripe/stripe-php

# Or download manually and include
# https://github.com/stripe/stripe-php/releases
```

### Step 3: Run Database Updates

```bash
mysql -u your_user -p homeservices < database_updates.sql
```

### Step 4: Create OAuth Files

#### A. Create `public/oauth_login.php`

Handles OAuth login initiation:
- Generates state token for security
- Redirects to Google/Facebook auth page
- Stores state in session

#### B. Create `public/oauth_callback.php`

Handles OAuth callback:
- Verifies state token
- Exchanges authorization code for access token
- Fetches user info from provider
- Creates/updates user in database
- Logs user in

### Step 5: Update User Model

Add to `app/models/User.php`:
- `loginWithOAuth($provider, $oauthId, $email, $name, $profilePicture)`
- `findByOAuth($provider, $oauthId)`
- `linkOAuthAccount($userId, $provider, $oauthId)`

### Step 6: Update Login/Register Pages

#### Update `public/login.php`:
Add OAuth buttons:
```html
<a href="oauth_login.php?provider=google" class="btn-google">
  <i class="fab fa-google"></i> Sign in with Google
</a>
<a href="oauth_login.php?provider=facebook" class="btn-facebook">
  <i class="fab fa-facebook"></i> Sign in with Facebook
</a>
```

#### Update `public/register.php`:
Add required fields from PDF:
- Age
- Mobile
- Country
- Language Preferred
- COVID-19 Vaccinated (checkbox)
- Trade
- Profession

### Step 7: Create Payment System

#### A. Create `app/models/Payment.php`

Methods:
- `createPaymentIntent($amount, $userId, $appointmentId, $description)`
- `updatePaymentStatus($paymentIntentId, $status)`
- `getPaymentsByUser($userId)`
- `refundPayment($paymentId, $amount)`

#### B. Create `app/controllers/PaymentController.php`

Methods:
- `createCheckoutSession($appointmentId)`
- `handleWebhook()` - Process Stripe webhook events
- `confirmPayment($paymentIntentId)`

#### C. Create `public/payment_checkout.php`

Stripe Checkout integration page

#### D. Create `public/payment_success.php` and `public/payment_cancel.php`

### Step 8: Implement Service Packages

#### A. Create `app/models/ServicePackage.php`

Methods:
- `getAllPackages()`
- `getPackageById($id)`
- `calculateFinalPrice($basePrice, $discountPercentage)`

#### B. Create `public/service_packages.php`

Display service packages with:
- Package name and description
- Services included
- Base price, discount, final price
- Duration
- Photos
- "Book Now" button

### Step 9: Update Service Booking System

#### Update `public/service.php`:
Add fields:
- Preferred dates
- Duration
- Budget/Cost preference
- Special instructions
- Location (address, city)
- COVID restrictions info

### Step 10: Add Location Services

#### Option 1: Google Maps API Integration
```javascript
// Add Google Maps JavaScript API
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
```

#### Option 2: Use HTML5 Geolocation
```javascript
navigator.geolocation.getCurrentPosition(function(position) {
  const lat = position.coords.latitude;
  const lng = position.coords.longitude;
});
```

### Step 11: Implement Security Measures

1. **Input Validation & Sanitization**
   - Use `filter_var()` for email validation
   - Use `htmlspecialchars()` for output
   - Validate all user inputs

2. **SQL Injection Prevention**
   - Use prepared statements (already implemented)
   - Never concatenate user input into SQL

3. **CSRF Protection**
   - Add CSRF tokens to forms
   - Verify tokens on submission

4. **Password Security**
   - Already using `PASSWORD_DEFAULT` ✓
   - Ensure minimum 8 characters

5. **Session Security**
   - Regenerate session ID after login
   - Set secure session cookie parameters

### Step 12: Add Additional Features (From PDF)

1. **Confirmation System**
   - Email confirmation after booking
   - QR code for appointments

2. **Reminder System**
   - Email reminders before appointment

3. **Voucher/Discount System** (Database tables already created)
   - Apply voucher codes at checkout
   - Validate voucher expiry and usage limits

## 📁 File Structure Summary

```
HomeServices/
├── app/
│   ├── controllers/
│   │   ├── PaymentController.php (NEW)
│   │   ├── ServicePackageController.php (NEW)
│   │   └── (existing controllers)
│   └── models/
│       ├── Payment.php (NEW)
│       ├── ServicePackage.php (NEW)
│       ├── Voucher.php (NEW)
│       └── (update User.php)
├── config/
│   ├── oauth.php ✓
│   ├── stripe.php ✓
│   └── Database.php (existing)
├── public/
│   ├── oauth_login.php (NEW)
│   ├── oauth_callback.php (NEW)
│   ├── payment_checkout.php (NEW)
│   ├── payment_success.php (NEW)
│   ├── payment_cancel.php (NEW)
│   ├── service_packages.php (NEW)
│   └── (update login.php, register.php, service.php)
├── database_updates.sql ✓
├── .env (CREATE - DO NOT COMMIT)
└── IMPLEMENTATION_GUIDE.md ✓
```

## 🔑 Getting OAuth Credentials

### Google OAuth:
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs: `http://localhost/public/oauth_callback.php?provider=google`

### Facebook OAuth:
1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app
3. Add Facebook Login product
4. Configure OAuth redirect URIs: `http://localhost/public/oauth_callback.php?provider=facebook`

### Stripe:
1. Go to [Stripe Dashboard](https://dashboard.stripe.com/)
2. Get test API keys from Developers > API keys
3. Use test mode (keys starting with `pk_test_` and `sk_test_`)

## 🧪 Testing

### Test OAuth Login:
1. Click "Sign in with Google" on login page
2. Authorize the application
3. Should redirect back and auto-login

### Test Stripe Payment:
1. Use test card: `4242 4242 4242 4242`
2. Use any future expiry date
3. Use any 3-digit CVC
4. Use any ZIP code

## 🎯 Key Features Implementation Priority

1. **HIGH PRIORITY** (Essential)
   - OAuth login ✓ (config ready)
   - User registration with extended fields
   - Stripe payment integration ✓ (config ready)
   - Service booking system

2. **MEDIUM PRIORITY**
   - Service packages
   - Location-based services
   - Voucher system

3. **LOW PRIORITY** (Nice to have)
   - QR codes
   - Email reminders
   - Review system

## 📞 Support

For questions or issues:
1. Check existing code in `app/` directory
2. Refer to configuration files in `config/`
3. Review database schema in `database_updates.sql`

## ⚠️ Security Notes

1. **NEVER** commit `.env` file or real API keys
2. Always use HTTPS in production
3. Keep Stripe in test mode until fully tested
4. Validate and sanitize all user inputs
5. Use prepared statements for database queries
6. Implement rate limiting for login attempts
7. Keep dependencies updated

---

**Made simple, secure, and robust** ✨
