<?php

namespace App\Http\Controllers;

use App\Contracts\TransactionServiceInterface;
use App\DTO\TransactionDTO;
use App\Http\Requests\TransactionStoreRequest;
use Inertia\Inertia;
use Illuminate\Support\Carbon;
use App\Utils\TransactionNumberGenerator;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionServiceInterface $service,
        private TransactionNumberGenerator $transactionNumberGenerator
    ) {}

    public function index()
    {
        $transactions = $this->service->getList();
        return Inertia::render('transactions/transactions', compact('transactions'));
    }

    public function create()
    {
        return Inertia::render('transactions/transaction-create');
    }

    public function store(TransactionStoreRequest $request)
    {
        $validatedData = $request->validated();
        $newTransaction = new TransactionDTO(
            $this->transactionNumberGenerator->generate(),
            Carbon::parse($validatedData['date']),
            $validatedData['customer'],
            $validatedData['address'],
            $validatedData['wa_number'],
            $validatedData['officer_name'],
            auth()->user()->id,
            $validatedData['items']
        );

        $this->service->createTransaction($newTransaction);

        return redirect()->route('transactions');
    }
}
