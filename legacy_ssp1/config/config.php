<?php

// Environment toggle (set to 'prod' on deployment)
$ENV = getenv('BLENDUP_ENV') ?: 'dev';

if ($ENV === 'prod') {
  ini_set('display_errors', '0');
  ini_set('display_startup_errors', '0');
  ini_set('log_errors', '1');
  // Optional: set a writable path
  // ini_set('error_log', __DIR__ . '/../storage/php-error.log');
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
} else {
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
}


// Prevent config from being loaded twice

if (defined('BLENDUP_CONFIG_LOADED')) { return; }
define('BLENDUP_CONFIG_LOADED', true);


// Start session if not already started

if (session_status() === PHP_SESSION_NONE) session_start();


// Base URL constant for project

if (!defined('BASE_URL')) define('BASE_URL', '/blendupfinal/');


// Database credentials

$DB_HOST = 'localhost';
$DB_NAME = 'blendup_final';
$DB_USER = 'root';
$DB_PASS = 'root'; 


// Database connection (PDO)

try {
  if (!isset($pdo) || !($pdo instanceof PDO)) {
    $pdo = new PDO(
      "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
      $DB_USER,
      $DB_PASS,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch rows as associative arrays
      ]
    );
  }
} catch (Exception $e) {
  // In prod, avoid leaking details
  if ($ENV === 'prod') {
    error_log('DB connection failed: '.$e->getMessage());
    http_response_code(500);
    die('Service temporarily unavailable.');
  } else {
    die('DB connection failed: ' . $e->getMessage());
  }
}




// Authentication helpers

// Check if a user is logged in
if (!function_exists('is_logged_in')) {
  function is_logged_in() { return isset($_SESSION['user']); }
}

// Check if current user is admin
if (!function_exists('is_admin')) {
  function is_admin() { 
    return is_logged_in() && (($_SESSION['user']['role'] ?? '') === 'admin'); 
  }
}
