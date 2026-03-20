import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import axios from 'axios';
import {
    Calendar,
    CheckCircle2,
    MessageSquare,
    Phone,
    RefreshCw,
    Send,
    User,
    XCircle,
} from 'lucide-react';
import { useCallback, useMemo, useState } from 'react';

interface WaTransaction {
    id: number;
    transaction_number: string;
    date: string;
    customer: string;
    wa_number: string;
    officer_name: string;
    is_wa_sent: boolean;
}

type FilterStatus = 'all' | 'sent' | 'not_sent';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transaksi', href: '/transactions/index' },
    { title: 'Manajemen WA', href: '#' },
];

function WaBadge({ sent }: { sent: boolean }) {
    if (sent) {
        return (
            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                <CheckCircle2 className="h-3 w-3" />
                Terkirim
            </span>
        );
    }
    return (
        <span className="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-400">
            <XCircle className="h-3 w-3" />
            Belum Terkirim
        </span>
    );
}

export default function WaManagement() {
    const { transactions: initialTransactions } = usePage<{
        transactions: WaTransaction[];
    }>().props;

    // Local state to allow optimistic badge updates without full page reload
    const [transactions, setTransactions] = useState<WaTransaction[]>(initialTransactions);
    const [loadingIds, setLoadingIds] = useState<Set<number>>(new Set());
    const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set());
    const [bulkLoading, setBulkLoading] = useState(false);
    const [filterStatus, setFilterStatus] = useState<FilterStatus>('all');
    const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' } | null>(null);

    const showToast = (msg: string, type: 'success' | 'error') => {
        setToast({ msg, type });
        setTimeout(() => setToast(null), 4000);
    };

    const filteredTransactions = useMemo(() => {
        if (filterStatus === 'sent') return transactions.filter((t) => t.is_wa_sent);
        if (filterStatus === 'not_sent') return transactions.filter((t) => !t.is_wa_sent);
        return transactions;
    }, [transactions, filterStatus]);

    const markSent = useCallback((id: number) => {
        setTransactions((prev) =>
            prev.map((t) => (t.id === id ? { ...t, is_wa_sent: true } : t)),
        );
    }, []);

    const handleResend = (id: number) => {
        setLoadingIds((prev) => new Set(prev).add(id));

        axios.post(`/transactions/${id}/resend-wa`, {}, {
            headers: { Accept: 'application/json' }
        })
            .then((res) => {
                const data = res.data;
                if (data.success) {
                    markSent(id);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch((err) => {
                const msg = err.response?.data?.message || 'Terjadi kesalahan jaringan.';
                showToast(msg, 'error');
            })
            .finally(() =>
                setLoadingIds((prev) => {
                    const next = new Set(prev);
                    next.delete(id);
                    return next;
                }),
            );
    };

    const handleMarkSent = (id: number) => {
        setLoadingIds((prev) => new Set(prev).add(id));
        axios.post(`/transactions/${id}/mark-wa-sent`, {}, {
            headers: { Accept: 'application/json' }
        })
            .then((res) => {
                const data = res.data;
                if (data.success) {
                    markSent(id);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch((err) => {
                const msg = err.response?.data?.message || 'Terjadi kesalahan jaringan.';
                showToast(msg, 'error');
            })
            .finally(() =>
                setLoadingIds((prev) => {
                    const next = new Set(prev);
                    next.delete(id);
                    return next;
                }),
            );
    };

    const handleBulkResend = () => {
        if (selectedIds.size === 0) return;
        setBulkLoading(true);

        axios.post('/transactions/bulk-resend-wa', { ids: Array.from(selectedIds) }, {
            headers: { Accept: 'application/json' }
        })
            .then((res) => {
                const data = res.data;
                (data.results as { id: number; success: boolean }[]).forEach((r) => {
                    if (r.success) markSent(r.id);
                });
                showToast(data.message, data.success ? 'success' : 'error');
                setSelectedIds(new Set());
            })
            .catch((err) => {
                const msg = err.response?.data?.message || 'Terjadi kesalahan jaringan.';
                showToast(msg, 'error');
            })
            .finally(() => setBulkLoading(false));
    };

    const toggleSelect = (id: number) => {
        setSelectedIds((prev) => {
            const next = new Set(prev);
            if (next.has(id)) next.delete(id);
            else next.add(id);
            return next;
        });
    };

    const toggleSelectAll = () => {
        const actionable = filteredTransactions.filter(t => !t.is_wa_sent);
        if (selectedIds.size === actionable.length && actionable.length > 0) {
            setSelectedIds(new Set());
        } else {
            setSelectedIds(new Set(actionable.map((t) => t.id)));
        }
    };

    const actionableCount = filteredTransactions.filter(t => !t.is_wa_sent).length;
    const allChecked = actionableCount > 0 && selectedIds.size === actionableCount;
    const someChecked = selectedIds.size > 0 && !allChecked;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Manajemen WA" />

            {/* Toast notification */}
            {toast && (
                <div
                    className={`fixed right-4 top-4 z-50 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium shadow-lg transition-all ${
                        toast.type === 'success'
                            ? 'bg-emerald-500 text-white'
                            : 'bg-red-500 text-white'
                    }`}
                >
                    {toast.type === 'success' ? (
                        <CheckCircle2 className="h-4 w-4 shrink-0" />
                    ) : (
                        <XCircle className="h-4 w-4 shrink-0" />
                    )}
                    {toast.msg}
                </div>
            )}

            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Page header */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-3">
                        <div className="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-2.5 shadow">
                            <MessageSquare className="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h1 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Manajemen WhatsApp
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                {transactions.length} transaksi dengan nomor WA
                            </p>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        {/* Filter dropdown */}
                        <select
                            value={filterStatus}
                            onChange={(e) => {
                                setFilterStatus(e.target.value as FilterStatus);
                                setSelectedIds(new Set());
                            }}
                            className="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                        >
                            <option value="all">Semua</option>
                            <option value="sent">Sudah Terkirim</option>
                            <option value="not_sent">Belum Terkirim</option>
                        </select>

                        {/* Bulk resend button */}
                        {selectedIds.size > 0 && (
                            <button
                                onClick={handleBulkResend}
                                disabled={bulkLoading}
                                className="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow transition hover:bg-emerald-700 disabled:opacity-60"
                            >
                                {bulkLoading ? (
                                    <RefreshCw className="h-4 w-4 animate-spin" />
                                ) : (
                                    <Send className="h-4 w-4" />
                                )}
                                Kirim Ulang Terpilih ({selectedIds.size})
                            </button>
                        )}
                    </div>
                </div>

                {/* Stats row */}
                <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div className="rounded-xl border border-sidebar-border/70 bg-background p-4 dark:border-sidebar-border">
                        <p className="text-xs text-muted-foreground">Total</p>
                        <p className="mt-1 text-2xl font-bold text-foreground">
                            {transactions.length}
                        </p>
                    </div>
                    <div className="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/40">
                        <p className="text-xs text-emerald-600 dark:text-emerald-400">Terkirim</p>
                        <p className="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">
                            {transactions.filter((t) => t.is_wa_sent).length}
                        </p>
                    </div>
                    <div className="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950/40">
                        <p className="text-xs text-red-600 dark:text-red-400">Belum Terkirim</p>
                        <p className="mt-1 text-2xl font-bold text-red-700 dark:text-red-300">
                            {transactions.filter((t) => !t.is_wa_sent).length}
                        </p>
                    </div>
                </div>

                {/* Table */}
                <div className="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    {filteredTransactions.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-20 text-center">
                            <MessageSquare className="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600" />
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Tidak ada transaksi yang cocok
                            </p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                                    <tr>
                                        <th className="px-4 py-3 text-left">
                                            <input
                                                type="checkbox"
                                                checked={allChecked}
                                                ref={(el) => {
                                                    if (el) el.indeterminate = someChecked;
                                                }}
                                                onChange={toggleSelectAll}
                                                className="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                            />
                                        </th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                            No. Nota
                                        </th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                            Muzakki
                                        </th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                            Tanggal
                                        </th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                            No. WA
                                        </th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">
                                            Status WA
                                        </th>
                                        <th className="px-4 py-3 text-center font-medium text-muted-foreground">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-sidebar-border/50 dark:divide-sidebar-border">
                                    {filteredTransactions.map((trx) => {
                                        const isLoading = loadingIds.has(trx.id);
                                        const isSelected = selectedIds.has(trx.id);
                                        return (
                                            <tr
                                                key={trx.id}
                                                className={`bg-background transition-colors hover:bg-muted/30 ${isSelected ? 'bg-emerald-50/50 dark:bg-emerald-950/20' : ''}`}
                                            >
                                                <td className="px-4 py-3">
                                                    {!trx.is_wa_sent && (
                                                        <input
                                                            type="checkbox"
                                                            checked={isSelected}
                                                            onChange={() => toggleSelect(trx.id)}
                                                            className="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                                        />
                                                    )}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            router.visit(
                                                                `/transactions/${trx.id}`,
                                                            )
                                                        }
                                                        className="rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 transition hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800"
                                                    >
                                                        {trx.transaction_number}
                                                    </button>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex items-center gap-2">
                                                        <User className="h-3.5 w-3.5 text-muted-foreground" />
                                                        <span className="font-medium text-foreground">
                                                            {trx.customer}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex items-center gap-2 text-muted-foreground">
                                                        <Calendar className="h-3.5 w-3.5 shrink-0" />
                                                        {new Date(trx.date).toLocaleDateString(
                                                            'id-ID',
                                                            {
                                                                day: 'numeric',
                                                                month: 'short',
                                                                year: 'numeric',
                                                            },
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex items-center gap-2 text-muted-foreground">
                                                        <Phone className="h-3.5 w-3.5 shrink-0" />
                                                        {trx.wa_number}
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <WaBadge sent={trx.is_wa_sent} />
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <div className="flex items-center justify-center gap-2">
                                                        {!trx.is_wa_sent && trx.wa_number && (
                                                            <button
                                                                type="button"
                                                                onClick={() => window.open(`/transactions/${trx.id}/manual-wa`, '_blank')}
                                                                title="Kirim WA Manual"
                                                                className="inline-flex items-center gap-1.5 rounded-lg border border-emerald-600 px-3 py-1.5 text-xs font-medium text-emerald-600 shadow-sm transition hover:bg-emerald-50 disabled:opacity-60"
                                                            >
                                                                <MessageSquare className="h-3.5 w-3.5" />
                                                                Manual
                                                            </button>
                                                        )}
                                                        {!trx.is_wa_sent && (
                                                            <button
                                                                type="button"
                                                                onClick={() => handleResend(trx.id)}
                                                                disabled={isLoading}
                                                                title="Kirim Ulang WA"
                                                                className="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-emerald-700 disabled:opacity-60"
                                                            >
                                                                {isLoading ? (
                                                                    <RefreshCw className="h-3.5 w-3.5 animate-spin" />
                                                                ) : (
                                                                    <Send className="h-3.5 w-3.5" />
                                                                )}
                                                                {isLoading ? 'Mengirim...' : 'Kirim Ulang'}
                                                            </button>
                                                        )}
                                                        {!trx.is_wa_sent && (
                                                            <button
                                                                type="button"
                                                                onClick={() => handleMarkSent(trx.id)}
                                                                disabled={isLoading}
                                                                title="Tandai WA Sudah Terkirim"
                                                                className="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 disabled:opacity-60 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                                            >
                                                                {isLoading ? (
                                                                    <RefreshCw className="h-3.5 w-3.5 animate-spin text-emerald-600" />
                                                                ) : (
                                                                    <CheckCircle2 className="h-3.5 w-3.5 text-emerald-600" />
                                                                )}
                                                                Tandai
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
