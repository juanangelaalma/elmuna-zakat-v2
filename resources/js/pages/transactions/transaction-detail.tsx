import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { OverviewCard } from '@/components/overview-card';
import AppLayout from '@/layouts/app-layout';
import { RICE_SALES_ID } from '@/lib/constant';
import TransactionItemDetailCard from '@/pages/transactions/forms/transaction-item-detail-card';
import { transactions } from '@/routes';
import { BreadcrumbItem, TransactionItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Printer, Calendar, User, MapPin, Phone, DollarSign, Package } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';

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

export default function TransactionDetail() {
    const { transaction } = usePage<{ transaction: Transaction }>().props;

    console.log(transaction.items)

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

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Detail Transaksi - ${transaction.transaction_number}`} />
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
                                <div className="flex items-center gap-2 mb-2">
                                    <span className="rounded-md bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                        {transaction.transaction_number}
                                    </span>
                                </div>
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Detail Transaksi
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {new Date(transaction.date).toLocaleDateString('id-ID', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
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
                            </div>
                        </div>
                    </div>

                    {/* Content */}
                    <div className="relative p-4 sm:p-6 space-y-6">
                        {/* Information Grid */}
                        <div>
                            <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-4">
                                Informasi Transaksi
                            </h3>
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <InfoItem
                                    icon={User}
                                    label="Customer"
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
                                    value={new Date(transaction.date).toLocaleDateString('id-ID')}
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
                            <h3 className="text-sm font-medium text-gray-900 dark:text-white mb-4">
                                Detail Item ({transaction.items.length})
                            </h3>
                            <div className="grid grid-cols-1 gap-3">
                                {transaction.items.map((item, index) => (
                                    <TransactionItemDetailCard key={index} item={item} />
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
    className = ""
}: {
    icon: any;
    label: string;
    value: string;
    className?: string;
}) => (
    <div className={`flex items-start gap-3 rounded-lg border border-sidebar-border/50 bg-muted/30 p-3 dark:border-sidebar-border ${className}`}>
        <div className="mt-0.5 rounded-md bg-gradient-to-br from-emerald-500 to-emerald-600 p-2">
            <Icon className="h-4 w-4 text-white" />
        </div>
        <div className="flex-1 min-w-0">
            <p className="text-xs font-medium text-muted-foreground">
                {label}
            </p>
            <p className="mt-1 text-sm font-medium text-foreground truncate">
                {value}
            </p>
        </div>
    </div>
);
