<?php
/**
 * Email Configuration for Gudang System
 * Configure SMTP settings for email notifications
 */

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 't43787659@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'byct nwmi iuen mnbm');    // Gmail App Password
define('SMTP_ENCRYPTION', 'tls');

// Email Settings
define('FROM_EMAIL', 't43787659@gmail.com');    // Your Gmail address
define('FROM_NAME', 'Sistem Gudang Material');
define('REPLY_TO_EMAIL', 't43787659@gmail.com'); // Your Gmail address

// System URLs
define('BASE_URL', 'http://localhost/gudang');    // Change this to your actual domain

// Admin Email (receives all notifications)
define('ADMIN_EMAIL', 't43787659@gmail.com');

/*
INSTRUCTIONS FOR GMAIL SETUP:
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security > 2-Step Verification > App passwords
   - Generate a password for "Mail"
3. Use the generated app password in SMTP_PASSWORD above
4. Replace 'your-email@gmail.com' with your actual Gmail address
*/
?>