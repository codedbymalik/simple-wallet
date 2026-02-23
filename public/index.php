<?php

// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Include Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Core\Router;
use Controllers\UserController;
use Controllers\AccountController;
use Controllers\TransactionController;

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');

// Initialize router
$router = new Router();

// User routes
$router->register('POST', '/api/users', function () {
    (new UserController())->create();
});

$router->register('GET', '/api/users/{id}', function (int $id) {
    (new UserController())->getById($id);
});

$router->register('GET', '/api/users', function () {
    (new UserController())->getAll();
});

$router->register('PUT', '/api/users/{id}', function (int $id) {
    (new UserController())->update($id);
});

$router->register('DELETE', '/api/users/{id}', function (int $id) {
    (new UserController())->delete($id);
});

// Account routes
$router->register('POST', '/api/accounts', function () {
    (new AccountController())->create();
});

$router->register('GET', '/api/accounts/{id}', function (int $id) {
    (new AccountController())->getById($id);
});

$router->register('GET', '/api/users/{userId}/accounts', function (int $userId) {
    (new AccountController())->getUserAccounts($userId);
});

$router->register('PUT', '/api/accounts/{id}', function (int $id) {
    (new AccountController())->update($id);
});

$router->register('DELETE', '/api/accounts/{id}', function (int $id) {
    (new AccountController())->delete($id);
});

// Transaction routes
$router->register('POST', '/api/transactions/transfer', function () {
    (new TransactionController())->transfer();
});

$router->register('GET', '/api/accounts/{accountId}/transactions', function (int $accountId) {
    (new TransactionController())->getAccountTransactions($accountId);
});

$router->register('GET', '/api/transactions/{id}', function (int $id) {
    (new TransactionController())->getTransactionById($id);
});

// Dispatch the request
$router->dispatch();
