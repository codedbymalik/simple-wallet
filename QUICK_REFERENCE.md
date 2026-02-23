# 📚 Quick Reference Guide - Bank Transaction API

## File Organization Quick Map

```
CONFIGURATION:
├── composer.json               PSR-4 autoloading setup
├── docker-compose.yaml         Docker services orchestration
├── Dockerfile                  PHP 8.2 Apache image definition
├── .env                        Database credentials & config
├── .gitignore                  Git exclusions
└── schema.sql                  Database schema

ENTRY POINT:
└── public/index.php            Main router & dispatcher

CORE INFRASTRUCTURE:
├── src/Core/Database.php       Singleton PDO instance
├── src/Core/Router.php         Custom URL routing
├── src/Core/BaseModel.php      Abstract CRUD base class
└── src/Core/BaseController.php Abstract JSON response handler

PRESENTATION LAYER:
├── src/Controllers/UserController.php
├── src/Controllers/AccountController.php
└── src/Controllers/TransactionController.php

DATA ACCESS LAYER:
├── src/Models/UserModel.php
├── src/Models/AccountModel.php
└── src/Models/TransactionModel.php

BUSINESS LOGIC LAYER:
├── src/Services/UserService.php
├── src/Services/AccountService.php
└── src/Services/TransactionService.php

CONTRACTS:
├── src/Interfaces/UserServiceInterface.php
├── src/Interfaces/AccountServiceInterface.php
└── src/Interfaces/TransactionServiceInterface.php

DOCUMENTATION:
├── README.md                   Complete API docs
├── QUICKSTART.md               Getting started guide
├── ARCHITECTURE.md             System design overview
├── IMPLEMENTATION_SUMMARY.md   What was built
├── DEPLOYMENT_CHECKLIST.md     Production deployment
└── QUICK_REFERENCE.md          This file
```

---

## 🔍 Class Reference

### Core Classes

#### Database.php (Singleton)
```php
// Get PDO connection
$pdo = Database::getInstance()->getConnection();

// Use in prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => 1]);
```

#### Router.php
```php
$router = new Router();
$router->register('POST', '/api/users', $callback);
$router->dispatch(); // Process request
```

#### BaseModel.php (Extend this)
```php
// Generic CRUD methods available to all models:
$model->find($id);                    // Get by ID
$model->all();                        // Get all
$model->findBy(['email' => 'x@.com']); // Find with criteria
$model->findOneBy(['userid' => 1]);   // Get first match
$model->create($data);                // Insert & return ID
$model->update($id, $data);           // Update & return count
$model->delete($id);                  // Delete & return count
```

#### BaseController.php (Extend this)
```php
// Response methods:
$this->sendSuccess($data, 'Message', 200);
$this->sendError('Error message', 400);
$this->sendJson(['key' => 'value'], 200);

// Request parsing:
$payload = $this->getJsonPayload(); // Parse JSON body
```

---

## 🌐 API Endpoints Cheat Sheet

### Users
```
POST   /api/users                 Create user
GET    /api/users                 List all users
GET    /api/users/{id}            Get user
PUT    /api/users/{id}            Update user
DELETE /api/users/{id}            Delete user
```

### Accounts
```
POST   /api/accounts              Create account
GET    /api/accounts/{id}         Get account
GET    /api/users/{userId}/accounts   List user's accounts
PUT    /api/accounts/{id}         Update account
DELETE /api/accounts/{id}         Delete account
```

### Transactions
```
POST   /api/transactions/transfer           Transfer funds (ACID)
GET    /api/accounts/{accountId}/transactions   Get history
GET    /api/transactions/{id}               Get transaction
```

---

## 📝 Request/Response Examples

### Create User
```bash
curl -X POST http://localhost/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "555-1234"
  }'

# Response (201):
{
  "status": "success",
  "message": "User created successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "555-1234",
    "created_at": "2024-02-23 10:00:00",
    "updated_at": "2024-02-23 10:00:00"
  }
}
```

### Transfer Funds
```bash
curl -X POST http://localhost/api/transactions/transfer \
  -H "Content-Type: application/json" \
  -d '{
    "from_account_id": 1,
    "to_account_id": 2,
    "amount": 1000,
    "description": "Payment"
  }'

# Response (201): Transaction recorded with ACID guarantees
```

### Error Response
```bash
curl -X POST http://localhost/api/transactions/transfer \
  -H "Content-Type: application/json" \
  -d '{
    "from_account_id": 1,
    "to_account_id": 2,
    "amount": 50000
  }'

# Response (422):
{
  "error": "Insufficient funds. Current balance: 5000"
}
```

---

## 🐳 Docker Commands Cheat Sheet

```bash
# Build & Start
docker-compose up --build -d

# Stop
docker-compose down

# View logs
docker-compose logs -f php            # Follow PHP logs
docker-compose logs -f db             # Follow MySQL logs

# Access containers
docker-compose exec php bash          # SSH into PHP
docker-compose exec db mysql -u root -p  # MySQL CLI

# Restart service
docker-compose restart php

# Check status
docker-compose ps

# Rebuild images
docker-compose build --no-cache

# Remove volumes (WARNING: deletes data)
docker-compose down -v
```

---

## 🔧 Environment Variables

```env
# .env file (loaded in public/index.php)
DB_HOST=db              # Docker service name or localhost
DB_USER=bank_user       # MySQL user
DB_PASSWORD=bank_password   # MySQL password
DB_NAME=bank_db         # Database name
APP_ENV=development     # development or production
```

---

## 📊 Database Tables Quick Reference

### Users Table
```sql
SELECT * FROM users;
-- Columns: id, name, email, phone, created_at, updated_at
-- Primary Key: id
-- Unique: email
```

### Accounts Table
```sql
SELECT * FROM accounts;
-- Columns: id, user_id, account_number, balance, currency, status
-- Primary Key: id
-- Foreign Key: user_id → users(id)
-- Unique: account_number
-- Check: balance >= 0
```

### Transactions Table
```sql
SELECT * FROM transactions;
-- Columns: id, from_account_id, to_account_id, amount, type, status, description, created_at
-- Primary Keys: id
-- Foreign Keys: from_account_id, to_account_id → accounts(id)
```

---

## 🔐 Validation Rules

### User Creation
- ✅ name: Required, non-empty
- ✅ email: Required, valid format, unique
- ✅ phone: Optional

### Account Creation
- ✅ user_id: Required, must exist
- ✅ account_number: Required, unique
- ✅ balance: Required, >= 0
- ✅ currency: Optional (defaults to 'USD')

### Fund Transfer
- ✅ from_account_id: Required, must exist, active
- ✅ to_account_id: Required, must exist, active
- ✅ amount: Required, > 0
- ✅ Balance check: from_balance >= amount
- ✅ Transaction: ACID compliant with rollback

---

## 🐛 Error Codes & Messages

| Code | Status | Meaning |
|------|--------|---------|
| 200 | OK | Success (GET, PUT) |
| 201 | Created | Resource created (POST) |
| 400 | Bad Request | Validation error |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Business logic error (e.g., insufficient funds) |
| 500 | Server Error | Unexpected error |

---

## 🔄 Development Workflow

```bash
# 1. Start Docker
docker-compose up --build -d

# 2. Verify database
docker-compose exec db mysql -u bank_user -p bank_db -e "SHOW TABLES;"

# 3. Test API
curl http://localhost/api/users

# 4. View logs
docker-compose logs -f php

# 5. Make changes to PHP code (no rebuild needed, volume mounted)
# Edit src/Services/UserService.php

# 6. Smart reload (optional)
docker-compose restart php

# 7. Stop when done
docker-compose down
```

---

## 🧪 Testing Workflow

### 1. Create Test User
```bash
curl -X POST http://localhost/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone": "555-0000"
  }'
# Note: User ID from response (should be 1)
```

### 2. Create Accounts
```bash
curl -X POST http://localhost/api/accounts \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "account_number": "ACC-001",
    "balance": 5000
  }'

curl -X POST http://localhost/api/accounts \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "account_number": "ACC-002",
    "balance": 3000
  }'
# Account IDs should be 1 and 2
```

### 3. Transfer Funds
```bash
curl -X POST http://localhost/api/transactions/transfer \
  -H "Content-Type: application/json" \
  -d '{
    "from_account_id": 1,
    "to_account_id": 2,
    "amount": 1000,
    "description": "Test transfer"
  }'
```

### 4. Verify Results
```bash
# Check balances
curl http://localhost/api/accounts/1  # Should be 4000
curl http://localhost/api/accounts/2  # Should be 4000

# Check transaction history
curl http://localhost/api/accounts/1/transactions
```

---

## 📋 Code Review Checklist

- ✅ Prepared statements used (no SQL injection)
- ✅ Type hints on all functions
- ✅ Try/catch error handling
- ✅ Validation before database queries
- ✅ Error logging via error_log()
- ✅ Clean JSON responses
- ✅ Services implement interfaces
- ✅ No HTML/CSS/JS in PHP files
- ✅ Comments for complex logic
- ✅ Returns/echoes only JSON (no var_dump)

---

## 🚀 Deployment Quick Steps

1. Update `.env` with production credentials
2. Run `docker-compose up --build -d`
3. Verify: `curl http://production-server/api/users`
4. Monitor: `docker-compose logs -f`
5. Backup database daily
6. Configure SSL/HTTPS
7. Set up monitoring/alerting

---

## 💾 Database Backup/Restore

```bash
# Backup
docker-compose exec db mysqldump -u bank_user -p bank_db > backup.sql

# Restore
docker-compose exec db mysql -u bank_user -p bank_db < backup.sql

# Backup volume
docker run --rm -v bank_api_db_data:/data -v $(pwd):/backup \
  alpine tar czf /backup/mysql.tar.gz /data
```

---

## 🔑 Key Concepts Summary

| Concept | Implementation | Purpose |
|---------|----------------|---------|
| **Singleton** | Database.php | Single PDO instance |
| **DI** | Service injection in controllers | Loose coupling |
| **ACID** | Transaction in TransactionService | Data integrity |
| **Router** | Custom pattern matching | Clean URLs |
| **Middleware** | BaseController | Common response handling |
| **OOP** | Abstract base classes | Code reuse |
| **Prepared Statements** | PDO :placeholders | SQL injection protection |

---

## 📞 Troubleshooting Guide

| Problem | Solution |
|---------|----------|
| 404 errors | Check route in public/index.php |
| DB connection error | Verify .env, restart db container |
| Slow queries | Check indexes, review logs |
| JSON parse error | Verify Content-Type header is application/json |
| Port conflict | Change port in docker-compose.yaml |
| permission denied | chmod files, check Docker permissions |

---

## 📖 Documentation Map

- **README.md** → Full API documentation
- **QUICKSTART.md** → Get started in 5 minutes
- **ARCHITECTURE.md** → System design & diagrams
- **IMPLEMENTATION_SUMMARY.md** → What was built
- **DEPLOYMENT_CHECKLIST.md** → Production deployment
- **QUICK_REFERENCE.md** → This file

---

**Everything you need to get started is above. Happy coding! 🎉**
