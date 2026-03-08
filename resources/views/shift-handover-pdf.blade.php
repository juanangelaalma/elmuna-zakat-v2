<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sera Terima - {{ $handover['shift_name'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1a1a1a;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .header h4 {
            margin: 2px 0 10px 0;
            font-size: 12pt;
            font-weight: normal;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .summary-table th, .summary-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
            text-align: center;
        }

        .summary-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .summary-table .amount {
            text-align: right;
        }

        .section-header td {
            background-color: #d9ead3; /* Light green like in the image */
            font-weight: bold;
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
        }

        .officer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .officer-table th, .officer-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .officer-table th {
            background-color: #f0f0f0;
        }

        .signature-area {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            margin-top: 60px;
            display: inline-block;
            width: 80%;
            border-bottom: 1px solid #000;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>UNIT PENGUMPUL ZAKAT (UPZ)</h3>
        <h4>MASJID AGUNG AL - MUNAWAR</h4>
        <div style="font-size: 9pt;">SEKRETARIAT: JL. WACHID HASYIM NO 02 TULUNGAGUNG 66211</div>
    </div>

    <div class="title">SERAH TERIMA</div>

    <table class="summary-table">
        <thead>
            <tr>
                <th rowspan="2">SHIFT</th>
                <th rowspan="2">TANGGAL</th>
                <th colspan="5">YANG DISERAH TERIMAKAN</th>
                <th colspan="2">JUMLAH</th>
            </tr>
            <tr>
                <th>BERAS (Kg)</th>
                <th>PENJUALAN BERAS (Rp)</th>
                <th>MAAL (Rp)</th>
                <th>FIDYAH</th>
                <th>SHODAQOH</th>
                <th>UANG (Rp)</th>
                <th>BERAS (Kg)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $handover['shift_name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($handover['handover_date'])->translatedFormat('d-m-Y') }}</td>
                
                <!-- Beras -->
                <td>{{ $handover['total_rice_quantity'] > 0 ? (float)$handover['total_rice_quantity'] . ' kg' : '-' }}</td>
                
                <!-- Penjualan Beras -->
                <td class="amount">{{ $handover['total_rice_sale_amount'] > 0 ? 'Rp ' . number_format($handover['total_rice_sale_amount'], 0, ',', '.') : '-' }}</td>
                
                <!-- Maal -->
                <td class="amount">{{ $handover['total_wealth_amount'] > 0 ? 'Rp ' . number_format($handover['total_wealth_amount'], 0, ',', '.') : '-' }}</td>
                
                <!-- Fidyah -->
                <td class="amount">
                    @if($handover['total_fidyah_amount'] > 0)
                        Rp {{ number_format($handover['total_fidyah_amount'], 0, ',', '.') }}<br>
                    @endif
                    @if($handover['total_fidyah_quantity'] > 0)
                        {{ (float)$handover['total_fidyah_quantity'] }} kg
                    @endif
                    @if($handover['total_fidyah_amount'] == 0 && $handover['total_fidyah_quantity'] == 0)
                        -
                    @endif
                </td>
                
                <!-- Shodaqoh -->
                <td class="amount">
                    @if($handover['total_donation_amount'] > 0)
                        Rp {{ number_format($handover['total_donation_amount'], 0, ',', '.') }}<br>
                    @endif
                    @if($handover['total_donation_quantity'] > 0)
                        {{ (float)$handover['total_donation_quantity'] }} kg beras
                    @endif
                    @if($handover['total_donation_amount'] == 0 && $handover['total_donation_quantity'] == 0)
                        -
                    @endif
                </td>
                
                <!-- Jumlah Uang -->
                @php
                    $total_uang = $handover['total_rice_sale_amount'] + $handover['total_wealth_amount'] + $handover['total_fidyah_amount'] + $handover['total_donation_amount'];
                @endphp
                <td class="amount" style="font-weight: bold;">
                    Rp {{ number_format($total_uang, 0, ',', '.') }}
                </td>

                <!-- Jumlah Beras -->
                @php
                    $total_beras = $handover['total_rice_quantity'] + $handover['total_fidyah_quantity'] + $handover['total_donation_quantity'];
                @endphp
                <td style="font-weight: bold;">
                    {{ $total_beras > 0 ? (float)$total_beras . ' kg' : '-' }}
                </td>
            </tr>
            <tr class="section-header">
                <td colspan="7" style="text-align: right; padding-right: 15px;">TOTAL KESELURUHAN</td>
                <td class="amount">Rp {{ number_format($total_uang, 0, ',', '.') }}</td>
                <td>{{ (float)$total_beras }} kg</td>
            </tr>
        </tbody>
    </table>

    <table class="officer-table" style="width: 50%; margin: 0 auto;">
        <thead>
            <tr>
                <th colspan="2">PETUGAS</th>
            </tr>
            <tr>
                <th>SERAH</th>
                <th>TERIMA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 15px;">{{ $handover['handing_over_officer_name'] }}</td>
                <td style="padding: 15px;">{{ $handover['receiving_officer_name'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <div>Yang Menyerahkan,</div>
            <div class="signature-line"></div>
            <div>{{ $handover['handing_over_officer_name'] }}</div>
        </div>
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div class="signature-line"></div>
            <div>( ......................... )</div>
        </div>
        <div class="signature-box">
            <div>Yang Menerima,</div>
            <div class="signature-line"></div>
            <div>{{ $handover['receiving_officer_name'] }}</div>
        </div>
    </div>

</body>
</html>
