import { OverviewCard } from '@/components/overview-card';
import Table from '@/components/table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { autoOrderedNumber, formatCurrency, formatNumber, formatDate } from '@/lib/utils';
import { transactions } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { DollarSign, Package, ShoppingCart, Download } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: transactions().url,
    },
    {
        title: 'Infaq',
    },
];

const columns = [
    { key: 'id', label: 'No', render: autoOrderedNumber },
    { key: 'transaction_number', label: 'Nomor Transaksi' },
    { key: 'customer', label: 'Muzakki' },
    {
        key: 'type',
        label: 'Beras/Uang',
        render: (value) => value === 'rice' ? 'Beras' : 'Uang'
    },
    {
        key: 'quantity',
        label: 'Quantity',
        render: (value) => value ? `${formatNumber(value)} kg` : '-'
    },
    {
        key: 'amount',
        label: 'Amount',
        render: (value) => value ? formatCurrency(value) : '-'
    },
    { key: 'date', label: 'Tanggal', render: formatDate },
];

export default function Donations() {
    const { data, totalAmount, totalQuantity, numberOfRecords } = usePage().props as any;

    const handleExport = () => {
        window.location.href = '/transactions/details/donations/export';
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Infaq" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-3">
                    <OverviewCard
                        title="Total Uang didapat"
                        value={formatCurrency(totalAmount)}
                        subtitle="Nilai Uang"
                        icon={DollarSign}
                        gradient="from-emerald-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900"
                        iconBg="bg-gradient-to-br from-emerald-500 to-emerald-600"
                    />

                    <OverviewCard
                        title="Total Beras didapat"
                        value={formatNumber(totalQuantity) + " kg"}
                        subtitle="Total Kuantitas Beras"
                        icon={Package}
                        gradient="from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900"
                        iconBg="bg-gradient-to-br from-blue-500 to-blue-600"
                    />

                    <OverviewCard
                        title="Total Records"
                        value={formatNumber(numberOfRecords)}
                        subtitle="Jumlah record"
                        icon={ShoppingCart}
                        gradient="from-blue-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900"
                        iconBg="bg-gradient-to-br from-purple-500 to-purple-600"
                    />
                </div>

                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div className="border-b border-gray-200 p-4 sm:p-6 dark:border-gray-700">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Daftar Infaq
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Daftar semua infaq
                                </p>
                            </div>
                            <Button onClick={handleExport} className="cursor-pointer">
                                <Download className="mr-2 h-4 w-4" />
                                Export Excel
                            </Button>
                        </div>
                    </div>

                    <Table
                        columns={columns}
                        rowsPerPage={10}
                        data={data}
                        onRowClick={null}
                    />
                </div>
            </div>
        </AppLayout>
    );
}

