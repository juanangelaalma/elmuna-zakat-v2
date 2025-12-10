import DatePicker from '@/components/date-picker';
import { OverviewCard } from '@/components/overview-card';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { formatCurrency, formatNumber } from '@/lib/utils';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import {
    Calendar,
    DollarSign,
    ShoppingCart,
    TrendingUp,
    Warehouse,
} from 'lucide-react';
import { useState } from 'react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Legend,
    Line,
    LineChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard() {
    const {
        filters,
        riceStock,
        salesSummary,
        incomeSummary,
        transactionSummary,
        purchaseSummary,
        dailyTrends,
    } = usePage().props as any;

    const [startDate, setStartDate] = useState(filters.start_date);
    const [endDate, setEndDate] = useState(filters.end_date);

    const handleFilterChange = () => {
        router.get(
            dashboard().url,
            { start_date: startDate, end_date: endDate },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-gray-900">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Filter Periode
                            </h2>
                            <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Pilih rentang tanggal untuk melihat data
                            </p>
                        </div>
                        <div className="flex flex-col gap-2 sm:flex-row sm:items-end">
                            <div className="flex flex-col gap-1">
                                <label className="text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Tanggal Mulai
                                </label>
                                <DatePicker className="h-10" date={startDate} setDate={setStartDate} />
                            </div>
                            <div className="flex flex-col gap-1">
                                <label className="text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Tanggal Akhir
                                </label>
                                <DatePicker className="h-10" date={endDate} setDate={setEndDate} />
                            </div>
                            <Button
                                onClick={handleFilterChange}
                                className="cursor-pointer h-10"
                            >
                                <Calendar className="mr-2 h-4 w-4" />
                                Filter
                            </Button>
                        </div>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <OverviewCard
                        title="Stok Beras Saat Ini"
                        value={formatNumber(riceStock.current_stock) + ' kg'}
                        subtitle="Stok beras tersedia"
                        icon={Warehouse}
                        gradient="from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900"
                        iconBg="bg-gradient-to-br from-blue-500 to-blue-600"
                    />

                    <OverviewCard
                        title="Total Pendapatan"
                        value={formatCurrency(incomeSummary.total)}
                        subtitle="Total uang masuk"
                        icon={DollarSign}
                        gradient="from-emerald-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900"
                        iconBg="bg-gradient-to-br from-emerald-500 to-emerald-600"
                    />

                    <OverviewCard
                        title="Beras Terjual"
                        value={
                            formatNumber(salesSummary.total_quantity) + ' kg'
                        }
                        subtitle={`${salesSummary.count} transaksi`}
                        icon={ShoppingCart}
                        gradient="from-orange-50 to-orange-100 dark:from-orange-950 dark:to-orange-900"
                        iconBg="bg-gradient-to-br from-orange-500 to-orange-600"
                    />

                    <OverviewCard
                        title="Total Transaksi"
                        value={formatNumber(
                            transactionSummary.total_transactions,
                        )}
                        subtitle="Jumlah transaksi"
                        icon={TrendingUp}
                        gradient="from-purple-50 to-purple-100 dark:from-purple-950 dark:to-purple-900"
                        iconBg="bg-gradient-to-br from-purple-500 to-purple-600"
                    />
                </div>

                <div className="grid gap-4 lg:grid-cols-2">
                    <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Detail Stok Beras
                        </h3>
                        <div className="space-y-3">
                            <div className="flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Pembelian
                                </span>
                                <span className="font-semibold text-blue-600 dark:text-blue-400">
                                    {formatNumber(riceStock.purchase)} kg
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-orange-50 p-3 dark:bg-orange-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Beras Terjual
                                </span>
                                <span className="font-semibold text-orange-600 dark:text-orange-400">
                                    {formatNumber(riceStock.sales)} kg
                                </span>
                            </div>

                            <div className="flex items-center justify-between p-3 border-t-1">
                                <span className="text-sm font-bold text-gray-700 dark:text-gray-300">
                                    Stok Beras untuk dijual
                                </span>
                                <span className="font-bold">
                                    {formatNumber(riceStock.current_stock)} kg
                                </span>
                            </div>

                            <div className="flex items-center justify-between rounded-lg bg-emerald-50 p-3 mt-6 dark:bg-emerald-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Zakat Beras
                                </span>
                                <span className="font-semibold text-emerald-600 dark:text-emerald-400">
                                    {formatNumber(riceStock.zakat_rice)} kg
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-purple-50 p-3 dark:bg-purple-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Infaq/Donasi Beras
                                </span>
                                <span className="font-semibold text-purple-600 dark:text-purple-400">
                                    {formatNumber(riceStock.donation_rice)} kg
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-pink-50 p-3 dark:bg-pink-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fidyah Beras
                                </span>
                                <span className="font-semibold text-pink-600 dark:text-pink-400">
                                    {formatNumber(riceStock.fidyah_rice)} kg
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg border-t-2 p-3 dark:border-blue-800">
                                <span className="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    Total Beras
                                </span>
                                <span className="text-lg font-bold text-gray-800 dark:text-gray-200">
                                    {formatNumber(riceStock.total_current_stock)} kg
                                </span>
                            </div>
                        </div>
                    </Card>

                    <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Detail Pendapatan
                        </h3>
                        <div className="space-y-3">
                            <div className="flex items-center justify-between rounded-lg bg-emerald-50 p-3 dark:bg-emerald-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Penjualan Beras
                                </span>
                                <span className="font-semibold text-emerald-600 dark:text-emerald-400">
                                    {formatCurrency(incomeSummary.rice_sales)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Zakat Mall
                                </span>
                                <span className="font-semibold text-blue-600 dark:text-blue-400">
                                    {formatCurrency(incomeSummary.zakat_mall)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-purple-50 p-3 dark:bg-purple-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Infaq
                                </span>
                                <span className="font-semibold text-purple-600 dark:text-purple-400">
                                    {formatCurrency(incomeSummary.infaq)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-pink-50 p-3 dark:bg-pink-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fidyah
                                </span>
                                <span className="font-semibold text-pink-600 dark:text-pink-400">
                                    {formatCurrency(incomeSummary.fidyah)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg border-t-2 p-3">
                                <span className="text-sm font-semibold text-gray-800">
                                    Total Pendapatan
                                </span>
                                <span className="text-lg font-bold">
                                    {formatCurrency(incomeSummary.total)}
                                </span>
                            </div>
                        </div>
                    </Card>
                </div>

                <div className="grid gap-4 lg:grid-cols-2">
                    <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Ringkasan Penjualan Beras
                        </h3>
                        <div className="space-y-3">
                            <div className="flex items-center justify-between rounded-lg bg-emerald-50 p-3 dark:bg-emerald-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Total Penjualan
                                </span>
                                <span className="font-semibold text-emerald-600 dark:text-emerald-400">
                                    {formatCurrency(salesSummary.total_amount)}
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-orange-50 p-3 dark:bg-orange-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Kuantitas Terjual
                                </span>
                                <span className="font-semibold text-orange-600 dark:text-orange-400">
                                    {formatNumber(salesSummary.total_quantity)}{' '}
                                    kg
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Jumlah Transaksi
                                </span>
                                <span className="font-semibold text-blue-600 dark:text-blue-400">
                                    {formatNumber(salesSummary.count)}
                                </span>
                            </div>
                        </div>
                    </Card>

                    <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                        <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                            Ringkasan Pembelian Beras
                        </h3>
                        <div className="space-y-3">
                            <div className="flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Total Pembelian
                                </span>
                                <span className="font-semibold text-blue-600 dark:text-blue-400">
                                    {formatCurrency(
                                        purchaseSummary.total_value,
                                    )}
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-purple-50 p-3 dark:bg-purple-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Kuantitas Dibeli
                                </span>
                                <span className="font-semibold text-purple-600 dark:text-purple-400">
                                    {formatNumber(
                                        purchaseSummary.total_quantity,
                                    )}{' '}
                                    kg
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg bg-emerald-50 p-3 dark:bg-emerald-950">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Jumlah Transaksi
                                </span>
                                <span className="font-semibold text-emerald-600 dark:text-emerald-400">
                                    {formatNumber(purchaseSummary.count)}
                                </span>
                            </div>
                        </div>
                    </Card>
                </div>

                <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Tren Pendapatan Harian
                    </h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <AreaChart data={dailyTrends}>
                            <defs>
                                <linearGradient
                                    id="colorIncome"
                                    x1="0"
                                    y1="0"
                                    x2="0"
                                    y2="1"
                                >
                                    <stop
                                        offset="5%"
                                        stopColor="#10b981"
                                        stopOpacity={0.8}
                                    />
                                    <stop
                                        offset="95%"
                                        stopColor="#10b981"
                                        stopOpacity={0}
                                    />
                                </linearGradient>
                            </defs>
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis
                                dataKey="date"
                                tick={{ fill: '#6b7280' }}
                                tickFormatter={(value) => {
                                    const date = new Date(value);
                                    return `${date.getDate()}/${date.getMonth() + 1}`;
                                }}
                            />
                            <YAxis tick={{ fill: '#6b7280' }} />
                            <Tooltip
                                contentStyle={{
                                    backgroundColor:
                                        'rgba(255, 255, 255, 0.95)',
                                    border: '1px solid #e5e7eb',
                                    borderRadius: '8px',
                                }}
                                formatter={(value: any) =>
                                    formatCurrency(value)
                                }
                            />
                            <Area
                                type="monotone"
                                dataKey="total_income"
                                stroke="#10b981"
                                fillOpacity={1}
                                fill="url(#colorIncome)"
                                name="Total Pendapatan"
                            />
                        </AreaChart>
                    </ResponsiveContainer>
                </Card>

                <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Perbandingan Pendapatan Per Sumber
                    </h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <BarChart data={dailyTrends}>
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis
                                dataKey="date"
                                tick={{ fill: '#6b7280' }}
                                tickFormatter={(value) => {
                                    const date = new Date(value);
                                    return `${date.getDate()}/${date.getMonth() + 1}`;
                                }}
                            />
                            <YAxis tick={{ fill: '#6b7280' }} />
                            <Tooltip
                                contentStyle={{
                                    backgroundColor:
                                        'rgba(255, 255, 255, 0.95)',
                                    border: '1px solid #e5e7eb',
                                    borderRadius: '8px',
                                }}
                                formatter={(value: any) =>
                                    formatCurrency(value)
                                }
                            />
                            <Legend />
                            <Bar
                                dataKey="rice_sales_amount"
                                fill="#10b981"
                                name="Penjualan Beras"
                            />
                            <Bar
                                dataKey="zakat_mall"
                                fill="#3b82f6"
                                name="Zakat Mall"
                            />
                            <Bar dataKey="infaq" fill="#a855f7" name="Infaq" />
                            <Bar
                                dataKey="fidyah"
                                fill="#ec4899"
                                name="Fidyah"
                            />
                        </BarChart>
                    </ResponsiveContainer>
                </Card>

                <Card className="border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Tren Penjualan Beras (Kuantitas)
                    </h3>
                    <ResponsiveContainer width="100%" height={300}>
                        <LineChart data={dailyTrends}>
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis
                                dataKey="date"
                                tick={{ fill: '#6b7280' }}
                                tickFormatter={(value) => {
                                    const date = new Date(value);
                                    return `${date.getDate()}/${date.getMonth() + 1}`;
                                }}
                            />
                            <YAxis tick={{ fill: '#6b7280' }} />
                            <Tooltip
                                contentStyle={{
                                    backgroundColor:
                                        'rgba(255, 255, 255, 0.95)',
                                    border: '1px solid #e5e7eb',
                                    borderRadius: '8px',
                                }}
                                formatter={(value: any) =>
                                    `${formatNumber(value)} kg`
                                }
                            />
                            <Legend />
                            <Line
                                type="monotone"
                                dataKey="rice_sales_quantity"
                                stroke="#f97316"
                                strokeWidth={2}
                                name="Kuantitas Terjual"
                            />
                        </LineChart>
                    </ResponsiveContainer>
                </Card>

            </div>
        </AppLayout>
    );
}
