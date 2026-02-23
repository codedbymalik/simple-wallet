<?php

namespace Interfaces;

interface AccountServiceInterface
{
    public function createAccount(int $userId, array $data): array;
    public function getAccountById(int $id): ?array;
    public function getUserAccounts(int $userId): array;
    public function updateAccount(int $id, array $data): array;
    public function deleteAccount(int $id): bool;
}
