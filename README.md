# Biometric Attendance System with PHP/XAMPP & Flexcode SDK

A modern PHP-based biometric attendance system with fingerprint authentication.

## Prerequisites

- **XAMPP** - For Apache and MySQL server - [Download XAMPP](https://www.apachefriends.org/index.html)
- **FlexCode SDK** - For fingerprint device integration - [FlexCode](https://flexcodesdk.com/)
- **Digital Persona 4500 Fingerprint Scanner** - [Amazon Link](https://www.amazon.com/Digital-Persona-U-are-U-4500-Fingerprint/dp/B075RSS2RQ)

## Installation Guide

### 1. Setup XAMPP
1. Install and start XAMPP
2. Start Apache and MySQL services
3. Place this project in the `htdocs` folder

### 2. Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `bio_attendance`
3. Import the SQL file from `databases/bio_app.sql`

### 3. Configure FlexCode SDK
1. Add your fingerprint device Serial Number (SN) to the device table
2. Add your FlexCode license details to the device table

### 4. Access the Application
- Open your browser and go to `http://localhost/bio_attendance`
- Default login credentials:
  - Username: admin
  - Password: admin123

## Project Structure

This is a **PHP-based application** that uses:
- PHP for server-side logic
- MySQL for database
- Bootstrap 3 for frontend styling
- jQuery for JavaScript functionality
- FlexCode SDK for fingerprint integration

## Features

- User authentication system
- Student registration and management
- Fingerprint enrollment and verification
- Attendance logging and reporting
- Device management
- Modern responsive UI

## Usage

1. **Add Students**: Register students with their matric numbers, departments, and levels
2. **Enroll Fingerprints**: Use the fingerprint scanner to enroll student fingerprints
3. **Mark Attendance**: Students can mark attendance using their enrolled fingerprints
4. **View Reports**: Access attendance logs and generate reports

## Important Notes

- Ensure your fingerprint scanner is connected before enrolling fingerprints
- This system requires FlexCode SDK license for fingerprint functionality
- The application runs on Apache server (via XAMPP)
- No Node.js or npm dependencies required

## Troubleshooting

- If you see npm errors, ignore them - this is a PHP project
- Ensure XAMPP services are running
- Check database connection settings in `config/` files
- Verify fingerprint device drivers are installed