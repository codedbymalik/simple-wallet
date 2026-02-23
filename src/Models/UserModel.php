<?php

namespace Models;

use Core\BaseModel;

class UserModel extends BaseModel
{
    protected string $table = 'users';

    public function createUser(array $data): int
    {
        return $this->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
    }

    public function getUserById(int $id): ?array
    {
        return $this->find($id);
    }

    public function getUserByEmail(string $email): ?array
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function getAllUsers(): array
    {
        return $this->all();
    }

    public function updateUser(int $id, array $data): int
    {
        $updateData = [];
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }
        
        return $this->update($id, $updateData);
    }

    public function deleteUser(int $id): int
    {
        return $this->delete($id);
    }
}
