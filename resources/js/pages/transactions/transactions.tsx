import { OverviewCard } from '@/components/overview-card';
import Table from '@/components/table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { autoOrderedNumber, formatCurrency, formatNumber } from '@/lib/utils';
import { transactionCreate, transactions } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { DollarSign, Package, ShoppingCart } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: transactions().url,
    },
];

const columns = [
    { key: 'id', label: 'No', render: autoOrderedNumber }, // auto-ordered number
    { key: 'transaction_number', label: 'Nomor Transaksi' },
    { key: 'date', label: 'Tanggal' },
    { key: 'customer', label: 'Nama' },
    { key: 'officer_name', label: 'Petugas' },
    {
        key: 'total_transaction_amount',
        label: 'Total Amount',
        render: formatCurrency,
    },
    { key: 'total_transaction_quantity', label: 'Total Quantity' },
];

export default function Transactions() {
    const { transactions, totalAmount, totalQuantity, numberOfTransactions } =
        usePage().props;

    const onRowClick = (row) => {
        router.visit(`/transactions/${row.id}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Transaksi" />
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
                        value={formatNumber(totalQuantity) + ' kg'}
                        subtitle="Total Kuantitas Beras"
                        icon={Package}
                        gradient="from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900"
                        iconBg="bg-gradient-to-br from-blue-500 to-blue-600"
                    />

                    <OverviewCard
                        title="Total Transactions"
                        value={formatNumber(numberOfTransactions)}
                        subtitle="Jumlah transaksi"
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
                                    Daftar Transaksi
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Daftar semua transaksi
                                </p>
                            </div>
                            <Link href={transactionCreate()} prefetch>
                                <Button className="cursor-pointer">
                                    <ShoppingCart className="mr-2 h-4 w-4" />
                                    Transaksi Baru
                                </Button>
                            </Link>
                        </div>
                    </div>

                    <Table
                        columns={columns}
                        rowsPerPage={10}
                        data={transactions}
                        onRowClick={onRowClick}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
