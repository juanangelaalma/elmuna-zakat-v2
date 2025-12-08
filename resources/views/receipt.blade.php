<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaction['transaction_number'] }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 5mm 2mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            width: 54mm;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 8px;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .info-section {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }

        .info-label {
            font-weight: bold;
        }

        .items-section {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .item {
            margin-bottom: 6px;
            font-size: 9px;
        }

        .item-header {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-detail {
            margin-left: 5px;
            margin-bottom: 1px;
        }

        .summary-section {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 8px;
        }

        .divider {
            border-bottom: 1px solid #000;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>EL MUNA ZAKAT</h1>
        <p>Jl. Kyai Haji Wahid Hasyim No.2B</p>
        <p>Hutan, Kauman, Kec. Tulungagung</p>
        <p>Kabupaten Tulungagung</p>
        <p>Jawa Timur 66261</p>
    </div>

    <!-- Transaction Info -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">No. Nota:</span>
            <span>{{ $transaction['transaction_number'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal:</span>
            <span>{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Petugas:</span>
            <span>{{ $transaction['officer_name'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nama:</span>
            <span>{{ $transaction['customer'] }}</span>
        </div>
        @if($transaction['wa_number'])
        <div class="info-row">
            <span class="info-label">WA:</span>
            <span>{{ $transaction['wa_number'] }}</span>
        </div>
        @endif
    </div>

    <!-- Items -->
    <div class="items-section">
        @foreach($transaction['items'] as $item)
        <div class="item">
            @php
                $itemTypeLabels = [
                    'RICE_SALES' => 'Penjualan Beras',
                    'RICE' => 'Zakat Beras',
                    'DONATION' => 'Infaq',
                    'FIDYAH' => 'Fidyah',
                    'WEALTH' => 'Zakat Mall'
                ];
                $label = $itemTypeLabels[$item['item_type']] ?? $item['item_type'];
            @endphp
            
            <div class="item-header">{{ $label }}</div>
            <div class="item-detail">Muzakki: {{ $item['customer'] }}</div>
            
            @if($item['item_type'] === 'RICE_SALES')
                <div class="item-detail">Qty: {{ $item['detail']['quantity'] }} kg</div>
                <div class="item-detail">Total: Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</div>
            
            @elseif($item['item_type'] === 'RICE')
                <div class="item-detail">Qty: {{ $item['detail']['quantity'] }} kg</div>
            
            @elseif($item['item_type'] === 'DONATION')
                <div class="item-detail">Tipe: {{ $item['detail']['donation_type'] === 'money' ? 'Uang' : 'Beras' }}</div>
                @if($item['detail']['donation_type'] === 'money')
                    <div class="item-detail">Amount: Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</div>
                @else
                    <div class="item-detail">Qty: {{ $item['detail']['quantity'] }} kg</div>
                @endif
            
            @elseif($item['item_type'] === 'FIDYAH')
                <div class="item-detail">Tipe: {{ $item['detail']['fidyah_type'] === 'money' ? 'Uang' : 'Beras' }}</div>
                @if($item['detail']['fidyah_type'] === 'money')
                    <div class="item-detail">Amount: Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</div>
                @else
                    <div class="item-detail">Qty: {{ $item['detail']['quantity'] }} kg</div>
                @endif
            
            @elseif($item['item_type'] === 'WEALTH')
                <div class="item-detail">Amount: Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="summary-section">
        @php
            $moneyTotal = 0;
            $riceTotal = 0;
            
            foreach($transaction['items'] as $item) {
                if(isset($item['detail']['amount'])) {
                    $moneyTotal += $item['detail']['amount'];
                }
                if($item['item_type'] !== 'RICE_SALES' && isset($item['detail']['quantity'])) {
                    $riceTotal += $item['detail']['quantity'];
                }
            }
        @endphp
        
        <div class="summary-row">
            <span>Total Uang:</span>
            <span>Rp {{ number_format($moneyTotal, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Beras:</span>
            <span>{{ $riceTotal }} kg</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Terima Kasih</p>
        <p>Semoga Berkah</p>
        <p style="margin-top: 5px;">{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
