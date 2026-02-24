<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi - {{ $transaction['transaction_number'] }}</title>
    <style>
        @page {
            size: 110mm 220mm;
            padding: 7mm 7mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1a1a1a;
            width: 90%;
            margin: auto;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            padding-bottom: 6px;
            margin-bottom: 6px;
            border-bottom: 2px solid #1a1a1a;
        }

        .header .org-name {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .header .org-sub {
            font-size: 7.5pt;
            color: #444;
            line-height: 1.3;
        }

        .header .doc-title {
            margin-top: 5px;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }

        /* ── Info Grid ── */
        .info-section {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #999;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1.5px 0;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .info-table .label {
            width: 30%;
            font-weight: bold;
            color: #333;
        }

        .info-table .sep {
            width: 5mm;
            text-align: center;
        }

        .info-table .value {
            color: #1a1a1a;
        }

        /* ── Items ── */
        .items-section {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #999;
        }

        .section-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
            margin-bottom: 4px;
        }

        .item {
            margin-bottom: 5px;
            padding: 4px 5px;
            background: #f5f5f5;
            border-left: 2px solid #1a1a1a;
        }

        .item-name {
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-detail-table td {
            font-size: 8pt;
            padding: 0.5px 0;
            color: #333;
            vertical-align: top;
        }

        .item-detail-table .dl {
            width: 30%;
        }

        .item-detail-table .ds {
            width: 4mm;
            text-align: center;
        }

        /* ── Summary ── */
        .summary-section {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #999;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 2px 0;
            font-size: 9pt;
            font-weight: bold;
        }

        .summary-table .s-label {
            color: #333;
        }

        .summary-table .s-sep {
            width: 5mm;
            text-align: center;
        }

        .summary-table .s-value {
            text-align: right;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            margin-top: 6px;
        }

        .footer .thanks {
            font-size: 8.5pt;
            font-weight: bold;
        }

        .footer .bless {
            font-size: 7.5pt;
            color: #555;
            margin-top: 1px;
        }

        .footer .timestamp {
            font-size: 7pt;
            color: #888;
            margin-top: 5px;
        }

        .no-item {
            font-size: 8pt;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header">
        <div class="org-name">El Muna Zakat</div>
        <div class="org-sub">
            Jl. Kyai Haji Wahid Hasyim No.2B, Hutan, Kauman, <br> Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66261
        </div>
        <div class="doc-title">Bukti Transaksi / Kuitansi</div>
    </div>

    {{-- ── Info Transaksi ── --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">No. Nota</td>
                <td class="sep">:</td>
                <td class="value">{{ $transaction['transaction_number'] }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal</td>
                <td class="sep">:</td>
                <td class="value">{{ \Carbon\Carbon::parse($transaction['date'])->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Petugas</td>
                <td class="sep">:</td>
                <td class="value">{{ $transaction['officer_name'] }}</td>
            </tr>
            <tr>
                <td class="label">Muzakki</td>
                <td class="sep">:</td>
                <td class="value">{{ $transaction['customer'] }}</td>
            </tr>
            @if($transaction['address'])
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td class="value">{{ $transaction['address'] }}</td>
            </tr>
            @endif
            @if($transaction['wa_number'])
            <tr>
                <td class="label">No. WhatsApp</td>
                <td class="sep">:</td>
                <td class="value">{{ $transaction['wa_number'] }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ── Rincian Item ── --}}
    <div class="items-section">
        <div class="section-title">Rincian Pembayaran</div>

        @php
            $itemTypeLabels = [
                'RICE_SALES' => 'Penjualan Beras',
                'RICE'       => 'Zakat Fitrah (Beras)',
                'DONATION'   => 'Infaq / Sedekah',
                'FIDYAH'     => 'Fidyah',
                'WEALTH'     => 'Zakat Mall',
            ];
        @endphp

        @forelse($transaction['items'] as $item)
            @php
                $label = $itemTypeLabels[$item['item_type']] ?? $item['item_type'];
            @endphp

            <div class="item">
                <div class="item-name">{{ $label }}</div>
                <table class="item-detail-table">
                    @if($item['customer'] !== $transaction['customer'])
                    <tr>
                        <td class="dl">Atas Nama</td>
                        <td class="ds">:</td>
                        <td>{{ $item['customer'] }}</td>
                    </tr>
                    @endif

                    @if($item['item_type'] === 'RICE_SALES')
                        <tr>
                            <td class="dl">Jumlah Beras</td>
                            <td class="ds">:</td>
                            <td>{{ $item['detail']['quantity'] }} kg</td>
                        </tr>
                        <tr>
                            <td class="dl">Harga</td>
                            <td class="ds">:</td>
                            <td>Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</td>
                        </tr>

                    @elseif($item['item_type'] === 'RICE')
                        <tr>
                            <td class="dl">Jumlah Beras</td>
                            <td class="ds">:</td>
                            <td>{{ $item['detail']['quantity'] }} kg</td>
                        </tr>

                    @elseif($item['item_type'] === 'DONATION')
                        <tr>
                            <td class="dl">Jenis</td>
                            <td class="ds">:</td>
                            <td>{{ $item['detail']['donation_type'] === 'money' ? 'Uang' : 'Beras' }}</td>
                        </tr>
                        @if($item['detail']['donation_type'] === 'money')
                        <tr>
                            <td class="dl">Nominal</td>
                            <td class="ds">:</td>
                            <td>Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="dl">Jumlah Beras</td>
                            <td class="ds">:</td>
                            <td>{{ $item['detail']['quantity'] }} kg</td>
                        </tr>
                        @endif

                    @elseif($item['item_type'] === 'FIDYAH')
                        <tr>
                            <td class="dl">Jenis</td>
                            <td class="ds">:</td>
                            <td>{{ $item['detail']['fidyah_type'] === 'money' ? 'Uang' : 'Beras' }}</td>
                        </tr>
                        @if($item['detail']['fidyah_type'] === 'money')
                        <tr>
                            <td class="dl">Nominal</td>
                            <td class="ds">:</td>
                            <td>Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="dl">Jumlah Beras</td>
                            <td class="ds">:</td>
                            <td>{{ $item['detail']['quantity'] }} kg</td>
                        </tr>
                        @endif

                    @elseif($item['item_type'] === 'WEALTH')
                        <tr>
                            <td class="dl">Nominal</td>
                            <td class="ds">:</td>
                            <td>Rp {{ number_format($item['detail']['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @empty
            <p class="no-item">Tidak ada item dalam transaksi ini.</p>
        @endforelse
    </div>

    {{-- ── Ringkasan Total ── --}}
    <div class="summary-section">
        @php
            $moneyTotal = 0;
            $riceTotal  = 0;

            foreach ($transaction['items'] as $item) {
                if (isset($item['detail']['amount'])) {
                    $moneyTotal += $item['detail']['amount'];
                }
                if ($item['item_type'] !== 'RICE_SALES' && isset($item['detail']['quantity'])) {
                    $riceTotal += $item['detail']['quantity'];
                }
            }
        @endphp

        <table class="summary-table">
            <tr>
                <td class="s-label">Total Uang</td>
                <td class="s-sep">:</td>
                <td class="s-value">Rp {{ number_format($moneyTotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="s-label">Total Beras</td>
                <td class="s-sep">:</td>
                <td class="s-value">{{ $riceTotal }} kg</td>
            </tr>
        </table>
    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="thanks">Jazakumullahu Khairan</div>
        <div class="bless">Semoga menjadi amal ibadah yang berkah</div>
        <div class="timestamp">Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

</body>
</html>
