import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import {
    CheckCircle2,
    ChevronLeft,
    Loader2,
    Save,
    WifiOff,
    Zap,
} from 'lucide-react';
import { useEffect, useState } from 'react';

interface PrinterData {
    id?: number;
    name: string;
    ip_address: string;
    port: number;
    protocol: 'raw' | 'ipp';
    paper_size: '58mm' | '80mm' | 'a4';
    is_default: boolean;
    is_active: boolean;
    notes: string | null;
}

interface Props {
    printer: PrinterData | null;
}

type ConnectionStatus = 'idle' | 'checking' | 'online' | 'offline';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengaturan', href: '/settings/profile' },
    { title: 'Print Management', href: '/settings/print-management' },
    { title: 'Form Printer', href: '#' },
];

export default function PrintManagementForm({ printer }: Props) {
    const isEdit = !!printer?.id;

    const [form, setForm] = useState<PrinterData>({
        name: printer?.name ?? '',
        ip_address: printer?.ip_address ?? '',
        port: printer?.port ?? 9100,
        protocol: printer?.protocol ?? 'raw',
        paper_size: printer?.paper_size ?? '80mm',
        is_default: printer?.is_default ?? false,
        is_active: printer?.is_active ?? true,
        notes: printer?.notes ?? '',
    });

    const [errors, setErrors] = useState<Partial<Record<keyof PrinterData, string>>>({});
    const [submitting, setSubmitting] = useState(false);
    const [connectionStatus, setConnectionStatus] = useState<ConnectionStatus>('idle');
    const [connectionMessage, setConnectionMessage] = useState('');

    // Auto-fill port when protocol changes
    useEffect(() => {
        if (form.protocol === 'raw') {
            setForm((prev) => ({ ...prev, port: 9100 }));
        } else {
            setForm((prev) => ({ ...prev, port: 631 }));
        }
    }, [form.protocol]);

    // ── Validation ────────────────────────────────────────────────────────────
    const validate = (): boolean => {
        const newErrors: Partial<Record<keyof PrinterData, string>> = {};

        if (!form.name.trim()) newErrors.name = 'Nama printer wajib diisi.';
        if (!form.ip_address.trim()) {
            newErrors.ip_address = 'IP Address wajib diisi.';
        } else if (!/^(\d{1,3}\.){3}\d{1,3}$/.test(form.ip_address.trim())) {
            newErrors.ip_address = 'Format IP Address tidak valid (contoh: 192.168.1.100).';
        }
        if (!form.port || form.port < 1 || form.port > 65535) {
            newErrors.port = 'Port harus antara 1 dan 65535.';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    // ── Check connection ──────────────────────────────────────────────────────
    const checkConnection = async () => {
        if (!form.ip_address.trim() || !/^(\d{1,3}\.){3}\d{1,3}$/.test(form.ip_address.trim())) {
            setErrors((prev) => ({ ...prev, ip_address: 'Masukkan IP Address yang valid terlebih dahulu.' }));
            return;
        }

        setConnectionStatus('checking');
        setConnectionMessage('');

        try {
            // If editing, use existing printer ID; otherwise we need to test via a temporary approach
            if (isEdit && printer?.id) {
                const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
                const res = await fetch(`/settings/print-management/${printer.id}/test-connection`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                    },
                });
                const data = await res.json();
                setConnectionStatus(data.success ? 'online' : 'offline');
                setConnectionMessage(data.message);
            } else {
                // For new printers, use the check-ip endpoint
                const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
                const res = await fetch('/settings/print-management/check-ip', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ ip_address: form.ip_address, port: form.port }),
                });
                const data = await res.json();
                setConnectionStatus(data.success ? 'online' : 'offline');
                setConnectionMessage(data.message ?? (res.ok ? 'Koneksi berhasil.' : 'Tidak dapat memeriksa koneksi.'));
            }
        } catch {
            setConnectionStatus('offline');
            setConnectionMessage('Gagal memeriksa koneksi printer.');
        }
    };

    // ── Submit ────────────────────────────────────────────────────────────────
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!validate()) return;

        setSubmitting(true);

        const payload = {
            ...form,
            notes: form.notes || null,
        };

        if (isEdit && printer?.id) {
            router.put(`/settings/print-management/${printer.id}`, payload, {
                onError: (errs) => {
                    setErrors(errs as Partial<Record<keyof PrinterData, string>>);
                    setSubmitting(false);
                },
                onFinish: () => setSubmitting(false),
            });
        } else {
            router.post('/settings/print-management', payload, {
                onError: (errs) => {
                    setErrors(errs as Partial<Record<keyof PrinterData, string>>);
                    setSubmitting(false);
                },
                onFinish: () => setSubmitting(false),
            });
        }
    };

    const handleChange = <K extends keyof PrinterData>(key: K, value: PrinterData[K]) => {
        setForm((prev) => ({ ...prev, [key]: value }));
        if (errors[key]) {
            setErrors((prev) => ({ ...prev, [key]: undefined }));
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={isEdit ? 'Edit Printer' : 'Tambah Printer'} />

            <div className="flex flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <Link
                        href="/settings/print-management"
                        className="rounded-lg border border-gray-300 p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800"
                    >
                        <ChevronLeft className="h-4 w-4" />
                    </Link>
                    <div>
                        <h1 className="text-xl font-semibold text-gray-900 dark:text-white">
                            {isEdit ? 'Edit Printer' : 'Tambah Printer Baru'}
                        </h1>
                        <p className="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            {isEdit ? `Mengedit: ${printer?.name}` : 'Tambahkan printer jaringan WiFi/LAN baru.'}
                        </p>
                    </div>
                </div>

                {/* Form */}
                <form onSubmit={handleSubmit} className="max-w-2xl space-y-6">
                    <div className="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-900">
                        <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Informasi Printer
                        </h2>

                        <div className="space-y-4">
                            {/* Nama */}
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nama Printer <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={form.name}
                                    onChange={(e) => handleChange('name', e.target.value)}
                                    placeholder="Contoh: Printer Kasir Utama"
                                    className={`w-full rounded-lg border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-emerald-500 dark:bg-gray-800 dark:text-white ${
                                        errors.name
                                            ? 'border-red-400 focus:ring-red-400'
                                            : 'border-gray-300 dark:border-gray-600'
                                    }`}
                                />
                                {errors.name && <p className="mt-1 text-xs text-red-500">{errors.name}</p>}
                            </div>

                            {/* IP Address + Cek Koneksi */}
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    IP Address <span className="text-red-500">*</span>
                                </label>
                                <div className="flex gap-2">
                                    <input
                                        type="text"
                                        value={form.ip_address}
                                        onChange={(e) => handleChange('ip_address', e.target.value)}
                                        placeholder="Contoh: 192.168.1.100"
                                        className={`flex-1 rounded-lg border px-3 py-2 font-mono text-sm outline-none transition focus:ring-2 focus:ring-emerald-500 dark:bg-gray-800 dark:text-white ${
                                            errors.ip_address
                                                ? 'border-red-400 focus:ring-red-400'
                                                : 'border-gray-300 dark:border-gray-600'
                                        }`}
                                    />
                                    <button
                                        type="button"
                                        onClick={checkConnection}
                                        disabled={connectionStatus === 'checking'}
                                        className="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                                    >
                                        {connectionStatus === 'checking' ? (
                                            <Loader2 className="h-4 w-4 animate-spin" />
                                        ) : (
                                            <Zap className="h-4 w-4" />
                                        )}
                                        Cek Koneksi
                                    </button>
                                </div>
                                {errors.ip_address && <p className="mt-1 text-xs text-red-500">{errors.ip_address}</p>}

                                {/* Connection status feedback */}
                                {connectionStatus !== 'idle' && connectionStatus !== 'checking' && (
                                    <div
                                        className={`mt-2 flex items-center gap-2 rounded-lg px-3 py-2 text-xs ${
                                            connectionStatus === 'online'
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                                : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                        }`}
                                    >
                                        {connectionStatus === 'online' ? (
                                            <CheckCircle2 className="h-3.5 w-3.5 shrink-0" />
                                        ) : (
                                            <WifiOff className="h-3.5 w-3.5 shrink-0" />
                                        )}
                                        {connectionMessage}
                                    </div>
                                )}
                            </div>

                            {/* Protokol + Port */}
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Protokol <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={form.protocol}
                                        onChange={(e) => handleChange('protocol', e.target.value as 'raw' | 'ipp')}
                                        className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                    >
                                        <option value="raw">RAW (ESC/POS)</option>
                                        <option value="ipp">IPP (Internet Printing Protocol)</option>
                                    </select>
                                    <p className="mt-1 text-xs text-gray-500">
                                        {form.protocol === 'raw'
                                            ? 'Untuk printer thermal ESC/POS (port default: 9100)'
                                            : 'Untuk printer yang mendukung IPP (port default: 631)'}
                                    </p>
                                </div>

                                <div>
                                    <label className="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Port <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        value={form.port}
                                        onChange={(e) => handleChange('port', parseInt(e.target.value) || 0)}
                                        min={1}
                                        max={65535}
                                        className={`w-full rounded-lg border px-3 py-2 font-mono text-sm outline-none transition focus:ring-2 focus:ring-emerald-500 dark:bg-gray-800 dark:text-white ${
                                            errors.port
                                                ? 'border-red-400 focus:ring-red-400'
                                                : 'border-gray-300 dark:border-gray-600'
                                        }`}
                                    />
                                    {errors.port && <p className="mt-1 text-xs text-red-500">{errors.port}</p>}
                                </div>
                            </div>

                            {/* Ukuran Kertas */}
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ukuran Kertas <span className="text-red-500">*</span>
                                </label>
                                <div className="flex gap-3">
                                    {(['58mm', '80mm', 'a4'] as const).map((size) => (
                                        <label
                                            key={size}
                                            className={`flex cursor-pointer items-center gap-2 rounded-lg border px-4 py-2.5 text-sm transition ${
                                                form.paper_size === size
                                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                                    : 'border-gray-300 text-gray-700 hover:border-gray-400 dark:border-gray-600 dark:text-gray-300'
                                            }`}
                                        >
                                            <input
                                                type="radio"
                                                name="paper_size"
                                                value={size}
                                                checked={form.paper_size === size}
                                                onChange={() => handleChange('paper_size', size)}
                                                className="sr-only"
                                            />
                                            {size.toUpperCase()}
                                        </label>
                                    ))}
                                </div>
                            </div>

                            {/* Catatan */}
                            <div>
                                <label className="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Catatan <span className="text-xs font-normal text-gray-400">(opsional)</span>
                                </label>
                                <textarea
                                    value={form.notes ?? ''}
                                    onChange={(e) => handleChange('notes', e.target.value)}
                                    placeholder="Catatan tambahan tentang printer ini..."
                                    rows={3}
                                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                />
                            </div>
                        </div>
                    </div>

                    {/* Toggles */}
                    <div className="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-900">
                        <h2 className="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Pengaturan
                        </h2>

                        <div className="space-y-4">
                            {/* Is Default */}
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-700 dark:text-gray-300">Jadikan Default</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        Printer ini akan digunakan secara otomatis saat mencetak struk.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => handleChange('is_default', !form.is_default)}
                                    className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                                        form.is_default ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'
                                    }`}
                                >
                                    <span
                                        className={`inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform ${
                                            form.is_default ? 'translate-x-6' : 'translate-x-1'
                                        }`}
                                    />
                                </button>
                            </div>

                            {/* Is Active */}
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">
                                        Printer nonaktif tidak akan muncul sebagai pilihan saat mencetak.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => handleChange('is_active', !form.is_active)}
                                    className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                                        form.is_active ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'
                                    }`}
                                >
                                    <span
                                        className={`inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform ${
                                            form.is_active ? 'translate-x-6' : 'translate-x-1'
                                        }`}
                                    />
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-3">
                        <button
                            type="submit"
                            disabled={submitting}
                            className="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-60"
                        >
                            {submitting ? (
                                <Loader2 className="h-4 w-4 animate-spin" />
                            ) : (
                                <Save className="h-4 w-4" />
                            )}
                            {isEdit ? 'Simpan Perubahan' : 'Tambah Printer'}
                        </button>
                        <Link
                            href="/settings/print-management"
                            className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Batal
                        </Link>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
