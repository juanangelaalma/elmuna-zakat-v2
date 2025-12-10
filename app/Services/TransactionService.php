<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\DTO\TransactionDTO;
use App\Models\Transaction;
use App\Contracts\TransactionServiceInterface;
use App\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Carbon;
use App\Factories\TransactionItemFactory;
use App\Contracts\TransactionDetailServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection as SupportCollection;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private TransactionRepositoryInterface $repository,
        private TransactionDetailServiceInterface $transactionDetailService,
    ) {}

    public function getList(): Collection
    {
        return $this->repository->getList();
    }

    public function getById(int $id): ?array
    {
        return $this->repository->getById($id);
    }

    public function createTransaction(TransactionDTO $transaction): Transaction
    {
        DB::beginTransaction();

        try {
            $newTransaction = $this->repository->createTransaction($transaction);

            foreach ($transaction->transaction_details as $item) {
                $item['transaction_id'] = $newTransaction->id;
                $this->transactionDetailService->createTransactionDetail($item);
            }

            DB::commit();

            return $newTransaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getRiceSales(): SupportCollection
    {
        return $this->repository->getRiceSales();
    }

    public function getRice(): SupportCollection
    {
        return $this->repository->getRice();
    }

    public function getDonations(): SupportCollection
    {
        return $this->repository->getDonations();
    }

    public function getFidyah(): SupportCollection
    {
        return $this->repository->getFidyah();
    }

    public function getWealths(): SupportCollection
    {
        return $this->repository->getWealths();
    }
}