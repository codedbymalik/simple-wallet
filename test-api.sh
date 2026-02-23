#!/bin/bash

# Bank Transaction REST API - Integration Testing Script
# This script demonstrates all API endpoints with curl commands

API_BASE_URL="http://localhost"

echo "=========================================="
echo "Bank Transaction API - Integration Tests"
echo "=========================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper function to make requests
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local description=$4

    echo -e "${BLUE}Testing:${NC} $description"
    echo -e "Request: ${BLUE}$method $endpoint${NC}"
    
    if [ -z "$data" ]; then
        curl -X "$method" \
            -H "Content-Type: application/json" \
            "$API_BASE_URL$endpoint"
    else
        echo -e "Payload: $data"
        curl -X "$method" \
            -H "Content-Type: application/json" \
            -d "$data" \
            "$API_BASE_URL$endpoint"
    fi
    
    echo ""
    echo "---"
    echo ""
}

# Test 1: Create Users
echo -e "${GREEN}=== USER MANAGEMENT ===${NC}"
test_endpoint "POST" "/api/users" \
    '{"name":"Alice Miller","email":"alice@example.com","phone":"555-0001"}' \
    "Create User (Alice)"

test_endpoint "POST" "/api/users" \
    '{"name":"Bob Smith","email":"bob@example.com","phone":"555-0002"}' \
    "Create User (Bob)"

# Test 2: Get Users
test_endpoint "GET" "/api/users" \
    "" \
    "Get All Users"

test_endpoint "GET" "/api/users/1" \
    "" \
    "Get User by ID"

# Test 3: Create Accounts
echo -e "${GREEN}=== ACCOUNT MANAGEMENT ===${NC}"
test_endpoint "POST" "/api/accounts" \
    '{"user_id":1,"account_number":"ACC-001","balance":10000.00,"currency":"USD"}' \
    "Create Account for Alice (Initial: \$10,000)"

test_endpoint "POST" "/api/accounts" \
    '{"user_id":1,"account_number":"ACC-002","balance":5000.00,"currency":"USD"}' \
    "Create Second Account for Alice (Initial: \$5,000)"

test_endpoint "POST" "/api/accounts" \
    '{"user_id":2,"account_number":"ACC-003","balance":15000.00,"currency":"USD"}' \
    "Create Account for Bob (Initial: \$15,000)"

# Test 4: Get Accounts
test_endpoint "GET" "/api/users/1/accounts" \
    "" \
    "Get All Accounts for Alice (User 1)"

test_endpoint "GET" "/api/accounts/1" \
    "" \
    "Get Account Details (Account 1)"

# Test 5: Test ACID Transfer Transaction
echo -e "${GREEN}=== TRANSACTION MANAGEMENT (ACID) ===${NC}"
test_endpoint "POST" "/api/transactions/transfer" \
    '{"from_account_id":1,"to_account_id":2,"amount":2000.00,"description":"Transfer between Alice'\''s accounts"}' \
    "Transfer \$2,000 from Account 1 to Account 2 (Alice)"

test_endpoint "POST" "/api/transactions/transfer" \
    '{"from_account_id":1,"to_account_id":3,"amount":3000.00,"description":"Payment to Bob"}' \
    "Transfer \$3,000 from Alice to Bob"

# Test 6: Get Transaction History
test_endpoint "GET" "/api/accounts/1/transactions" \
    "" \
    "Get Transaction History for Account 1 (Alice)"

test_endpoint "GET" "/api/accounts/3/transactions" \
    "" \
    "Get Transaction History for Account 3 (Bob)"

# Test 7: Error Cases
echo -e "${GREEN}=== ERROR HANDLING ===${NC}"
test_endpoint "POST" "/api/transactions/transfer" \
    '{"from_account_id":1,"to_account_id":3,"amount":50000.00,"description":"Large transfer"}' \
    "Error Test: Insufficient Funds (Attempt \$50,000 transfer)"

test_endpoint "POST" "/api/accounts" \
    '{"user_id":999,"account_number":"ACC-999","balance":1000}' \
    "Error Test: Non-existent User"

test_endpoint "POST" "/api/users" \
    '{"name":"Invalid User","email":"alice@example.com"}' \
    "Error Test: Duplicate Email"

# Test 8: Update Operations
echo -e "${GREEN}=== UPDATE OPERATIONS ===${NC}"
test_endpoint "PUT" "/api/users/1" \
    '{"name":"Alice Miller Updated","phone":"555-9999"}' \
    "Update User Information"

test_endpoint "PUT" "/api/accounts/1" \
    '{"status":"inactive"}' \
    "Update Account Status"

echo -e "${GREEN}=========================================="
echo "Integration Tests Complete!"
echo "==========================================${NC}"
