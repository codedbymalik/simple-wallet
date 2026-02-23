<?php

namespace Models;

use Core\BaseModel;

class AccountModel extends BaseModel
{
    protected string $table = 'accounts';

    public function createAccount(array $data): int
    {
        return $this->create([
            'user_id' => $data['user_id'],
            'account_number' => $data['account_number'],
            'balance' => $data['balance'],
            'currency' => $data['currency'] ?? 'USD',
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function getAccountById(int $id): ?array
    {
        return $this->find($id);
    }

    public function getAccountByNumber(string $accountNumber): ?array
    {
        return $this->findOneBy(['account_number' => $accountNumber]);
    }

    public function getUserAccounts(int $userId): array
    {
        return $this->findBy(['user_id' => $userId]);
    }

    public function updateAccount(int $id, array $data): int
    {
        $updateData = [];
        if (isset($data['balance'])) {
            $updateData['balance'] = $data['balance'];
        }
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        return $this->update($id, $updateData);
    }

    public function deleteAccount(int $id): int
    {
        return $this->delete($id);
    }

    public function getBalance(int $id): ?float
    {
        $account = $this->find($id);
        return $account ? (float) $account['balance'] : null;
    }

    public function decreaseBalance(int $id, float $amount): bool
    {
        $query = "UPDATE {$this->table} SET balance = balance - :amount WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['amount' => $amount, 'id' => $id]);
    }

    public function increaseBalance(int $id, float $amount): bool
    {
        $query = "UPDATE {$this->table} SET balance = balance + :amount WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['amount' => $amount, 'id' => $id]);
    }
}
