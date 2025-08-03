<?php

class TaskController {
    private $taskModel;

    public function __construct() {
        $this->taskModel = new Task();
    }

    /**
     * Create a new task (POST /tasks)
     */
    public function create() {
        try {
            $input = $this->getJsonInput();
            
            // Validate input
            $errors = $this->taskModel->validate($input);
            if (!empty($errors)) {
                $this->sendErrorResponse('Validation failed', 400, $errors);
                return;
            }

            $task = $this->taskModel->create($input);
            
            $this->sendSuccessResponse($task, 201);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Failed to create task: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get all tasks (GET /tasks)
     */
    public function index() {
        try {
            $status = $_GET['status'] ?? null;
            
            // Validate status filter if provided
            if ($status && !in_array($status, Task::getValidStatuses())) {
                $this->sendErrorResponse(
                    'Invalid status filter. Valid values: ' . implode(', ', Task::getValidStatuses()), 
                    400
                );
                return;
            }

            $tasks = $this->taskModel->findAll($status);
            
            $this->sendSuccessResponse($tasks);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Failed to retrieve tasks: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get single task (GET /tasks/{id})
     */
    public function show($id) {
        try {
            if (!$this->isValidId($id)) {
                $this->sendErrorResponse('Invalid task ID', 400);
                return;
            }

            $task = $this->taskModel->findById($id);
            
            if (!$task) {
                $this->sendErrorResponse('Task not found', 404);
                return;
            }

            $this->sendSuccessResponse($task);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Failed to retrieve task: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update a task (PUT /tasks/{id})
     */
    public function update($id) {
        try {
            if (!$this->isValidId($id)) {
                $this->sendErrorResponse('Invalid task ID', 400);
                return;
            }

            $input = $this->getJsonInput();
            
            // Validate input for update
            $errors = $this->taskModel->validate($input, true);
            if (!empty($errors)) {
                $this->sendErrorResponse('Validation failed', 400, $errors);
                return;
            }

            $task = $this->taskModel->update($id, $input);
            
            if (!$task) {
                $this->sendErrorResponse('Task not found', 404);
                return;
            }

            $this->sendSuccessResponse($task);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Failed to update task: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Partially update a task (PATCH /tasks/{id})
     * PATCH allows partial updates - only provided fields are updated
     */
    public function patch($id) {
        try {
            if (!$this->isValidId($id)) {
                $this->sendErrorResponse('Invalid task ID', 400);
                return;
            }

            // Check if task exists first
            $existingTask = $this->taskModel->findById($id);
            if (!$existingTask) {
                $this->sendErrorResponse('Task not found', 404);
                return;
            }

            $input = $this->getJsonInput();
            
            // For PATCH, we only validate fields that are provided
            if (!empty($input)) {
                $errors = $this->taskModel->validate($input, true);
                if (!empty($errors)) {
                    $this->sendErrorResponse('Validation failed', 400, $errors);
                    return;
                }
            }

            $task = $this->taskModel->update($id, $input);
            
            $this->sendSuccessResponse($task);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Failed to patch task: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a task (DELETE /tasks/{id}) - Bonus endpoint
     */
    public function delete($id) {
        try {
            if (!$this->isValidId($id)) {
                $this->sendErrorResponse('Invalid task ID', 400);
                return;
            }

            // Check if task exists
            $task = $this->taskModel->findById($id);
            if (!$task) {
                $this->sendErrorResponse('Task not found', 404);
                return;
            }

            $this->taskModel->delete($id);
            
            $this->sendSuccessResponse(['message' => 'Task deleted successfully']);
            
        } catch (Exception $e) {
            $this->sendErrorResponse('Failed to delete task: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get JSON input from request body
     */
    private function getJsonInput() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }
        
        return $input ?: [];
    }

    /**
     * Validate ID parameter
     */
    private function isValidId($id) {
        return is_numeric($id) && $id > 0;
    }

    /**
     * Send success response
     */
    private function sendSuccessResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Send error response
     */
    private function sendErrorResponse($message, $statusCode = 400, $details = null) {
        http_response_code($statusCode);
        
        $response = [
            'success' => false,
            'error' => $message,
            'code' => $statusCode
        ];
        
        if ($details) {
            $response['details'] = $details;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
}
