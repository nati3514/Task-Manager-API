<?php

// Parse the request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$uri = parse_url($requestUri, PHP_URL_PATH);

// Remove leading slash and split into segments
$segments = explode('/', trim($uri, '/'));

// Initialize controller
$taskController = new TaskController();

// Route matching
switch ($requestMethod) {
    case 'POST':
        if ($segments[0] === 'tasks' && count($segments) === 1) {
            // POST /tasks - Create new task
            $taskController->create();
        }
        break;
        
    case 'GET':
        if ($segments[0] === 'tasks') {
            if (count($segments) === 1) {
                // GET /tasks - Get all tasks (with optional status filter)
                $taskController->index();
            } elseif (count($segments) === 2 && is_numeric($segments[1])) {
                // GET /tasks/{id} - Get single task
                $taskController->show($segments[1]);
            }
        }
        break;
        
    case 'PUT':
        if ($segments[0] === 'tasks' && count($segments) === 2 && is_numeric($segments[1])) {
            // PUT /tasks/{id} - Update task (full update)
            $taskController->update($segments[1]);
        }
        break;
        
    case 'PATCH':
        if ($segments[0] === 'tasks' && count($segments) === 2 && is_numeric($segments[1])) {
            // PATCH /tasks/{id} - Partial update task
            $taskController->patch($segments[1]);
        }
        break;
        
    case 'DELETE':
        if ($segments[0] === 'tasks' && count($segments) === 2 && is_numeric($segments[1])) {
            // DELETE /tasks/{id} - Delete task (bonus endpoint)
            $taskController->delete($segments[1]);
        }
        break;
}

