<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Test Print</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            margin: 10mm;
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .separator { border-top: 1px dashed #000; margin: 6px 0; }
        .title { font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 4px; font-size: 9pt; }
        .label { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <div class="center">
        <div class="title">TEST PRINT</div>
        <div class="separator"></div>
        <div class="bold">El Muna Zakat</div>
        <div>Masjid Al Munawwar</div>
        <div class="separator"></div>
    </div>

    <table>
        <tr>
            <td class="label">Printer</td>
            <td>: {{ $printer->name }}</td>
        </tr>
        <tr>
            <td class="label">IP Address</td>
            <td>: {{ $printer->ip_address }}</td>
        </tr>
        <tr>
            <td class="label">Port</td>
            <td>: {{ $printer->port }}</td>
        </tr>
        <tr>
            <td class="label">Protokol</td>
            <td>: {{ strtoupper($printer->protocol) }}</td>
        </tr>
        <tr>
            <td class="label">Ukuran Kertas</td>
            <td>: {{ $printer->paper_size }}</td>
        </tr>
    </table>

    <div class="separator"></div>
    <div class="center">
        <div>Dicetak: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }} WIB</div>
        <div class="bold">Halaman Test Berhasil!</div>
    </div>
</body>
</html>
