<?php

namespace App\Http\Controllers;

use App\Contracts\TransactionServiceInterface;
use App\DTO\TransactionDTO;
use App\Http\Requests\TransactionStoreRequest;
use Inertia\Inertia;
use Illuminate\Support\Carbon;
use App\Utils\TransactionNumberGenerator;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionServiceInterface $service,
        private TransactionNumberGenerator $transactionNumberGenerator
    ) {}

    public function index()
    {
        $transactions = $this->service->getList();
        $transactionSummary = $this->summarizeTransactions($transactions);
        
        $totalAmount = $transactionSummary->totalAmount;
        $totalQuantity = $transactionSummary->totalQuantity;
        $numberOfTransactions = count($transactions);

        return Inertia::render('transactions/transactions', compact('transactions', 'totalAmount', 'totalQuantity', 'numberOfTransactions'));
    }

    public function create()
    {
        return Inertia::render('transactions/transaction-create');
    }

    public function show($id)
    {
        $transaction = $this->service->getById($id);
        
        if (!$transaction) {
            return redirect()->route('transactions')->with('error', 'Transaction not found');
        }

        return Inertia::render('transactions/transaction-detail', compact('transaction'));
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

    public function receipt($id)
    {
        $transaction = $this->service->getById($id);
        
        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        $pdf = Pdf::loadView('receipt', compact('transaction'))
            ->setPaper([0, 0, 165, 500], 'portrait'); // 58mm width in points (58mm = ~165pt)

        return $pdf->stream('struk-' . $transaction['transaction_number'] . '.pdf');
    }
    
    private function summarizeTransactions($transactions)
    {
        $totalAmount = $transactions->sum('total_transaction_amount');
        $totalQuantity = $transactions->sum('total_transaction_quantity');

        return (object) [
            'totalAmount' => $totalAmount,
            'totalQuantity' => $totalQuantity,
        ];
    }
}
