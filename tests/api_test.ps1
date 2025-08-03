# Task Manager API Test Script (PowerShell Version)
# This script tests all API endpoints with sample data

$API_BASE = "http://localhost:8080"

Write-Host "Task Manager API Test Script" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host "Testing API at: $API_BASE" -ForegroundColor Yellow
Write-Host ""

# Function to make HTTP requests and display results
function Test-Endpoint {
    param(
        [string]$Method,
        [string]$Endpoint,
        [string]$Data,
        [string]$Description
    )
    
    Write-Host "Testing: $Description" -ForegroundColor Cyan
    Write-Host "   $Method $Endpoint" -ForegroundColor Gray
    
    try {
        $uri = "$API_BASE$Endpoint"
        
        if ($Data) {
            Write-Host "   Data: $Data" -ForegroundColor Gray
            $response = Invoke-RestMethod -Uri $uri -Method $Method -Body $Data -ContentType "application/json" -ErrorAction Stop
            $statusCode = 200  # Invoke-RestMethod assumes success
        } else {
            $response = Invoke-RestMethod -Uri $uri -Method $Method -ErrorAction Stop
            $statusCode = 200
        }
        
        Write-Host "   Status: $statusCode" -ForegroundColor Green
        Write-Host "   Response:" -ForegroundColor Gray
        $response | ConvertTo-Json -Depth 10 | Write-Host
        
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "   Status: $statusCode" -ForegroundColor Red
        Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
        
        # Try to get error response body
        try {
            $errorStream = $_.Exception.Response.GetResponseStream()
            $reader = New-Object System.IO.StreamReader($errorStream)
            $errorBody = $reader.ReadToEnd()
            if ($errorBody) {
                Write-Host "   Response:" -ForegroundColor Gray
                $errorBody | ConvertFrom-Json | ConvertTo-Json -Depth 10 | Write-Host
            }
        } catch {
            # Ignore if we can't read error body
        }
    }
    
    Write-Host ""
    Start-Sleep -Seconds 1
}

Write-Host "Starting API Tests..." -ForegroundColor Green
Write-Host ""

# Test 1: Create a new task
Test-Endpoint -Method "POST" -Endpoint "/tasks" `
    -Data '{"title":"Complete project documentation","description":"Write comprehensive README and API docs","status":"pending"}' `
    -Description "Create new task"

# Test 2: Create another task
Test-Endpoint -Method "POST" -Endpoint "/tasks" `
    -Data '{"title":"Implement user authentication","description":"Add JWT-based authentication system","status":"in-progress"}' `
    -Description "Create another task"

# Test 3: Get all tasks
Test-Endpoint -Method "GET" -Endpoint "/tasks" `
    -Description "Get all tasks"

# Test 4: Get tasks with status filter
Test-Endpoint -Method "GET" -Endpoint "/tasks?status=pending" `
    -Description "Get tasks with status filter (pending)"

# Test 5: Get tasks with status filter
Test-Endpoint -Method "GET" -Endpoint "/tasks?status=completed" `
    -Description "Get tasks with status filter (completed)"

# Test 6: Get single task by ID
Test-Endpoint -Method "GET" -Endpoint "/tasks/1" `
    -Description "Get task by ID (1)"

# Test 7: Update a task
Test-Endpoint -Method "PUT" -Endpoint "/tasks/1" `
    -Data '{"title":"Complete project documentation","description":"Write comprehensive README and API docs - Updated","status":"completed"}' `
    -Description "Update task (ID: 1)"

# Test 8: Get updated task
Test-Endpoint -Method "GET" -Endpoint "/tasks/1" `
    -Description "Get updated task (ID: 1)"

# Test 9: Test PATCH (partial update)
Test-Endpoint -Method "PATCH" -Endpoint "/tasks/2" `
    -Data '{"status":"completed"}' `
    -Description "Partial update task (ID: 2) - change status only"

# Test 10: Get patched task
Test-Endpoint -Method "GET" -Endpoint "/tasks/2" `
    -Description "Get patched task (ID: 2)"

# Test 11: Test DELETE task
Test-Endpoint -Method "DELETE" -Endpoint "/tasks/3" `
    -Description "Delete task (ID: 3)"

# Test 12: Verify task was deleted
Test-Endpoint -Method "GET" -Endpoint "/tasks/3" `
    -Description "Verify deleted task returns 404 (ID: 3)"

# Test 13: Test validation error
Test-Endpoint -Method "POST" -Endpoint "/tasks" `
    -Data '{"description":"Task without title","status":"pending"}' `
    -Description "Test validation error (missing title)"

# Test 14: Test invalid status
Test-Endpoint -Method "POST" -Endpoint "/tasks" `
    -Data '{"title":"Test task","description":"Testing invalid status","status":"invalid-status"}' `
    -Description "Test validation error (invalid status)"

# Test 15: Test invalid status filter
Test-Endpoint -Method "GET" -Endpoint "/tasks?status=invalid" `
    -Description "Test invalid status filter"

# Test 16: Test non-existent task
Test-Endpoint -Method "GET" -Endpoint "/tasks/999" `
    -Description "Test non-existent task (ID: 999)"

# Test 17: Test invalid task ID
Test-Endpoint -Method "GET" -Endpoint "/tasks/abc" `
    -Description "Test invalid task ID (abc)"

Write-Host "API Testing Complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Tips:" -ForegroundColor Yellow
Write-Host "   - Check that all endpoints return proper HTTP status codes" -ForegroundColor Gray
Write-Host "   - Verify JSON response format is consistent" -ForegroundColor Gray
Write-Host "   - Ensure validation errors are handled gracefully" -ForegroundColor Gray
Write-Host "   - Test edge cases and error conditions" -ForegroundColor Gray
