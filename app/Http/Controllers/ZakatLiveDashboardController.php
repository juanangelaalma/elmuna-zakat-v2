<?php

namespace App\Http\Controllers;

use App\Models\DefaultValue;
use App\Models\Donation;
use App\Models\Fidyah;
use App\Models\PurchaseRice;
use App\Models\PurchaseRiceAllocation;
use App\Models\Rice;
use App\Models\RiceSale;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Wealth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionItemType;

class ZakatLiveDashboardController extends Controller
{
    public function index()
    {
        return view('zakat-live');
    }

    public function data(): JsonResponse
    {
        return response()->json([
            'today'   => $this->getTodayData(),
            'stock'   => $this->getStockData(),
            'overall' => $this->getOverallData(),
            'marquee' => $this->getMarqueeData(),
        ]);
    }

    // ─── TODAY ───────────────────────────────────────────────────────────────

    private function getTodayData(): array
    {
        $today = Carbon::today()->toDateString();

        // Total muzakki (distinct transactions) hari ini
        $totalMuzakki = TransactionDetail::whereHas('transaction', fn($q) =>
            $q->whereDate('date', $today)
        )->whereIn('type', [TransactionItemType::RICE, TransactionItemType::RICE_SALES])->count();

        // Total beras hari ini: rices + rice_sales
        $riceFromZakat = Rice::whereHas('transactionDetail.transaction', fn($q) =>
            $q->whereDate('date', $today)
        )->sum('quantity');

        $riceFromSales = RiceSale::whereHas('transactionDetail.transaction', fn($q) =>
            $q->whereDate('date', $today)
        )->sum('quantity');

        $totalRice = (float) $riceFromZakat + (float) $riceFromSales;

        // Total uang hari ini: donations + fidyahs + wealths + rice_sales
        $totalMoney = $this->sumMoneyForDate($today);

        // 5 transaksi terakhir hari ini (live feed)
        $recentTransactions = $this->getRecentTransactions($today, 5);

        return [
            'muzakki'             => $totalMuzakki,
            'total_rice_kg'       => round($totalRice, 1),
            'total_money'         => (float) $totalMoney,
            'total_money_fmt'     => 'Rp ' . number_format($totalMoney, 0, ',', '.'),
            'recent_transactions' => $recentTransactions,
        ];
    }

    private function sumMoneyForDate(string $date): float
    {
        $donations = Donation::whereHas('transactionDetail.transaction', fn($q) =>
            $q->whereDate('date', $date)
        )->sum('amount');

        $fidyahs = Fidyah::whereHas('transactionDetail.transaction', fn($q) =>
            $q->whereDate('date', $date)
        )->sum('amount');

        $wealths = Wealth::whereHas('transactionDetail.transaction', fn($q) =>
            $q->whereDate('date', $date)
        )->sum('amount');

        $riceSales = RiceSale::whereHas('transactionDetail.transaction', fn($q) =>
            $q->whereDate('date', $date)
        )->sum('amount');

        return (float) $donations + (float) $fidyahs + (float) $wealths + (float) $riceSales;
    }

    private function getRecentTransactions(string $date, int $limit): array
    {
        return Transaction::with(['details' => fn($q) => $q->select('id', 'transaction_id', 'type', 'giver_name')])
            ->whereDate('date', $date)
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'customer', 'created_at'])
            ->map(function ($trx) {
                $types = $trx->details->pluck('type')->unique()->values();
                $typeLabels = $types->map(fn($t) => $this->typeLabel($t))->join(', ');

                return [
                    'name'        => $trx->customer,
                    'types'       => $typeLabels,
                    'created_at'  => $trx->created_at?->format('H:i'),
                ];
            })
            ->toArray();
    }

    // ─── STOCK ───────────────────────────────────────────────────────────────

    private function getStockData(): array
    {
        // Total beras yang pernah dibeli
        $totalPurchased = (float) PurchaseRice::sum('quantity');

        // Total beras yang sudah dialokasikan ke penjualan
        $totalAllocated = (float) PurchaseRiceAllocation::sum('quantity');

        $availableStock = max(0, $totalPurchased - $totalAllocated);

        // Harga beras dari default_values
        $defaults    = DefaultValue::first();
        $pricePerPkg = null;
        if ($defaults && $defaults->rice_sales_quantity > 0) {
            $pricePerPkg = (float) $defaults->rice_sales_amount / (float) $defaults->rice_sales_quantity;
        }

        return [
            'available_kg'           => round($availableStock, 1),
            'total_purchased_kg'     => round($totalPurchased, 1),
            'price_per_pkg'          => $pricePerPkg,
            'price_per_pkg_fmt'      => $pricePerPkg
                ? 'Rp ' . number_format($pricePerPkg, 0, ',', '.')
                : 'N/A',
            'default_rice_quantity'  => $defaults?->rice_sales_quantity,
        ];
    }

    // ─── OVERALL ─────────────────────────────────────────────────────────────

    private function getOverallData(): array
    {
        // Total muzakki keseluruhan
        $totalMuzakki = Transaction::count();

        // Total beras keseluruhan
        $totalRice = (float) Rice::sum('quantity') + (float) RiceSale::sum('quantity');

        // Total uang keseluruhan
        $totalMoney = (float) Donation::sum('amount')
            + (float) Fidyah::sum('amount')
            + (float) Wealth::sum('amount')
            + (float) RiceSale::sum('amount');

        // Breakdown per kategori
        $breakdown = $this->getCategoryBreakdown();

        // Chart: total beras per hari (7 hari terakhir)
        $chartData = $this->getRiceChartData(7);

        // Estimasi penerima manfaat (per 2.5 kg)
        $estimatedBeneficiaries = (int) floor($totalRice / 2.5);

        return [
            'muzakki'            => $totalMuzakki,
            'total_rice_kg'      => round($totalRice, 1),
            'total_money'        => $totalMoney,
            'total_money_fmt'    => 'Rp ' . number_format($totalMoney, 0, ',', '.'),
            'breakdown'          => $breakdown,
            'chart_data'         => $chartData,
            'estimated_beneficiaries' => $estimatedBeneficiaries,
        ];
    }

    private function getCategoryBreakdown(): array
    {
        // Zakat Fitrah Beras (bawa sendiri) — type = 'rice'
        $zakatBeras = TransactionDetail::where('type', 'rice')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('COUNT(DISTINCT transactions.id) as trx_count')
            ->first();
        $zakatBerasKg = (float) Rice::whereHas('transactionDetail', fn($q) =>
            $q->where('type', 'rice')
        )->sum('quantity');

        // Zakat Fitrah Beli di Masjid (penjualan beras) — type = 'rice_sale'
        $zakatBeliMasjid = TransactionDetail::where('type', 'rice_sale')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('COUNT(DISTINCT transactions.id) as trx_count')
            ->first();
        $zakatBeliKg = (float) RiceSale::whereHas('transactionDetail', fn($q) =>
            $q->where('type', 'rice_sale')
        )->sum('quantity');

        // Zakat Mal — type = 'wealth'
        $zakatMal = TransactionDetail::where('type', 'wealth')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('COUNT(DISTINCT transactions.id) as trx_count')
            ->first();
        $zakatMalAmount = (float) Wealth::sum('amount');

        // Infaq/Sedekah — type = 'donation'
        $infaq = TransactionDetail::where('type', 'donation')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('COUNT(DISTINCT transactions.id) as trx_count')
            ->first();
        $infaqAmount = (float) Donation::sum('amount');

        // Fidyah — type = 'fidyah'
        $fidyah = TransactionDetail::where('type', 'fidyah')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('COUNT(DISTINCT transactions.id) as trx_count')
            ->first();
        $fidyahAmount = (float) Fidyah::sum('amount');

        return [
            'zakat_beras' => [
                'label'   => 'Zakat Fitrah (Beras)',
                'muzakki' => (int) ($zakatBeras->trx_count ?? 0),
                'value'   => round($zakatBerasKg, 1),
                'unit'    => 'kg',
                'color'   => 'primary',
            ],
            'zakat_beli_masjid' => [
                'label'   => 'Zakat Fitrah (Beli di Masjid)',
                'muzakki' => (int) ($zakatBeliMasjid->trx_count ?? 0),
                'value'   => round($zakatBeliKg, 1),
                'unit'    => 'kg',
                'color'   => 'orange',
            ],
            'zakat_mal' => [
                'label'     => 'Zakat Mal',
                'muzakki'   => (int) ($zakatMal->trx_count ?? 0),
                'value'     => $zakatMalAmount,
                'value_fmt' => 'Rp ' . number_format($zakatMalAmount, 0, ',', '.'),
                'unit'      => 'Rp',
                'color'     => 'secondary',
            ],
            'infaq' => [
                'label'     => 'Infaq & Sedekah',
                'muzakki'   => (int) ($infaq->trx_count ?? 0),
                'value'     => $infaqAmount,
                'value_fmt' => 'Rp ' . number_format($infaqAmount, 0, ',', '.'),
                'unit'      => 'Rp',
                'color'     => 'blue',
            ],
            'fidyah' => [
                'label'     => 'Fidyah',
                'muzakki'   => (int) ($fidyah->trx_count ?? 0),
                'value'     => $fidyahAmount,
                'value_fmt' => 'Rp ' . number_format($fidyahAmount, 0, ',', '.'),
                'unit'      => 'Rp',
                'color'     => 'purple',
            ],
        ];
    }

    private function getRiceChartData(int $days): array
    {
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = Carbon::today()->subDays($i)->toDateString();
            $dayName = Carbon::today()->subDays($i)->isoFormat('ddd');

            $riceQty = (float) Rice::whereHas('transactionDetail.transaction', fn($q) =>
                $q->whereDate('date', $date)
            )->sum('quantity');

            $riceSaleQty = (float) RiceSale::whereHas('transactionDetail.transaction', fn($q) =>
                $q->whereDate('date', $date)
            )->sum('quantity');

            $result[] = [
                'date'       => $date,
                'day'        => $dayName,
                'total_rice' => round($riceQty + $riceSaleQty, 1),
            ];
        }

        return $result;
    }

    // ─── MARQUEE ─────────────────────────────────────────────────────────────

    private function getMarqueeData(): array
    {
        $today = Carbon::today()->toDateString();

        return TransactionDetail::whereHas('transaction', fn($q) =>
            $q->whereDate('date', $today)
        )->latest('id')
            ->get(['giver_name', 'type', 'created_at', 'amount', 'quantity'])
            ->map(fn($t) => [
                'name'       => $t->giver_name,
                'type'       => $this->typeLabel($t->type),
                'amount_fmt' => 'Rp ' . number_format($t->amount, 0, ',', '.'),
                'quantity'   => $t->quantity,
                'created_at' => $t->created_at?->format('H:i'),
            ])
            ->toArray();
    }

    // ─── HELPER ──────────────────────────────────────────────────────────────

    private function typeLabel(string $type): string
    {
        return match ($type) {
            TransactionItemType::RICE => 'Zakat Fitrah (Beras)',
            TransactionItemType::RICE_SALES => 'Zakat Fitrah (Beli di Masjid)',
            TransactionItemType::WEALTH => 'Zakat Mal',
            TransactionItemType::DONATION => 'Infaq/Sedekah',
            TransactionItemType::FIDYAH => 'Fidyah',
            default     => ucfirst($type),
        };
    }
}
