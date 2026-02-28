import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import {
    CheckCircle2,
    Edit,
    Loader2,
    Plus,
    Printer,
    Star,
    Trash2,
    WifiOff,
    XCircle,
    Zap,
} from 'lucide-react';
import { useEffect, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengaturan', href: '/settings/profile' },
    { title: 'Print Management', href: '/settings/print-management' },
];

interface PrinterData {
    id: number;
    name: string;
    ip_address: string;
    port: number;
    protocol: 'raw' | 'ipp';
    paper_size: '58mm' | '80mm' | 'a4';
    is_default: boolean;
    is_active: boolean;
    notes: string | null;
    created_by: string | null;
    created_at: string | null;
}

interface Props {
    printers: PrinterData[];
}

type OnlineStatus = 'checking' | 'online' | 'offline' | 'unknown';

interface Toast {
    id: number;
    type: 'success' | 'error';
    message: string;
}

export default function PrintManagementIndex({ printers }: Props) {
    const [onlineStatuses, setOnlineStatuses] = useState<Record<number, OnlineStatus>>({});
    const [loadingActions, setLoadingActions] = useState<Record<string, boolean>>({});
    const [toasts, setToasts] = useState<Toast[]>([]);
    const [deleteConfirm, setDeleteConfirm] = useState<number | null>(null);

    // ── Toast helpers ──────────────────────────────────────────────────────────
    const addToast = (type: 'success' | 'error', message: string) => {
        const id = Date.now();
        setToasts((prev) => [...prev, { id, type, message }]);
        setTimeout(() => setToasts((prev) => prev.filter((t) => t.id !== id)), 4000);
    };

    // ── Check online status for all printers on mount ─────────────────────────
    useEffect(() => {
        const initial: Record<number, OnlineStatus> = {};
        printers.forEach((p) => (initial[p.id] = 'checking'));
        setOnlineStatuses(initial);

        printers.forEach((printer) => {
            checkConnection(printer.id, true);
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // ── Actions ───────────────────────────────────────────────────────────────

    const checkConnection = async (printerId: number, silent = false) => {
        if (!silent) {
            setOnlineStatuses((prev) => ({ ...prev, [printerId]: 'checking' }));
        }

        try {
            const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
            const res = await fetch(`/settings/print-management/${printerId}/test-connection`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            });
            const data = await res.json();
            setOnlineStatuses((prev) => ({ ...prev, [printerId]: data.success ? 'online' : 'offline' }));
            if (!silent) {
                addToast(data.success ? 'success' : 'error', data.message);
            }
        } catch {
            setOnlineStatuses((prev) => ({ ...prev, [printerId]: 'offline' }));
        }
    };

    const setDefault = async (printerId: number) => {
        const key = `default-${printerId}`;
        setLoadingActions((prev) => ({ ...prev, [key]: true }));
        try {
            const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
            const res = await fetch(`/settings/print-management/${printerId}/set-default`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            });
            const data = await res.json();
            addToast(data.success ? 'success' : 'error', data.message);
            if (data.success) {
                router.reload({ only: ['printers'] });
            }
        } catch {
            addToast('error', 'Gagal mengubah printer default.');
        } finally {
            setLoadingActions((prev) => ({ ...prev, [key]: false }));
        }
    };

    const testPrint = async (printerId: number) => {
        const key = `test-${printerId}`;
        setLoadingActions((prev) => ({ ...prev, [key]: true }));
        try {
            const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
            const res = await fetch(`/settings/print-management/${printerId}/test-print`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            });
            const data = await res.json();
            addToast(data.success ? 'success' : 'error', data.message);
        } catch {
            addToast('error', 'Gagal mengirim halaman test.');
        } finally {
            setLoadingActions((prev) => ({ ...prev, [key]: false }));
        }
    };

    const deletePrinter = (printerId: number) => {
        router.delete(`/settings/print-management/${printerId}`, {
            onSuccess: () => addToast('success', 'Printer berhasil dihapus.'),
            onError: () => addToast('error', 'Gagal menghapus printer.'),
        });
        setDeleteConfirm(null);
    };

    // ── Render helpers ────────────────────────────────────────────────────────

    const OnlineBadge = ({ status }: { status: OnlineStatus }) => {
        if (status === 'checking') {
            return (
                <span className="inline-flex items-center gap-1 rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">
                    <Loader2 className="h-3 w-3 animate-spin" />
                    Cek...
                </span>
            );
        }
        if (status === 'online') {
            return (
                <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <CheckCircle2 className="h-3 w-3" />
                    Online
                </span>
            );
        }
        if (status === 'offline') {
            return (
                <span className="inline-flex items-center gap-1 rounded-full bg-destructive/10 px-2 py-0.5 text-xs font-medium text-destructive">
                    <WifiOff className="h-3 w-3" />
                    Offline
                </span>
            );
        }
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">
                <XCircle className="h-3 w-3" />
                Unknown
            </span>
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Print Management" />

            {/* Toast notifications */}
            <div className="fixed right-4 top-4 z-50 flex flex-col gap-2">
                {toasts.map((toast) => (
                    <div
                        key={toast.id}
                        className={`flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium shadow-lg transition-all ${
                            toast.type === 'success'
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-destructive text-white'
                        }`}
                    >
                        {toast.type === 'success' ? (
                            <CheckCircle2 className="h-4 w-4 shrink-0" />
                        ) : (
                            <XCircle className="h-4 w-4 shrink-0" />
                        )}
                        {toast.message}
                    </div>
                ))}
            </div>

            {/* Delete confirmation modal */}
            {deleteConfirm !== null && (
                <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/50">
                    <div className="mx-4 w-full max-w-sm rounded-xl border border-border bg-card p-6 shadow-xl">
                        <h3 className="mb-2 text-lg font-semibold text-card-foreground">Hapus Printer?</h3>
                        <p className="mb-6 text-sm text-muted-foreground">
                            Printer ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <div className="flex justify-end gap-3">
                            <Button
                                variant="outline"
                                onClick={() => setDeleteConfirm(null)}
                            >
                                Batal
                            </Button>
                            <Button
                                variant="destructive"
                                onClick={() => deletePrinter(deleteConfirm)}
                            >
                                Ya, Hapus
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            <div className="flex flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-semibold text-foreground">Print Management</h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Kelola printer jaringan WiFi/LAN untuk mencetak struk zakat.
                        </p>
                    </div>
                    <Button asChild>
                        <Link href="/settings/print-management/create">
                            <Plus className="h-4 w-4" />
                            Tambah Printer
                        </Link>
                    </Button>
                </div>

                {/* Table */}
                <div className="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    {printers.length === 0 ? (
                        <div className="flex flex-col items-center justify-center gap-3 py-16 text-center">
                            <Printer className="h-12 w-12 text-muted-foreground/40" />
                            <p className="text-sm text-muted-foreground">
                                Belum ada printer yang ditambahkan.
                            </p>
                            <Button asChild>
                                <Link href="/settings/print-management/create">
                                    <Plus className="h-4 w-4" />
                                    Tambah Printer Pertama
                                </Link>
                            </Button>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-sidebar-border/70 bg-muted/30 dark:border-sidebar-border">
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Nama</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">IP Address</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Port</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Protokol</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Kertas</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Status Online</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Default</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Aktif</th>
                                        <th className="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {printers.map((printer) => (
                                        <tr
                                            key={printer.id}
                                            className="border-b border-sidebar-border/40 last:border-0 hover:bg-muted/20 dark:border-sidebar-border/40"
                                        >
                                            <td className="px-4 py-3">
                                                <div className="font-medium text-foreground">{printer.name}</div>
                                                {printer.notes && (
                                                    <div className="mt-0.5 text-xs text-muted-foreground">{printer.notes}</div>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 font-mono text-foreground/80">{printer.ip_address}</td>
                                            <td className="px-4 py-3 font-mono text-foreground/80">{printer.port}</td>
                                            <td className="px-4 py-3">
                                                <span className="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium uppercase text-primary">
                                                    {printer.protocol}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-foreground/80">{printer.paper_size}</td>
                                            <td className="px-4 py-3">
                                                <button
                                                    onClick={() => checkConnection(printer.id)}
                                                    title="Klik untuk cek ulang"
                                                    className="cursor-pointer"
                                                >
                                                    <OnlineBadge status={onlineStatuses[printer.id] ?? 'unknown'} />
                                                </button>
                                            </td>
                                            <td className="px-4 py-3">
                                                {printer.is_default ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                                                        <Star className="h-3 w-3 fill-current" />
                                                        Default
                                                    </span>
                                                ) : (
                                                    <span className="text-xs text-muted-foreground">—</span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                {printer.is_active ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                        Aktif
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">
                                                        Nonaktif
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center justify-end gap-1">
                                                    {/* Edit */}
                                                    <Link
                                                        href={`/settings/print-management/${printer.id}/edit`}
                                                        className="rounded-md p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground"
                                                        title="Edit"
                                                    >
                                                        <Edit className="h-4 w-4" />
                                                    </Link>

                                                    {/* Set Default */}
                                                    {!printer.is_default && (
                                                        <button
                                                            onClick={() => setDefault(printer.id)}
                                                            disabled={loadingActions[`default-${printer.id}`]}
                                                            className="rounded-md p-1.5 text-secondary-foreground hover:bg-secondary/30 disabled:opacity-50"
                                                            title="Jadikan Default"
                                                        >
                                                            {loadingActions[`default-${printer.id}`] ? (
                                                                <Loader2 className="h-4 w-4 animate-spin" />
                                                            ) : (
                                                                <Star className="h-4 w-4" />
                                                            )}
                                                        </button>
                                                    )}

                                                    {/* Test Print */}
                                                    <button
                                                        onClick={() => testPrint(printer.id)}
                                                        disabled={loadingActions[`test-${printer.id}`]}
                                                        className="rounded-md p-1.5 text-primary hover:bg-primary/10 disabled:opacity-50"
                                                        title="Test Print"
                                                    >
                                                        {loadingActions[`test-${printer.id}`] ? (
                                                            <Loader2 className="h-4 w-4 animate-spin" />
                                                        ) : (
                                                            <Zap className="h-4 w-4" />
                                                        )}
                                                    </button>

                                                    {/* Delete */}
                                                    <button
                                                        onClick={() => setDeleteConfirm(printer.id)}
                                                        className="rounded-md p-1.5 text-destructive hover:bg-destructive/10"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {/* Legend */}
                <div className="rounded-lg border border-sidebar-border/50 bg-muted/20 p-4 text-xs text-muted-foreground dark:border-sidebar-border">
                    <p className="font-medium text-foreground">Catatan:</p>
                    <ul className="mt-1 list-inside list-disc space-y-1">
                        <li>Klik badge <strong>Status Online</strong> untuk cek ulang koneksi printer.</li>
                        <li>Protokol <strong>RAW</strong> (port 9100) untuk printer thermal ESC/POS.</li>
                        <li>Protokol <strong>IPP</strong> (port 631) untuk printer yang mendukung Internet Printing Protocol.</li>
                        <li>Hanya satu printer yang bisa menjadi <strong>Default</strong> — printer ini yang digunakan saat mencetak struk.</li>
                    </ul>
                </div>
            </div>
        </AppLayout>
    );
}
