<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi - {{ $transaction['transaction_number'] }}</title>
    <style>
        @page {
            size: 110mm 220mm;
            margin: 0;
            padding: 0;
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
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* Wrapper untuk memutar konten 90 derajat berlawanan jarum jam */
        .content-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 205mm; /* Lebar = Tinggi kertas DL border-less */
            height: 105mm; /* Tinggi = Lebar kertas DL border-less */
            transform: translate(-50%, -50%) rotate(90deg);
        }

        /* ── Header ── */
        .header {
            text-align: center;
            padding-bottom: 4px;
            margin-bottom: 6px;
            border-bottom: 2px solid #1a1a1a;
        }

        .header .org-name {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .header .org-sub {
            font-size: 8pt;
            color: #444;
            line-height: 1.2;
        }

        .header .doc-title {
            margin-top: 4px;
            font-size: 10pt;
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
            display: table;
            width: 100%;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
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
            width: 1mm;
            text-align: center;
            padding: 0 5px;
        }

        /* ── Summary ── */
        .summary-section {
            margin-top: 8px;
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

        .print-date {
            font-size: 7pt;
            color: #888;
            /* position: absolute; */
            top: 20px;
            right: 0px;
        }
    </style>
</head>
<body>
<div class="content-wrapper">

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
        <div class="info-col">
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
            </table>
        </div>
        <div class="info-col">
            <table class="info-table">
                <tr>
                    <td class="label">Muzakki (Ketua)</td>
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

            // 1) Grouping Data
            $groupedItems = [];

            foreach ($transaction['items'] as $item) {
                $type = $item['item_type'];
                
                // Kumpulkan subtype (money/rice) tapi jangan pisahkan group bedasarkan ini
                $currentSubType = '';
                if ($type === 'DONATION' && isset($item['detail']['donation_type'])) {
                    $currentSubType = $item['detail']['donation_type'];
                } elseif ($type === 'FIDYAH' && isset($item['detail']['fidyah_type'])) {
                    $currentSubType = $item['detail']['fidyah_type'];
                }

                $groupKey = $type; // Gabungkan semua subtipe ke dalam tipe utamanya

                if (!isset($groupedItems[$groupKey])) {
                    $groupedItems[$groupKey] = [
                        'item_type' => $type,
                        'sub_types' => [], // Simpan array sub_types
                        'label'     => $itemTypeLabels[$type] ?? $type,
                        'count'     => 0,
                        'customers' => [],
                        'quantity'  => 0,
                        'amount'    => 0,
                    ];
                }

                $groupedItems[$groupKey]['count'] += 1;
                
                if ($currentSubType && !in_array($currentSubType, $groupedItems[$groupKey]['sub_types'])) {
                    $groupedItems[$groupKey]['sub_types'][] = $currentSubType;
                }
                
                // Tambahkan customer jika unik
                if (!in_array($item['customer'], $groupedItems[$groupKey]['customers'])) {
                    $groupedItems[$groupKey]['customers'][] = $item['customer'];
                }

                if (isset($item['detail']['quantity'])) {
                    $groupedItems[$groupKey]['quantity'] += $item['detail']['quantity'];
                }
                if (isset($item['detail']['amount'])) {
                    $groupedItems[$groupKey]['amount'] += $item['detail']['amount'];
                }
            }
        @endphp

        <table class="item-detail-table" style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align: left; padding-bottom: 5px; width: 40%">Keterangan</th>
                    <th style="text-align: left; padding-bottom: 5px; width: 35%">Atas Nama (Muzakki)</th>
                    <th style="text-align: right; padding-bottom: 5px; width: 10%">Kuantitas</th>
                    <th style="text-align: right; padding-bottom: 5px; width: 15%">Total Nominal</th>
                </tr>
            </thead>
            <tbody>
            @forelse($groupedItems as $group)
                @php
                    $displayLabel = $group['label'];
                    if ($group['count'] > 1) {
                        $displayLabel .= " (" . $group['count'] . ")";
                    }
                    
                    // Format penamaan label jika ada uang & beras
                    if (count($group['sub_types']) > 0) {
                        if (in_array('money', $group['sub_types']) && in_array('rice', $group['sub_types'])) {
                            $displayLabel .= " - Beras & Uang";
                        } elseif (in_array('money', $group['sub_types'])) {
                            $displayLabel .= " - Uang";
                        } elseif (in_array('rice', $group['sub_types'])) {
                            $displayLabel .= " - Beras";
                        }
                    }

                    $customerNames = implode(', ', $group['customers']);
                @endphp

                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 4px 0; font-weight: bold;">{{ $displayLabel }}</td>
                    <td style="padding: 4px 0;">{{ $customerNames }}</td>
                    <td style="text-align: right; padding: 4px 0; font-weight: bold;">
                        @if($group['quantity'] > 0)
                            {{ $group['quantity'] }} kg
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: right; padding: 4px 0;">
                        @if($group['amount'] > 0)
                            Rp {{ number_format($group['amount'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="no-item" style="text-align: center; padding: 10px;">Tidak ada item dalam transaksi ini.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

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

    <table style="width: 100%; font-size: 9pt;position: absolute; bottom: 60px; left: 0;">
        <tr>
            <!-- Letak tanda tangan / info ringkas -->
            <td style="width: 40%; vertical-align: top; text-align: center; font-size: 8.5pt;">
                <div style="font-weight: bold;">Jazakumullahu Khairan</div>
                    <div style="color: #555; margin-top: 2px;">Semoga menjadi amal ibadah yang berkah</div>
                    <div class="print-date">Dicetak: {{ now()->timezone('Asia/Jakarta')->locale('id_ID')->translatedFormat('d F Y, H:i') }} WIB</div>
                </td>
                
                <!-- Letak Total Uang & Beras -->
                <td style="width: 60%; vertical-align: top;">
                    <table class="summary-table" style="width: 100%;">
                        <tr>
                            <td class="s-label" style="text-align: right; padding-right: 15px;">Total Uang</td>
                            <td class="s-sep" style="width: 5px;">:</td>
                            <td class="s-value" style="width: 30%;">Rp {{ number_format($moneyTotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="s-label" style="text-align: right; padding-right: 15px;">Total Beras</td>
                            <td class="s-sep" style="width: 5px;">:</td>
                            <td class="s-value" style="width: 30%; font-weight: bold;">{{ $riceTotal }} kg</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</div> <!-- /.content-wrapper -->
</body>
</html>
