<?php

namespace Interfaces;

interface TransactionServiceInterface
{
    public function transferFunds(int $fromAccountId, int $toAccountId, float $amount, string $description): array;
    public function getAccountTransactions(int $accountId): array;
    public function getTransactionById(int $id): ?array;
}
