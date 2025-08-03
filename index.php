<?php
/**
 * Task Manager API - Main Entry Point
 */

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Autoload classes
spl_autoload_register(function ($class) {
    $directories = ['controllers', 'models', 'database', 'config'];
    
    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Initialize database
require_once 'database/Database.php';
Database::initialize();

// Load routes
require_once 'routes/api.php';

// Handle 404 for unmatched routes
http_response_code(404);
echo json_encode([
    'success' => false,
    'error' => 'Endpoint not found',
    'code' => 404
]);
