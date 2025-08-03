<?php
/**
 * This script initializes the SQLite database and creates necessary tables
 */

require_once __DIR__ . '/Database.php';

try {
    echo "Initializing database...\n";
    
    // Initialize database and create tables
    Database::initialize();
    
    echo "Database initialized successfully!\n";
    echo "Tables created:\n";
    echo "- tasks (id, title, description, status, created_at, updated_at)\n";
    
    // Optional: Insert sample data for testing
    $db = Database::getInstance()->getConnection();
    
    // Check if we already have data
    $stmt = $db->query("SELECT COUNT(*) as count FROM tasks");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        echo "\nInserting sample data...\n";
        
        $sampleTasks = [
            [
                'title' => 'Setup development environment',
                'description' => 'Install PHP, configure database, and setup project structure',
                'status' => 'completed'
            ],
            [
                'title' => 'Implement API endpoints',
                'description' => 'Create REST API endpoints for task management',
                'status' => 'in-progress'
            ],
            [
                'title' => 'Write documentation',
                'description' => 'Create comprehensive README and API documentation',
                'status' => 'pending'
            ]
        ];
        
        $stmt = $db->prepare("
            INSERT INTO tasks (title, description, status, created_at, updated_at) 
            VALUES (:title, :description, :status, datetime('now'), datetime('now'))
        ");
        
        foreach ($sampleTasks as $task) {
            $stmt->execute($task);
        }
        
        echo "Sample data inserted successfully!\n";
    }
    
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
}
