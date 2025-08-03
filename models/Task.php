<?php

class Task {
    private $db;
    private $table = 'tasks';
    
    // Valid status values
    const VALID_STATUSES = ['pending', 'in-progress', 'completed'];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    /**
     * Create a new task
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (title, description, status, created_at, updated_at) 
                VALUES (:title, :description, :status, datetime('now'), datetime('now'))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':status' => $data['status'] ?? 'pending'
        ]);
        
        $id = $this->db->lastInsertId();
        return $this->findById($id);
    }

    /**
     * Get all tasks with optional status filter
     */
    public function findAll($status = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Find task by ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    /**
     * Update a task
     */
    public function update($id, $data) {
        // First check if task exists
        $existingTask = $this->findById($id);
        if (!$existingTask) {
            return false;
        }

        $sql = "UPDATE {$this->table} 
                SET title = :title, 
                    description = :description, 
                    status = :status, 
                    updated_at = datetime('now')
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'] ?? $existingTask['title'],
            ':description' => $data['description'] ?? $existingTask['description'],
            ':status' => $data['status'] ?? $existingTask['status']
        ]);
        
        if ($result) {
            return $this->findById($id);
        }
        
        return false;
    }

    /**
     * Delete a task
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Validate task data
     */
    public function validate($data, $isUpdate = false) {
        $errors = [];

        // Title validation
        if (!$isUpdate || isset($data['title'])) {
            if (empty($data['title']) || !is_string($data['title'])) {
                $errors[] = 'Title is required and must be a string';
            } elseif (strlen($data['title']) > 255) {
                $errors[] = 'Title must not exceed 255 characters';
            }
        }

        // Description validation
        if (isset($data['description']) && !is_string($data['description'])) {
            $errors[] = 'Description must be a string';
        }

        // Status validation
        if (isset($data['status'])) {
            if (!in_array($data['status'], self::VALID_STATUSES)) {
                $errors[] = 'Status must be one of: ' . implode(', ', self::VALID_STATUSES);
            }
        }

        return $errors;
    }

    /**
     * Get valid status values
     */
    public static function getValidStatuses() {
        return self::VALID_STATUSES;
    }
}
