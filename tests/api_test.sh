#!/bin/bash

# Task Manager API Test Script
# This script tests all API endpoints with sample data

API_BASE="http://localhost:8080"
CONTENT_TYPE="Content-Type: application/json"

echo "Task Manager API Test Script"
echo "================================"
echo "Testing API at: $API_BASE"
echo ""

# Function to make HTTP requests and display results
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local description=$4
    
    echo "Testing: $description"
    echo "   $method $endpoint"
    
    if [ -n "$data" ]; then
        echo "   Data: $data"
        response=$(curl -s -X $method "$API_BASE$endpoint" \
                       -H "$CONTENT_TYPE" \
                       -d "$data" \
                       -w "\nHTTP_CODE:%{http_code}")
    else
        response=$(curl -s -X $method "$API_BASE$endpoint" \
                       -w "\nHTTP_CODE:%{http_code}")
    fi
    
    # Extract HTTP code and response body
    http_code=$(echo "$response" | grep "HTTP_CODE:" | cut -d: -f2)
    body=$(echo "$response" | sed '/HTTP_CODE:/d')
    
    echo "   Status: $http_code"
    echo "   Response:"
    echo "$body" | jq . 2>/dev/null || echo "$body"
    echo ""
    
    # Brief pause between requests
    sleep 1
}

echo "Starting API Tests..."
echo ""

# Test 1: Create a new task
test_endpoint "POST" "/tasks" \
    '{"title":"Complete project documentation","description":"Write comprehensive README and API docs","status":"pending"}' \
    "Create new task"

# Test 2: Create another task
test_endpoint "POST" "/tasks" \
    '{"title":"Implement user authentication","description":"Add JWT-based authentication system","status":"in-progress"}' \
    "Create another task"

# Test 3: Get all tasks
test_endpoint "GET" "/tasks" "" \
    "Get all tasks"

# Test 4: Get tasks with status filter
test_endpoint "GET" "/tasks?status=pending" "" \
    "Get tasks with status filter (pending)"

# Test 5: Get tasks with status filter
test_endpoint "GET" "/tasks?status=completed" "" \
    "Get tasks with status filter (completed)"

# Test 6: Get single task by ID
test_endpoint "GET" "/tasks/1" "" \
    "Get task by ID (1)"

# Test 7: Update a task
test_endpoint "PUT" "/tasks/1" \
    '{"title":"Complete project documentation","description":"Write comprehensive README and API docs - Updated","status":"completed"}' \
    "Update task (ID: 1)"

# Test 8: Get updated task
test_endpoint "GET" "/tasks/1" "" \
    "Get updated task (ID: 1)"

# Test 9: Test PATCH (partial update)
test_endpoint "PATCH" "/tasks/2" \
    '{"status":"completed"}' \
    "Partial update task (ID: 2) - change status only"

# Test 10: Get patched task
test_endpoint "GET" "/tasks/2" "" \
    "Get patched task (ID: 2)"

# Test 11: Test DELETE task
test_endpoint "DELETE" "/tasks/3" "" \
    "Delete task (ID: 3)"

# Test 12: Verify task was deleted
test_endpoint "GET" "/tasks/3" "" \
    "Verify deleted task returns 404 (ID: 3)"

# Test 13: Test validation error
test_endpoint "POST" "/tasks" \
    '{"description":"Task without title","status":"pending"}' \
    "Test validation error (missing title)"

# Test 14: Test invalid status
test_endpoint "POST" "/tasks" \
    '{"title":"Test task","description":"Testing invalid status","status":"invalid-status"}' \
    "Test validation error (invalid status)"

# Test 15: Test invalid status filter
test_endpoint "GET" "/tasks?status=invalid" "" \
    "Test invalid status filter"

# Test 16: Test non-existent task
test_endpoint "GET" "/tasks/999" "" \
    "Test non-existent task (ID: 999)"

# Test 17: Test invalid task ID
test_endpoint "GET" "/tasks/abc" "" \
    "Test invalid task ID (abc)"

echo "API Testing Complete!"
echo ""
echo "Tips:"
echo "   - Check that all endpoints return proper HTTP status codes"
echo "   - Verify JSON response format is consistent"
echo "   - Ensure validation errors are handled gracefully"
echo "   - Test edge cases and error conditions"
