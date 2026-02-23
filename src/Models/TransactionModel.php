<?php

namespace Models;

use Core\BaseModel;

class TransactionModel extends BaseModel
{
    protected string $table = 'transactions';

    public function createTransaction(array $data): int
    {
        return $this->create([
            'from_account_id' => $data['from_account_id'],
            'to_account_id' => $data['to_account_id'],
            'amount' => $data['amount'],
            'type' => $data['type'] ?? 'transfer',
            'status' => $data['status'] ?? 'completed',
            'description' => $data['description'] ?? null,
        ]);
    }

    public function getTransactionById(int $id): ?array
    {
        return $this->find($id);
    }

    public function getAccountTransactions(int $accountId): array
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE from_account_id = :id OR to_account_id = :id 
                  ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $accountId]);
        
        return $stmt->fetchAll();
    }

    public function getAllTransactions(): array
    {
        return $this->all();
    }

    public function updateTransaction(int $id, array $data): int
    {
        $updateData = [];
        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }
        
        return $this->update($id, $updateData);
    }

    public function deleteTransaction(int $id): int
    {
        return $this->delete($id);
    }
}
