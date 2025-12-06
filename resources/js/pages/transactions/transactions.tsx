import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import Table from '@/components/table';
import { OverviewCard } from '@/components/overview-card';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { Package, DollarSign, ShoppingCart } from 'lucide-react';
import { formatNumber, formatCurrency, autoOrderedNumber } from '@/lib/utils';
import { router } from '@inertiajs/react'
import { transactions, transactionCreate } from '@/routes';

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
    { key: 'total_transaction_amount', label: 'Total Amount', render: formatCurrency },
    { key: 'total_transaction_quantity', label: 'Total Quantity' },
];

export default function Transactions() {
    const { transactions, totalValue, numberOfTransactions } = usePage().props;

    const onRowClick = (row) => {
        router.visit(`/transactions/${row.id}`);
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Transaksi" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-3">
                    <OverviewCard
                        title="Total Value"
                        value={formatCurrency(totalValue)}
                        subtitle="Nilai total transaksi"
                        icon={DollarSign}
                        gradient="from-emerald-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900"
                        iconBg="bg-gradient-to-br from-emerald-500 to-emerald-600"
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
                    <div className="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
