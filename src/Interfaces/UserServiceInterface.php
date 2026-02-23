<?php

namespace Interfaces;

interface UserServiceInterface
{
    public function createUser(array $data): array;
    public function getUserById(int $id): ?array;
    public function getAllUsers(): array;
    public function updateUser(int $id, array $data): array;
    public function deleteUser(int $id): bool;
}
