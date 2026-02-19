# Nominee OTP Login System Documentation

## Overview
The nominee OTP (One-Time Password) login system allows nominees to securely log in to the World Publications Awards platform using their email address and a temporary OTP sent to their email, instead of traditional username/password credentials.

## How It Works

### 1. Nominee Registration & Account Creation
- When a nominee accesses the login page, they can click on "Nominee Login"
- They enter their email address associated with their nominee profile
- The system checks if a nominee exists with that email in the `nominees` table
- If found and no corresponding user account exists, the system creates a user account with the `nominee` role

### 2. OTP Generation and Delivery
- A 6-digit numeric OTP is generated using the `generateOTP()` function
- The OTP is stored in the `otp_tokens` table with expiration time (10 minutes)
- The OTP is sent to the nominee's email using the `sendOTPEmailEnhanced()` function
- In development, if email sending fails, the OTP is displayed for testing purposes

### 3. OTP Verification
- The nominee enters the received OTP in the verification form
- The system validates the OTP against the stored token in the database
- If valid and not expired, the nominee is logged in and redirected to the nominee dashboard
- The OTP is marked as used after successful validation

## Key Components

### Database Tables
- `nominees` - Contains nominee information including email addresses
- `users` - Contains user accounts with roles (including 'nominee' role)
- `otp_tokens` - Temporarily stores OTP codes with expiration and usage status

### Core Functions
- `ensureNomineeUserAccount($email)` - Creates user account if nominee exists but has no account
- `generateOTP()` - Generates a 6-digit numeric OTP
- `storeOTP($email, $otp)` - Stores OTP in database with expiration
- `validateOTP($email, $otp)` - Validates the OTP and marks it as used
- `sendOTPEmailEnhanced($email, $otp)` - Sends OTP to nominee's email

### Key Files
- `login.php` - Main login page with OTP flow implementation
- `nominee-dashboard.php` - Dashboard page for logged-in nominees
- `includes/helpers.php` - Contains all OTP-related helper functions

## Security Features
- OTPs expire after 10 minutes
- Each OTP can only be used once
- Proper SQL injection prevention with prepared statements
- Session-based authentication after successful OTP verification

## Email Configuration
For production environments, configure proper SMTP settings in your server to ensure reliable OTP delivery. The system falls back to the basic PHP `mail()` function if advanced email libraries aren't available.

## Testing
The system includes a test script (`test_nominee_login.php`) that verifies all components are properly set up and functional.

## User Flow
1. Nominee visits login page
2. Clicks "Nominee Login" option
3. Enters email address
4. Receives OTP via email
5. Enters OTP in verification form
6. Gets redirected to nominee dashboard upon successful verification