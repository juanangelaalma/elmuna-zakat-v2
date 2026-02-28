<?php

namespace App\Services;

use App\Models\PrintSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PrintService
{
    /**
     * Kirim raw data ke printer via socket (protokol RAW port 9100).
     *
     * @param  PrintSetting  $printer
     * @param  string        $content  Raw ESC/POS atau teks biasa
     * @return array{success: bool, message: string}
     */
    public function printRaw(PrintSetting $printer, string $content): array
    {
        try {
            $socket = @fsockopen($printer->ip_address, $printer->port, $errno, $errstr, 5);

            if (! $socket) {
                return [
                    'success' => false,
                    'message' => "Tidak dapat terhubung ke printer ({$printer->ip_address}:{$printer->port}): {$errstr} (errno {$errno})",
                ];
            }

            fwrite($socket, $content);
            fclose($socket);

            return [
                'success' => true,
                'message' => 'Data berhasil dikirim ke printer.',
            ];
        } catch (\Throwable $e) {
            Log::error('PrintService::printRaw error', [
                'printer' => $printer->name,
                'error'   => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim data ke printer: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim PDF ke printer via IPP (port 631).
     *
     * @param  PrintSetting  $printer
     * @param  string        $pdfPath  Path absolut ke file PDF
     * @return array{success: bool, message: string}
     */
    public function printIPP(PrintSetting $printer, string $pdfPath): array
    {
        try {
            if (! file_exists($pdfPath)) {
                return [
                    'success' => false,
                    'message' => 'File PDF tidak ditemukan: ' . $pdfPath,
                ];
            }

            $pdfContent = file_get_contents($pdfPath);
            if ($pdfContent === false) {
                return [
                    'success' => false,
                    'message' => 'Gagal membaca file PDF.',
                ];
            }

            // Build IPP request (IPP/1.1 Print-Job)
            $ippRequest = $this->buildIPPRequest($pdfContent);

            $url = "http://{$printer->ip_address}:{$printer->port}/ipp/print";

            $context = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => implode("\r\n", [
                        'Content-Type: application/ipp',
                        'Content-Length: ' . strlen($ippRequest),
                        'Connection: close',
                    ]),
                    'content' => $ippRequest,
                    'timeout' => 5,
                ],
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return [
                    'success' => false,
                    'message' => "Tidak dapat terhubung ke printer IPP ({$url}).",
                ];
            }

            return [
                'success' => true,
                'message' => 'Dokumen berhasil dikirim ke printer via IPP.',
            ];
        } catch (\Throwable $e) {
            Log::error('PrintService::printIPP error', [
                'printer' => $printer->name,
                'error'   => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim dokumen ke printer: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Method utama yang dipanggil dari luar.
     * Otomatis pilih metode berdasarkan printer->protocol.
     *
     * @param  PrintSetting  $printer
     * @param  string        $pdfPathOrContent  Path PDF (untuk IPP) atau konten raw (untuk RAW)
     * @return array{success: bool, message: string}
     */
    public function print(PrintSetting $printer, string $pdfPathOrContent): array
    {
        if ($printer->protocol === 'ipp') {
            return $this->printIPP($printer, $pdfPathOrContent);
        }

        // Untuk RAW: jika argumen adalah path file yang ada, baca isinya
        if (file_exists($pdfPathOrContent)) {
            $content = file_get_contents($pdfPathOrContent);
            if ($content === false) {
                return [
                    'success' => false,
                    'message' => 'Gagal membaca file untuk dikirim ke printer.',
                ];
            }
            return $this->printRaw($printer, $content);
        }

        // Jika bukan path file, anggap sebagai konten langsung
        return $this->printRaw($printer, $pdfPathOrContent);
    }

    /**
     * Cek koneksi printer.
     *
     * @param  PrintSetting  $printer
     * @return bool
     */
    public function checkConnection(PrintSetting $printer): bool
    {
        try {
            $socket = @fsockopen($printer->ip_address, $printer->port, $errno, $errstr, 5);
            if ($socket) {
                fclose($socket);
                return true;
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Kirim halaman test print ke printer.
     *
     * @param  PrintSetting  $printer
     * @return array{success: bool, message: string}
     */
    public function sendTestPage(PrintSetting $printer): array
    {
        try {
            if ($printer->protocol === 'raw') {
                // ESC/POS test page
                $testContent = $this->buildTestPageRaw($printer);
                return $this->printRaw($printer, $testContent);
            }

            // IPP: buat PDF test sederhana dan kirim
            $testPdfPath = $this->generateTestPdf($printer);
            if (! $testPdfPath) {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat halaman test.',
                ];
            }

            $result = $this->printIPP($printer, $testPdfPath);

            // Hapus file temporary
            if (file_exists($testPdfPath)) {
                unlink($testPdfPath);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('PrintService::sendTestPage error', [
                'printer' => $printer->name,
                'error'   => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim halaman test: ' . $e->getMessage(),
            ];
        }
    }

    // ── Private Helpers ────────────────────────────────────────────────────────

    /**
     * Build ESC/POS test page content.
     */
    private function buildTestPageRaw(PrintSetting $printer): string
    {
        $ESC = chr(27);
        $LF  = chr(10);
        $GS  = chr(29);

        // Initialize printer
        $init = $ESC . '@';

        // Center align
        $center = $ESC . 'a' . chr(1);

        // Bold on
        $boldOn  = $ESC . 'E' . chr(1);
        $boldOff = $ESC . 'E' . chr(0);

        // Double height/width
        $bigText   = $GS . '!' . chr(17);
        $normalText = $GS . '!' . chr(0);

        // Cut paper
        $cut = $GS . 'V' . chr(66) . chr(0);

        $separator = str_repeat('-', $printer->paper_size === '58mm' ? 32 : 48);

        $content  = $init;
        $content .= $center;
        $content .= $bigText . $boldOn . 'TEST PRINT' . $boldOff . $normalText . $LF;
        $content .= $separator . $LF;
        $content .= $boldOn . 'El Muna Zakat' . $boldOff . $LF;
        $content .= 'Masjid Al Munawwar' . $LF;
        $content .= $separator . $LF;
        $content .= 'Printer : ' . $printer->name . $LF;
        $content .= 'IP      : ' . $printer->ip_address . $LF;
        $content .= 'Port    : ' . $printer->port . $LF;
        $content .= 'Protokol: ' . strtoupper($printer->protocol) . $LF;
        $content .= 'Kertas  : ' . $printer->paper_size . $LF;
        $content .= $separator . $LF;
        $content .= now()->format('d/m/Y H:i:s') . $LF;
        $content .= $separator . $LF;
        $content .= $LF . $LF . $LF;
        $content .= $cut;

        return $content;
    }

    /**
     * Generate a simple test PDF using DomPDF.
     * Returns the absolute path to the generated file, or null on failure.
     */
    private function generateTestPdf(PrintSetting $printer): ?string
    {
        try {
            $html = view('print-test', ['printer' => $printer])->render();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

            $filename = 'test-print-' . $printer->id . '-' . time() . '.pdf';
            $path     = storage_path('app/temp/' . $filename);

            // Pastikan direktori ada
            if (! is_dir(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $pdf->save($path);

            return $path;
        } catch (\Throwable $e) {
            Log::error('PrintService::generateTestPdf error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Build a minimal IPP/1.1 Print-Job request.
     * Reference: RFC 8011
     */
    private function buildIPPRequest(string $pdfContent): string
    {
        // IPP version 1.1
        $version = pack('n', 0x0101);

        // Operation: Print-Job (0x0002)
        $operation = pack('n', 0x0002);

        // Request ID
        $requestId = pack('N', 1);

        // Attribute groups
        $operationAttributes = chr(0x01); // operation-attributes-tag

        // charset
        $charsetTag   = chr(0x47); // charset
        $charsetName  = pack('n', 18) . 'attributes-charset';
        $charsetValue = pack('n', 5) . 'utf-8';

        // natural-language
        $langTag   = chr(0x48); // natural-language
        $langName  = pack('n', 27) . 'attributes-natural-language';
        $langValue = pack('n', 5) . 'en-us';

        // printer-uri
        $printerUriTag   = chr(0x45); // uri
        $printerUriName  = pack('n', 11) . 'printer-uri';
        $printerUriValue = pack('n', 16) . 'ipp://localhost/';

        // job-name
        $jobNameTag   = chr(0x42); // nameWithoutLanguage
        $jobNameName  = pack('n', 8) . 'job-name';
        $jobNameValue = pack('n', 9) . 'Test Page';

        // end-of-attributes
        $endAttributes = chr(0x03);

        $header = $version . $operation . $requestId
            . $operationAttributes
            . $charsetTag . $charsetName . $charsetValue
            . $langTag . $langName . $langValue
            . $printerUriTag . $printerUriName . $printerUriValue
            . $jobNameTag . $jobNameName . $jobNameValue
            . $endAttributes;

        return $header . $pdfContent;
    }
}
