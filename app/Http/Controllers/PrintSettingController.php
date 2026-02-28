<?php

namespace App\Http\Controllers;

use App\Models\PrintSetting;
use App\Services\PrintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * PrintSettingController
 *
 * Handles CRUD for printer settings and AJAX actions:
 * - setDefault, testConnection, testPrint, checkIp
 */

class PrintSettingController extends Controller
{
    public function __construct(private PrintService $printService) {}

    // ── CRUD ──────────────────────────────────────────────────────────────────

    /**
     * Tampilkan daftar printer.
     */
    public function index(): Response
    {
        $printers = PrintSetting::with('creator')
            ->orderByDesc('is_default')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get()
            ->map(fn (PrintSetting $p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'ip_address' => $p->ip_address,
                'port'       => $p->port,
                'protocol'   => $p->protocol,
                'paper_size' => $p->paper_size,
                'is_default' => $p->is_default,
                'is_active'  => $p->is_active,
                'notes'      => $p->notes,
                'created_by' => $p->creator?->name,
                'created_at' => $p->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('settings/print-management/index', [
            'printers' => $printers,
        ]);
    }

    /**
     * Form tambah printer.
     */
    public function create(): Response
    {
        return Inertia::render('settings/print-management/form', [
            'printer' => null,
        ]);
    }

    /**
     * Simpan printer baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'ip_address' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}\d{1,3}$/'],
            'port'       => 'required|integer|min:1|max:65535',
            'protocol'   => 'required|in:raw,ipp',
            'paper_size' => 'required|in:58mm,80mm,a4',
            'is_default' => 'boolean',
            'is_active'  => 'boolean',
            'notes'      => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Jika printer baru dijadikan default, hapus default yang lama
            if (! empty($validated['is_default'])) {
                PrintSetting::where('is_default', true)->update(['is_default' => false]);
            }

            PrintSetting::create([
                ...$validated,
                'is_default' => $validated['is_default'] ?? false,
                'is_active'  => $validated['is_active'] ?? true,
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()
            ->route('print-settings.index')
            ->with('success', 'Printer berhasil ditambahkan.');
    }

    /**
     * Form edit printer.
     */
    public function edit(PrintSetting $printSetting): Response
    {
        return Inertia::render('settings/print-management/form', [
            'printer' => [
                'id'         => $printSetting->id,
                'name'       => $printSetting->name,
                'ip_address' => $printSetting->ip_address,
                'port'       => $printSetting->port,
                'protocol'   => $printSetting->protocol,
                'paper_size' => $printSetting->paper_size,
                'is_default' => $printSetting->is_default,
                'is_active'  => $printSetting->is_active,
                'notes'      => $printSetting->notes,
            ],
        ]);
    }

    /**
     * Update printer.
     */
    public function update(Request $request, PrintSetting $printSetting): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'ip_address' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}\d{1,3}$/'],
            'port'       => 'required|integer|min:1|max:65535',
            'protocol'   => 'required|in:raw,ipp',
            'paper_size' => 'required|in:58mm,80mm,a4',
            'is_default' => 'boolean',
            'is_active'  => 'boolean',
            'notes'      => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $printSetting) {
            // Jika printer ini dijadikan default, hapus default yang lama
            if (! empty($validated['is_default'])) {
                PrintSetting::where('is_default', true)
                    ->where('id', '!=', $printSetting->id)
                    ->update(['is_default' => false]);
            }

            $printSetting->update([
                ...$validated,
                'is_default' => $validated['is_default'] ?? false,
                'is_active'  => $validated['is_active'] ?? true,
            ]);
        });

        return redirect()
            ->route('print-settings.index')
            ->with('success', 'Printer berhasil diperbarui.');
    }

    /**
     * Hapus printer.
     */
    public function destroy(PrintSetting $printSetting): RedirectResponse
    {
        $printSetting->delete();

        return redirect()
            ->route('print-settings.index')
            ->with('success', 'Printer berhasil dihapus.');
    }

    // ── AJAX Actions ──────────────────────────────────────────────────────────

    /**
     * Set printer sebagai default via AJAX.
     */
    public function setDefault(PrintSetting $printSetting): JsonResponse
    {
        try {
            DB::transaction(function () use ($printSetting) {
                PrintSetting::where('is_default', true)->update(['is_default' => false]);
                $printSetting->update(['is_default' => true]);
            });

            return response()->json([
                'success' => true,
                'message' => "Printer \"{$printSetting->name}\" dijadikan default.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah printer default: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cek apakah printer online (ping IP) via AJAX.
     */
    public function testConnection(PrintSetting $printSetting): JsonResponse
    {
        $online = $this->printService->checkConnection($printSetting);

        return response()->json([
            'success' => $online,
            'message' => $online
                ? "Printer \"{$printSetting->name}\" online dan dapat dijangkau."
                : "Printer \"{$printSetting->name}\" tidak dapat dijangkau. Pastikan printer menyala dan terhubung ke jaringan yang sama.",
        ]);
    }

    /**
     * Kirim halaman test print ke printer via AJAX.
     */
    public function testPrint(PrintSetting $printSetting): JsonResponse
    {
        $result = $this->printService->sendTestPage($printSetting);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Cek koneksi ke IP + port yang diberikan (untuk form tambah printer baru).
     * Tidak memerlukan PrintSetting yang sudah tersimpan.
     */
    public function checkIp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ip_address' => ['required', 'string', 'regex:/^(\d{1,3}\.){3}\d{1,3}$/'],
            'port'       => 'required|integer|min:1|max:65535',
        ]);

        try {
            $socket = @fsockopen($validated['ip_address'], $validated['port'], $errno, $errstr, 5);
            if ($socket) {
                fclose($socket);
                return response()->json([
                    'success' => true,
                    'message' => "IP {$validated['ip_address']}:{$validated['port']} dapat dijangkau.",
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "IP {$validated['ip_address']}:{$validated['port']} tidak dapat dijangkau. ({$errstr})",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa koneksi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Return daftar printer aktif sebagai JSON (untuk modal pemilihan printer di halaman transaksi).
     * Dipanggil dengan query param ?json=1
     */
    public function indexJson(): JsonResponse
    {
        $printers = PrintSetting::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'name', 'ip_address', 'protocol', 'paper_size', 'is_active']);

        return response()->json(['printers' => $printers]);
    }
}
