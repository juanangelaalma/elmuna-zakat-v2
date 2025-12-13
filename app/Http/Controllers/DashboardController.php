<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRice;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\RiceSale;
use App\Models\Rice;
use App\Models\Donation;
use App\Models\Fidyah;
use App\Models\Wealth;
use App\Models\PurchaseRiceAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'riceStock' => $this->getRiceStock($startDate, $endDate),
            'salesSummary' => $this->getSalesSummary($startDate, $endDate),
            'incomeSummary' => $this->getIncomeSummary($startDate, $endDate),
            'transactionSummary' => $this->getTransactionSummary($startDate, $endDate),
            'purchaseSummary' => $this->getPurchaseSummary($startDate, $endDate),
            'dailyTrends' => $this->getDailyTrends($startDate, $endDate),
        ];

        return Inertia::render('dashboard', $data);
    }

    private function getRiceStock($startDate, $endDate)
    {
        $purchaseTotal = PurchaseRice::whereBetween('date', [$startDate, $endDate])
            ->sum('quantity');

        $riceIncome = Rice::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('quantity');

        $riceDonations = Donation::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->whereIn('unit_type', ['kg', 'karung'])
            ->sum('quantity');

        $riceFidyah = Fidyah::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->whereIn('unit_type', ['kg', 'karung'])
            ->sum('quantity');

        $riceSales = RiceSale::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('quantity');

        $totalIncome = $purchaseTotal + $riceIncome + $riceDonations + $riceFidyah;
        $totalOutcome = $riceSales;
        $currentStockFromPurchaseIncomeOnly = $purchaseTotal - $totalOutcome;
        $totalCurrentStock = $totalIncome - $totalOutcome;

        return [
            'purchase' => $purchaseTotal,
            'zakat_rice' => $riceIncome,
            'donation_rice' => $riceDonations,
            'fidyah_rice' => $riceFidyah,
            'total_income' => $totalIncome,
            'sales' => $riceSales,
            'current_stock' => $currentStockFromPurchaseIncomeOnly,
            'total_current_stock' => $totalCurrentStock
        ];
    }

    private function getSalesSummary($startDate, $endDate)
    {
        $riceSales = RiceSale::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->selectRaw('SUM(amount) as total_amount, SUM(quantity) as total_quantity, COUNT(*) as count')
            ->first();

        return [
            'total_amount' => $riceSales->total_amount ?? 0,
            'total_quantity' => $riceSales->total_quantity ?? 0,
            'count' => $riceSales->count ?? 0,
        ];
    }

    private function getIncomeSummary($startDate, $endDate)
    {
        $zakatMall = Wealth::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('amount');

        $infaq = Donation::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('amount');

        $fidyah = Fidyah::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('amount');

        $riceSales = RiceSale::whereHas('transactionDetail.transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->sum('amount');

        return [
            'zakat_mall' => $zakatMall,
            'infaq' => $infaq,
            'fidyah' => $fidyah,
            'rice_sales' => $riceSales,
            'total' => $zakatMall + $infaq + $fidyah + $riceSales,
        ];
    }

    private function getTransactionSummary($startDate, $endDate)
    {
        $transactions = Transaction::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as count')
            ->first();

        $transactionDetails = TransactionDetail::whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        return [
            'total_transactions' => $transactions->count ?? 0,
            'by_type' => $transactionDetails,
        ];
    }

    private function getPurchaseSummary($startDate, $endDate)
    {
        $purchases = PurchaseRice::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('SUM(quantity) as total_quantity, SUM(quantity * price_per_kg) as total_value, COUNT(*) as count')
            ->first();

        return [
            'total_quantity' => $purchases->total_quantity ?? 0,
            'total_value' => $purchases->total_value ?? 0,
            'count' => $purchases->count ?? 0,
        ];
    }

    private function getDailyTrends($startDate, $endDate)
    {
        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        $dailyData = [];
        foreach ($dates as $date) {
            $nextDate = Carbon::parse($date)->addDay()->format('Y-m-d');

            $riceSales = RiceSale::whereHas('transactionDetail.transaction', function ($query) use ($date, $nextDate) {
                    $query->whereBetween('date', [$date, $nextDate]);
                })
                ->selectRaw('SUM(amount) as amount, SUM(quantity) as quantity')
                ->first();

            $income = $this->getIncomeSummary($date, $date);

            $dailyData[] = [
                'date' => $date,
                'rice_sales_amount' => $riceSales->amount ?? 0,
                'rice_sales_quantity' => $riceSales->quantity ?? 0,
                'zakat_mall' => $income['zakat_mall'],
                'infaq' => $income['infaq'],
                'fidyah' => $income['fidyah'],
                'total_income' => $income['total'],
            ];
        }

        return $dailyData;
    }
}
