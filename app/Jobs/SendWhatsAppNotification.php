<?php

namespace App\Jobs;

use App\Helpers\FidyahHelper;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Transaction;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private string $id,
        private string $waNumber,
        private string $customer,
        private string $address,
        private string $officerName,
        private string $transactionNumber,
        private string $date,
        private array  $items,
    ) {}

    public function handle(WhatsAppService $whatsAppService): bool
    {
        $message = $this->buildMessage();
        Log::info('message ' . $message);
        Log::info('Sending WhatsApp notification to ' . $this->waNumber);

        $success = $whatsAppService->send($this->waNumber, $message);

        if ($success) {
            Transaction::where('id', $this->id)->update(['is_wa_sent' => true]);
        }

        return $success;
    }

    private function buildMessage(): string
    {
        // Normalize: fold day_count into quantity/amount upfront
        $items = FidyahHelper::normalizeItems($this->items);

        $itemTypeLabels = [
            'RICE_SALES' => 'Zakat Fitrah (Beli Beras)',
            'RICE'       => 'Zakat Fitrah (Bawa Beras)',
            'DONATION'   => 'Infaq / Sedekah',
            'FIDYAH'     => 'Fidyah',
            'WEALTH'     => 'Zakat Mall',
        ];

        // --- group item (sama dengan logika di receipt) ---
        $groupedItems = [];
        foreach ($items as $item) {
            $type           = $item['item_type'];
            $currentSubType = '';

            if ($type === 'DONATION' && isset($item['detail']['donation_type'])) {
                $currentSubType = $item['detail']['donation_type'];
            } elseif ($type === 'FIDYAH' && isset($item['detail']['fidyah_type'])) {
                $currentSubType = $item['detail']['fidyah_type'];
            }

            if (!isset($groupedItems[$type])) {
                $groupedItems[$type] = [
                    'label'     => $itemTypeLabels[$type] ?? $type,
                    'sub_types' => [],
                    'count'     => 0,
                    'customers' => [],
                    'quantity'  => 0,
                    'amount'    => 0,
                ];
            }

            $groupedItems[$type]['count'] += 1;

            if ($currentSubType && !in_array($currentSubType, $groupedItems[$type]['sub_types'])) {
                $groupedItems[$type]['sub_types'][] = $currentSubType;
            }

            if (!in_array($item['customer'], $groupedItems[$type]['customers'])) {
                $groupedItems[$type]['customers'][] = $item['customer'];
            }

            if (isset($item['detail']['quantity'])) {
                $groupedItems[$type]['quantity'] += $item['detail']['quantity'];
            }
            if (isset($item['detail']['amount'])) {
                $groupedItems[$type]['amount'] += $item['detail']['amount'];
            }
        }

        // --- hitung total keseluruhan ---
        $moneyTotal = 0;
        $riceTotal  = 0;
        foreach ($items as $item) {
            if (isset($item['detail']['amount'])) {
                $moneyTotal += $item['detail']['amount'];
            }
            if ($item['item_type'] !== 'RICE_SALES' && isset($item['detail']['quantity'])) {
                $riceTotal += $item['detail']['quantity'];
            }
        }

        // --- susun baris item ---
        $itemLines = '';
        foreach ($groupedItems as $group) {
            $label = $group['label'];

            if ($group['count'] > 1) {
                $label .= ' (' . $group['count'] . ')';
            }

            if (in_array('money', $group['sub_types']) && in_array('rice', $group['sub_types'])) {
                $label .= ' - Beras & Uang';
            } elseif (in_array('money', $group['sub_types'])) {
                $label .= ' - Uang';
            } elseif (in_array('rice', $group['sub_types'])) {
                $label .= ' - Beras';
            }

            $customerNames = implode(', ', $group['customers']);
            $qty   = $group['quantity'] > 0 ? $group['quantity'] . ' kg' : '-';
            $money = $group['amount']   > 0 ? 'Rp ' . number_format($group['amount'], 0, ',', '.') : '-';

            $itemLines .= "  • {$label}\n";
            $itemLines .= "    Muzakki : {$customerNames}\n";
            $itemLines .= "    Beras   : {$qty}\n";
            $itemLines .= "    Nominal : {$money}\n";
        }

        $formattedDate  = Carbon::parse($this->date)
            ->locale('id')
            ->translatedFormat('d F Y');
        $formattedMoney = 'Rp ' . number_format($moneyTotal, 0, ',', '.');
        $formattedRice  = $riceTotal . ' kg';

        $appName = config('app.name');
        $message  = "🕌 **{$appName}**\n";
        $message .= "Bukti Transaksi / Kuitansi\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";

        $message .= "📋 *Informasi Transaksi*\n";
        $message .= "No. Nota : {$this->transactionNumber}\n";
        $message .= "Tanggal  : {$formattedDate}\n";
        $message .= "Petugas  : {$this->officerName}\n\n";

        $message .= "👤 *Data Muzakki*\n";
        $message .= "Nama    : {$this->customer}\n";
        $message .= "Alamat  : {$this->address}\n\n";

        $message .= "📦 *Rincian Pembayaran*\n";
        $message .= $itemLines . "\n";

        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💰 Total Uang  : {$formattedMoney}\n";
        $message .= "🌾 Total Beras : {$formattedRice}\n\n";

        $receiptUrl = URL::signedRoute('transactions.receipt', ['id' => $this->id]);
        $message .= "Nota dapat diakses pada link berikut:\n";
        $message .= $receiptUrl . " \n\n";

        $message .= "_Jazakumullahu Khairan_\n";
        $message .= "_Semoga menjadi amal ibadah yang berkah_ 🤲";

        return $message;
    }
}
