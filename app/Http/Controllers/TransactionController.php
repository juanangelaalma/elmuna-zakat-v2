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
        $transaction = $this->service->getById(strval($id));
        
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
        $transaction = $this->service->getById(strval($id));
        
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

    public function riceSales()
    {
        $data = $this->service->getRiceSales();
        $summary = $this->summarizeRiceSales($data);

        return Inertia::render('transactions/rice-sales', [
            'data' => $data,
            'totalAmount' => $summary->totalAmount,
            'totalQuantity' => $summary->totalQuantity,
            'numberOfRecords' => $data->count(),
        ]);
    }

    public function rice()
    {
        $data = $this->service->getRice();
        $summary = $this->summarizeRice($data);

        return Inertia::render('transactions/rice', [
            'data' => $data,
            'totalQuantity' => $summary->totalQuantity,
            'numberOfRecords' => $data->count(),
        ]);
    }

    public function donations()
    {
        $data = $this->service->getDonations();
        $summary = $this->summarizeDonations($data);

        return Inertia::render('transactions/donations', [
            'data' => $data,
            'totalAmount' => $summary->totalAmount,
            'totalQuantity' => $summary->totalQuantity,
            'numberOfRecords' => $data->count(),
        ]);
    }

    public function fidyahs()
    {
        $data = $this->service->getFidyah();
        $summary = $this->summarizeFidyah($data);

        return Inertia::render('transactions/fidyahs', [
            'data' => $data,
            'totalAmount' => $summary->totalAmount,
            'totalQuantity' => $summary->totalQuantity,
            'numberOfRecords' => $data->count(),
        ]);
    }

    public function wealths()
    {
        $data = $this->service->getWealths();
        $summary = $this->summarizeWealths($data);

        return Inertia::render('transactions/wealths', [
            'data' => $data,
            'totalAmount' => $summary->totalAmount,
            'numberOfRecords' => $data->count(),
        ]);
    }

    public function exportRiceSales()
    {
        $data = $this->service->getRiceSales();
        return $this->exportToExcel($data, 'Penjualan Beras', ['customer', 'quantity', 'total', 'date', 'transaction_number']);
    }

    public function exportRice()
    {
        $data = $this->service->getRice();
        return $this->exportToExcel($data, 'Beras', ['customer', 'quantity', 'date', 'transaction_number']);
    }

    public function exportDonations()
    {
        $data = $this->service->getDonations();
        return $this->exportToExcel($data, 'Donasi', ['customer', 'type', 'quantity', 'amount', 'date', 'transaction_number']);
    }

    public function exportFidyah()
    {
        $data = $this->service->getFidyah();
        return $this->exportToExcel($data, 'Fidyah', ['customer', 'type', 'quantity', 'amount', 'date', 'transaction_number']);
    }

    public function exportWealths()
    {
        $data = $this->service->getWealths();
        return $this->exportToExcel($data, 'Zakat Mall', ['customer', 'amount', 'date', 'transaction_number']);
    }

    private function exportToExcel($data, $filename, $columns)
    {
        $columnLabels = [
            'customer' => 'Muzakki',
            'quantity' => 'Quantity',
            'total' => 'Total',
            'amount' => 'Amount',
            'type' => 'Beras/Uang',
            'date' => 'Tanggal',
            'transaction_number' => 'Nomor Transaksi',
        ];

        $labels = array_map(function($col) use ($columnLabels) {
            return $columnLabels[$col] ?? $col;
        }, $columns);

        $exportData = $data->map(function ($item) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                $value = $item->$col ?? '';
                if ($col === 'date' && $value) {
                    $value = \Carbon\Carbon::parse($value)->format('d/m/Y');
                } elseif ($col === 'type' && $value) {
                    $value = $value === 'rice' ? 'Beras' : 'Uang';
                }
                $row[] = $value;
            }
            return $row;
        })->toArray();

        $file = fopen('php://temp', 'r+');
        
        // Add BOM for UTF-8 to ensure Excel displays correctly
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($file, $labels);
        foreach ($exportData as $row) {
            fputcsv($file, $row);
        }
        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }

    private function summarizeRiceSales($data)
    {
        return (object) [
            'totalAmount' => $data->sum('total'),
            'totalQuantity' => $data->sum('quantity'),
        ];
    }

    private function summarizeRice($data)
    {
        return (object) [
            'totalQuantity' => $data->sum('quantity'),
        ];
    }

    private function summarizeDonations($data)
    {
        return (object) [
            'totalAmount' => $data->sum('amount'),
            'totalQuantity' => $data->where('type', 'rice')->sum('quantity'),
        ];
    }

    private function summarizeFidyah($data)
    {
        return (object) [
            'totalAmount' => $data->sum('amount'),
            'totalQuantity' => $data->where('type', 'rice')->sum('quantity'),
        ];
    }

    private function summarizeWealths($data)
    {
        return (object) [
            'totalAmount' => $data->sum('amount'),
        ];
    }
}
