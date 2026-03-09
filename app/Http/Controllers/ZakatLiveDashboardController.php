<?php

namespace App\Http\Controllers;

use App\Constants\TransactionItemType;
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

class ZakatLiveDashboardController extends Controller
{
    // ─── Config: model → conditions ──────────────────────────────────────────

    private array $riceModels = [
        Rice::class     => [],
        RiceSale::class => [],
        Fidyah::class   => ['fidyah_type' => 'rice'],
        Donation::class => ['donation_type' => 'rice'],
    ];

    private array $riceModelsWithoutDonationAndFidyah = [
        Rice::class     => [],
        RiceSale::class => [],
    ];

    private array $moneyModels = [
        Donation::class => [],
        Fidyah::class   => [],
        Wealth::class   => [],
        RiceSale::class => [],
    ];

    // ─── Routes ──────────────────────────────────────────────────────────────

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

        $totalMuzakki = TransactionDetail::whereHas(
            'transaction',
            fn($q) => $q->whereDate('date', $today)
        )->count();

        $totalRice  = $this->sumByModels($this->riceModels, 'quantity', $today);
        $totalMoney = $this->sumByModels($this->moneyModels, 'amount', $today);

        return [
            'muzakki'             => $totalMuzakki,
            'total_rice_kg'       => round($totalRice, 1),
            'total_money'         => $totalMoney,
            'total_money_fmt'     => $this->formatRupiah($totalMoney),
            'recent_transactions' => $this->getRecentTransactions($today, 5),
        ];
    }

    private function getRecentTransactions(string $date, int $limit): array
    {
        return Transaction::with(['details' => fn($q) => $q->select('id', 'transaction_id', 'type', 'giver_name')])
            ->whereDate('date', $date)
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'customer', 'created_at'])
            ->map(fn($trx) => [
                'name'       => $trx->customer,
                'types'      => $trx->details->pluck('type')->unique()->map(fn($t) => $this->typeLabel($t))->join(', '),
                'created_at' => $trx->created_at?->format('H:i'),
            ])
            ->toArray();
    }

    // ─── STOCK ───────────────────────────────────────────────────────────────

    private function getStockData(): array
    {
        $totalPurchased = (float) PurchaseRice::sum('quantity');
        $totalAllocated = (float) PurchaseRiceAllocation::sum('quantity');
        $availableStock = max(0, $totalPurchased - $totalAllocated);

        $defaults    = DefaultValue::first();
        $pricePerPkg = (float) $defaults->rice_sales_amount;

        return [
            'available_kg'          => round($availableStock, 1),
            'total_purchased_kg'    => round($totalPurchased, 1),
            'price_per_pkg'         => $pricePerPkg,
            'price_per_pkg_fmt'     => $pricePerPkg ? $this->formatRupiah($pricePerPkg) : 'N/A',
            'default_rice_quantity' => $defaults?->rice_sales_quantity,
        ];
    }

    // ─── OVERALL ─────────────────────────────────────────────────────────────

    private function getOverallData(): array
    {
        $totalRice  = $this->sumByModels($this->riceModelsWithoutDonationAndFidyah, 'quantity');
        $totalMoney = $this->sumByModels($this->moneyModels, 'amount');
        $totalMuzakki = $this->countByModels($this->riceModelsWithoutDonationAndFidyah);

        $defaults     = DefaultValue::first();
        $kgPerPerson  = (float) ($defaults?->beneficiary_rice_kg ?? 5);

        return [
            'muzakki'                 => $totalMuzakki,
            'total_rice_kg'           => round($totalRice, 1),
            'total_money'             => $totalMoney,
            'total_money_fmt'         => $this->formatRupiah($totalMoney),
            'breakdown'               => $this->getCategoryBreakdown(),
            'chart_data'              => $this->getRiceChartData(7),
            'estimated_beneficiaries' => $kgPerPerson > 0 ? (int) floor($totalRice / $kgPerPerson) : 0,
            'beneficiary_rice_kg'     => $kgPerPerson,
        ];
    }

    private function getCategoryBreakdown(): array
    {
        $categories = [
            'zakat_beras' => [
                'label'  => 'Zakat Fitrah (Beras)',
                'type'   => TransactionItemType::RICE,
                'model'  => Rice::class,
                'field'  => 'quantity',
                'unit'   => 'kg',
                'color'  => 'primary',
            ],
            'zakat_beli_masjid' => [
                'label'  => 'Zakat Fitrah (Beli di Masjid)',
                'type'   => TransactionItemType::RICE_SALES,
                'model'  => RiceSale::class,
                'field'  => 'quantity',
                'unit'   => 'kg',
                'color'  => 'orange',
            ],
            'zakat_mal' => [
                'label'  => 'Zakat Mal',
                'type'   => TransactionItemType::WEALTH,
                'model'  => Wealth::class,
                'field'  => 'amount',
                'unit'   => 'Rp',
                'color'  => 'secondary',
            ],
            'infaq' => [
                'label'  => 'Infaq & Sedekah',
                'type'   => TransactionItemType::DONATION,
                'model'  => Donation::class,
                'field'  => 'amount',
                'unit'   => 'Rp',
                'color'  => 'blue',
            ],
            'fidyah' => [
                'label'  => 'Fidyah',
                'type'   => TransactionItemType::FIDYAH,
                'model'  => Fidyah::class,
                'field'  => 'amount',
                'unit'   => 'Rp',
                'color'  => 'purple',
            ],
        ];

        return collect($categories)->map(function ($cat) {
            $muzakki = TransactionDetail::where('type', $cat['type'])
                ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->whereNull('transactions.deleted_at')
                ->distinct('transaction_details.transaction_id')
                ->count();

            $value = (float) $cat['model']::whereHas(
                'transactionDetail',
                fn($q) => $q->where('type', $cat['type'])
            )->sum($cat['field']);

            $result = [
                'label'   => $cat['label'],
                'muzakki' => $muzakki,
                'value'   => $cat['unit'] === 'kg' ? round($value, 1) : $value,
                'unit'    => $cat['unit'],
                'color'   => $cat['color'],
            ];

            if ($cat['unit'] === 'Rp') {
                $result['value_fmt'] = $this->formatRupiah($value);
            }

            return $result;
        })->toArray();
    }

    private function getRiceChartData(int $days): array
    {
        return collect(range($days - 1, 0))
            ->map(function ($i) {
                $date = Carbon::today()->subDays($i)->toDateString();

                $total = $this->sumByModels([
                    Rice::class     => [],
                    RiceSale::class => [],
                ], 'quantity', $date);

                return [
                    'date'       => $date,
                    'day'        => Carbon::parse($date)->isoFormat('ddd'),
                    'total_rice' => round($total, 1),
                ];
            })
            ->toArray();
    }

    // ─── MARQUEE ─────────────────────────────────────────────────────────────

    private function getMarqueeData(): array
    {
        $today = Carbon::today()->toDateString();

        return TransactionDetail::with(['rice', 'riceSale', 'donation', 'fidyah', 'wealth'])
            ->whereHas('transaction', fn($q) => $q->whereDate('date', $today))
            ->latest('id')
            ->get(['id', 'giver_name', 'type', 'created_at'])
            ->map(fn($t) => [
                'name'       => $t->giver_name,
                'type'       => $this->typeLabel($t->type),
                'created_at' => $t->created_at?->format('H:i'),
                'amount'     => (float) $this->resolveDetailValue($t, 'amount'),
                'quantity'   => (float) $this->resolveDetailValue($t, 'quantity'),
            ])
            ->toArray();
    }

    private function resolveDetailValue(TransactionDetail $detail, string $field): mixed
    {
        return match ($detail->type) {
            TransactionItemType::RICE       => $field === 'quantity' ? $detail->rice?->quantity : 0,
            TransactionItemType::RICE_SALES => $detail->riceSale?->$field,
            TransactionItemType::DONATION   => $detail->donation?->$field,
            TransactionItemType::FIDYAH     => $detail->fidyah?->$field,
            TransactionItemType::WEALTH     => $field === 'amount' ? $detail->wealth?->amount : 0,
            default                         => 0,
        };
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

    /**
     * Sum a field across multiple models, optionally filtered by date.
     * $models format: [ModelClass => ['column' => 'value'], ...]
     */
    private function sumByModels(array $models, string $field, ?string $date = null): float
    {
        return collect($models)
            ->map(fn($conditions, $model) => (float) $model::whereHas(
                'transactionDetail.transaction',
                fn($q) => $date ? $q->whereDate('date', $date) : $q
            )->where($conditions)->sum($field))
            ->sum();
    }

    private function countByModels(array $models): int
    {
        return collect($models)
            ->map(fn($conditions, $model) => $model::whereHas('transactionDetail.transaction')
                ->where($conditions)
                ->count())
            ->sum();
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            TransactionItemType::RICE       => 'Zakat Fitrah (Beras)',
            TransactionItemType::RICE_SALES => 'Zakat Fitrah (Beli di Masjid)',
            TransactionItemType::WEALTH     => 'Zakat Mal',
            TransactionItemType::DONATION   => 'Infaq/Sedekah',
            TransactionItemType::FIDYAH     => 'Fidyah',
            default                         => ucfirst($type),
        };
    }
}
