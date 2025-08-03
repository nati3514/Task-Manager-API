# Task Manager API

A simple yet powerful Task Management REST API built with pure PHP, demonstrating core PHP skills and best practices.

## Features

- ✅ Full CRUD operations for tasks
- ✅ RESTful API design
- ✅ SQLite database with PDO
- ✅ MVC architecture
- ✅ Docker support
- ✅ Input validation & error handling
- ✅ Task filtering by status
- ✅ Comprehensive API documentation

## Project Structure

```
task-manager-api/
├── controllers/        # API controllers
├── models/            # Data models
├── database/          # Database setup and migrations
├── routes/            # Route definitions
├── config/            # Configuration files
├── tests/             # API test scripts
├── docker/            # Docker configuration
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Quick Start

### Using Docker (Recommended)

1. Clone the repository:
```bash
git clone <repository-url>
cd task-manager-api
```

2. Build and run with Docker Compose:
```bash
docker-compose up --build
```

3. The API will be available at `http://localhost:8080`

### Manual Setup

1. Ensure PHP 8.0+ and SQLite3 are installed
2. Clone the repository
3. Initialize the database:
```bash
php database/init.php
```
4. Start the PHP built-in server:
```bash
php -S localhost:8080 index.php
```

## API Endpoints

### Create Task
```http
POST /tasks
Content-Type: application/json

{
    "title": "Complete project",
    "description": "Finish the task manager API",
    "status": "pending"
}
```

### Get All Tasks
```http
GET /tasks
```

### Get All Tasks with Filter
```http
GET /tasks?status=completed
```

### Get Task by ID
```http
GET /tasks/1
```

### Update Task (Full Update)
```http
PUT /tasks/1
Content-Type: application/json

{
    "title": "Updated title",
    "description": "Updated description",
    "status": "completed"
}
```

### Partial Update Task
```http
PATCH /tasks/1
Content-Type: application/json

{
    "status": "completed"
}
```

### Delete Task
```http
DELETE /tasks/1
```

## Task Status Values

- `pending` - Task is created but not started
- `in-progress` - Task is being worked on
- `completed` - Task is finished

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Task title",
        "description": "Task description",
        "status": "pending",
        "created_at": "2024-01-01 12:00:00",
        "updated_at": "2024-01-01 12:00:00"
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": "Error message",
    "code": 400
}
```

## Testing

### Automated Testing

**Option 1: PowerShell (Windows)**
```powershell
# Run the PowerShell test script
.\tests\api_test.ps1
```

**Option 2: Bash (Linux/Mac/Git Bash)**
```bash
# Make executable and run
chmod +x tests/api_test.sh
./tests/api_test.sh
```

**Option 3: Postman**
Import the collection from `tests/postman_collection.json`

### Manual Testing Examples

**PowerShell:**
```powershell
# Get all tasks
Invoke-RestMethod -Uri "http://localhost:8080/tasks" -Method GET

# Create a task
$body = @{
    title = "Test Task"
    description = "Testing the API"
    status = "pending"
} | ConvertTo-Json
Invoke-RestMethod -Uri "http://localhost:8080/tasks" -Method POST -Body $body -ContentType "application/json"

# Update task status (PATCH)
$patchBody = @{ status = "completed" } | ConvertTo-Json
Invoke-RestMethod -Uri "http://localhost:8080/tasks/1" -Method PATCH -Body $patchBody -ContentType "application/json"
```

**cURL:**
```bash
# Get all tasks
curl -X GET "http://localhost:8080/tasks"

# Create a task
curl -X POST "http://localhost:8080/tasks" \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Task","description":"Testing","status":"pending"}'

# Partial update (PATCH)
curl -X PATCH "http://localhost:8080/tasks/1" \
  -H "Content-Type: application/json" \
  -d '{"status":"completed"}'
```

## Development

This project follows MVC architecture principles:

- **Models**: Handle data logic and database operations
- **Controllers**: Process HTTP requests and responses
- **Routes**: Define API endpoints and route to controllers
- **Database**: SQLite with PDO for data persistence

## Technologies Used

- PHP 8.0+
- SQLite3
- PDO (PHP Data Objects)
- Docker & Docker Compose
- JSON for data exchange

## License

MIT License
