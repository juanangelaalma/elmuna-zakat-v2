import { OverviewCard } from '@/components/overview-card';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/app-layout';
import { RICE_SALES_ID } from '@/lib/constant';
import { formatCurrency } from '@/lib/utils';
import TransactionItemDetailCard from '@/pages/transactions/forms/transaction-item-detail-card';
import { transactions } from '@/routes';
import { BreadcrumbItem, TransactionItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    Calendar,
    CheckCircle2,
    DollarSign,
    Loader2,
    MapPin,
    Package,
    Phone,
    Printer,
    Trash2,
    User,
    WifiOff,
    XCircle,
} from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: transactions().url,
    },
    {
        title: 'Detail Transaksi',
        href: '#',
    },
];

interface Transaction {
    id: number;
    transaction_number: string;
    date: string;
    customer: string;
    address: string;
    wa_number: string;
    officer_name: string;
    items: TransactionItem[];
}

// Printer selection modal for when no default printer is set
interface PrinterOption {
    id: number;
    name: string;
    ip_address: string;
    protocol: string;
    paper_size: string;
    is_active: boolean;
}

type PrintStatus = 'idle' | 'loading' | 'success' | 'error';

interface Toast {
    type: 'success' | 'error';
    message: string;
}

export default function TransactionDetail() {
    const { transaction } = usePage<{ transaction: Transaction }>().props;

    const moneyTotal = transaction.items.reduce((total, item) => {
        if ('amount' in item.detail) {
            return total + (Number(item.detail.amount) ?? 0);
        }
        return total;
    }, 0);

    const riceTotal = transaction.items.reduce((total, item) => {
        if (item.item_type !== RICE_SALES_ID && 'quantity' in item.detail) {
            return total + (Number(item.detail.quantity) ?? 0);
        }
        return total;
    }, 0);

    const handleGenerateReceipt = () => {
        window.open(`/transactions/${transaction.id}/receipt`, '_blank');
    };

    const [showDeleteDialog, setShowDeleteDialog] = useState(false);
    const [printStatus, setPrintStatus] = useState<PrintStatus>('idle');
    const [printToast, setPrintToast] = useState<Toast | null>(null);
    const [showPrinterModal, setShowPrinterModal] = useState(false);
    const [availablePrinters, setAvailablePrinters] = useState<PrinterOption[]>([]);

    const showToast = (type: 'success' | 'error', message: string) => {
        setPrintToast({ type, message });
        setTimeout(() => setPrintToast(null), 4000);
    };

    const sendToPrinter = async (printerId?: number) => {
        setPrintStatus('loading');
        setShowPrinterModal(false);

        try {
            const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
            const url = `/transactions/${transaction.id}/print-to-printer`;
            const body = printerId ? JSON.stringify({ printer_id: printerId }) : undefined;

            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body,
            });

            const data = await res.json();

            if (data.success) {
                setPrintStatus('success');
                showToast('success', 'Struk berhasil dikirim ke printer!');
                setTimeout(() => setPrintStatus('idle'), 3000);
            } else if (data.no_default) {
                // No default printer — fetch available printers and show modal
                setPrintStatus('idle');
                const csrfToken2 = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
                const printersRes = await fetch('/settings/print-management/printers/json', {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken2 },
                });
                if (printersRes.ok) {
                    const printersData = await printersRes.json();
                    setAvailablePrinters(printersData.printers ?? []);
                    setShowPrinterModal(true);
                } else {
                    showToast('error', 'Tidak ada printer default aktif. Silakan atur printer di menu Print Management.');
                }
            } else {
                setPrintStatus('error');
                showToast('error', data.message ?? 'Printer tidak tersedia, coba cek koneksi.');
                setTimeout(() => setPrintStatus('idle'), 3000);
            }
        } catch {
            setPrintStatus('error');
            showToast('error', 'Gagal menghubungi server. Coba lagi.');
            setTimeout(() => setPrintStatus('idle'), 3000);
        }
    };

    const handleDelete = () => {
        router.delete(`/transactions/${transaction.id}`, {
            preserveScroll: false,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head
                title={`Detail Transaksi - ${transaction.transaction_number}`}
            />

            {/* Print Toast */}
            {printToast && (
                <div className="fixed right-4 top-4 z-50">
                    <div
                        className={`flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium shadow-lg ${
                            printToast.type === 'success'
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-destructive text-white'
                        }`}
                    >
                        {printToast.type === 'success' ? (
                            <CheckCircle2 className="h-4 w-4 shrink-0" />
                        ) : (
                            <XCircle className="h-4 w-4 shrink-0" />
                        )}
                        {printToast.message}
                    </div>
                </div>
            )}

            {/* Printer Selection Modal */}
            {showPrinterModal && (
                <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/50">
                    <div className="mx-4 w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-xl">
                        <h3 className="mb-1 text-lg font-semibold text-card-foreground">Pilih Printer</h3>
                        <p className="mb-4 text-sm text-muted-foreground">
                            Tidak ada printer default aktif. Pilih printer dari daftar berikut:
                        </p>
                        {availablePrinters.length === 0 ? (
                            <div className="flex flex-col items-center gap-3 py-6 text-center">
                                <WifiOff className="h-10 w-10 text-muted-foreground/40" />
                                <p className="text-sm text-muted-foreground">Tidak ada printer aktif yang tersedia.</p>
                                <Link
                                    href="/settings/print-management"
                                    className="text-sm font-medium text-primary hover:underline"
                                >
                                    Tambah printer di Print Management →
                                </Link>
                            </div>
                        ) : (
                            <div className="space-y-2">
                                {availablePrinters.map((p) => (
                                    <button
                                        key={p.id}
                                        onClick={() => sendToPrinter(p.id)}
                                        className="flex w-full items-center gap-3 rounded-lg border border-border p-3 text-left hover:border-primary/50 hover:bg-primary/5"
                                    >
                                        <Printer className="h-5 w-5 shrink-0 text-muted-foreground" />
                                        <div>
                                            <p className="text-sm font-medium text-foreground">{p.name}</p>
                                            <p className="text-xs text-muted-foreground">
                                                {p.ip_address} · {p.protocol.toUpperCase()} · {p.paper_size}
                                            </p>
                                        </div>
                                    </button>
                                ))}
                            </div>
                        )}
                        <div className="mt-4 flex justify-end">
                            <Button
                                variant="outline"
                                onClick={() => setShowPrinterModal(false)}
                            >
                                Batal
                            </Button>
                        </div>
                    </div>
                </div>
            )}

            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Overview Cards - Summary */}
                <div className="grid gap-4 md:grid-cols-2">
                    <OverviewCard
                        title="Total Uang"
                        value={formatCurrency(moneyTotal)}
                        subtitle="Total Nilai Uang"
                        icon={DollarSign}
                        gradient="from-emerald-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900"
                        iconBg="bg-gradient-to-br from-emerald-500 to-emerald-600"
                    />
                    <OverviewCard
                        title="Total Beras"
                        value={`${riceTotal} kg`}
                        subtitle="Total Kuantitas Beras"
                        icon={Package}
                        gradient="from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900"
                        iconBg="bg-gradient-to-br from-blue-500 to-blue-600"
                    />
                </div>

                {/* Main Card */}
                <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    {/* Header */}
                    <div className="relative border-b border-gray-200 p-4 sm:p-6 dark:border-gray-700">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div className="mb-2 flex items-center gap-2">
                                    <span className="rounded-md bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                        {transaction.transaction_number}
                                    </span>
                                </div>
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Detail Transaksi
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {new Date(
                                        transaction.date,
                                    ).toLocaleDateString('id-ID', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                    })}
                                </p>
                            </div>
                            <div className="flex gap-2">
                                <Link href={transactions().url}>
                                    <Button variant="outline" className="gap-2">
                                        <ArrowLeft className="h-4 w-4" />
                                        Kembali
                                    </Button>
                                </Link>
                                <Button
                                    onClick={handleGenerateReceipt}
                                    className="gap-2"
                                >
                                    <Printer className="h-4 w-4" />
                                    Cetak Struk
                                </Button>
                                <Button
                                    id="btn-print-printer"
                                    variant="outline"
                                    onClick={() => sendToPrinter()}
                                    disabled={printStatus === 'loading'}
                                    className="gap-2"
                                >
                                    {printStatus === 'loading' ? (
                                        <Loader2 className="h-4 w-4 animate-spin" />
                                    ) : printStatus === 'success' ? (
                                        <CheckCircle2 className="h-4 w-4 text-emerald-500" />
                                    ) : printStatus === 'error' ? (
                                        <XCircle className="h-4 w-4 text-red-500" />
                                    ) : (
                                        <Printer className="h-4 w-4" />
                                    )}
                                    {printStatus === 'loading'
                                        ? 'Mengirim...'
                                        : printStatus === 'success'
                                          ? 'Terkirim!'
                                          : printStatus === 'error'
                                            ? 'Gagal'
                                            : '🖨️ Print ke Printer'}
                                </Button>
                                <Button
                                    variant="destructive"
                                    className="gap-2"
                                    onClick={() => setShowDeleteDialog(true)}
                                >
                                    <Trash2 className="h-4 w-4" />
                                    Hapus Transaksi
                                </Button>
                                <Dialog open={showDeleteDialog} onOpenChange={setShowDeleteDialog}>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Hapus Transaksi?</DialogTitle>
                                            <DialogDescription>
                                                Transaksi <strong>{transaction.transaction_number}</strong> akan dihapus.
                                                Jika berisi penjualan beras, stok akan dikembalikan.
                                                Transaksi dapat dipulihkan dari halaman <em>Sampah</em>.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter>
                                            <Button variant="outline" onClick={() => setShowDeleteDialog(false)}>
                                                Batal
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                onClick={handleDelete}
                                            >
                                                Ya, Hapus
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </div>
                    </div>

                    {/* Content */}
                    <div className="relative space-y-6 p-4 sm:p-6">
                        {/* Information Grid */}
                        <div>
                            <h3 className="mb-4 text-sm font-medium text-gray-900 dark:text-white">
                                Informasi Transaksi
                            </h3>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <InfoItem
                                    icon={User}
                                    label="Muzakki"
                                    value={transaction.customer}
                                />
                                <InfoItem
                                    icon={User}
                                    label="Petugas"
                                    value={transaction.officer_name}
                                />
                                <InfoItem
                                    icon={Calendar}
                                    label="Tanggal"
                                    value={new Date(
                                        transaction.date,
                                    ).toLocaleDateString('id-ID')}
                                />
                                <InfoItem
                                    icon={MapPin}
                                    label="Alamat"
                                    value={transaction.address || '-'}
                                    className="sm:col-span-2"
                                />
                                <InfoItem
                                    icon={Phone}
                                    label="Nomor WA"
                                    value={transaction.wa_number || '-'}
                                />
                            </div>
                        </div>

                        {/* Transaction Items */}
                        <div>
                            <h3 className="mb-4 text-sm font-medium text-gray-900 dark:text-white">
                                Detail Item ({transaction.items.length})
                            </h3>
                            <div className="grid grid-cols-1 gap-3">
                                {transaction.items.map((item, index) => (
                                    <TransactionItemDetailCard
                                        key={index}
                                        item={item}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

// Info Item Component
const InfoItem = ({
    icon: Icon,
    label,
    value,
    className = '',
}: {
    icon: any;
    label: string;
    value: string;
    className?: string;
}) => (
    <div
        className={`flex items-start gap-3 rounded-lg border border-sidebar-border/50 bg-muted/30 p-3 dark:border-sidebar-border ${className}`}
    >
        <div className="mt-0.5 rounded-md bg-gradient-to-br from-emerald-500 to-emerald-600 p-2">
            <Icon className="h-4 w-4 text-white" />
        </div>
        <div className="min-w-0 flex-1">
            <p className="text-xs font-medium text-muted-foreground">{label}</p>
            <p className="mt-1 truncate text-sm font-medium text-foreground">
                {value}
            </p>
        </div>
    </div>
);
