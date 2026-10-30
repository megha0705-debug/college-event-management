<?php
/**
 * Logout Handler
 * CampusPulse — College Event Management Website
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Log out user
logout_user();

// Re-start session for flash notification
session_start();
flash_set('info', 'You have been logged out safely. See you soon!');
header("Location: " . base_url('login.php'));
exit;

