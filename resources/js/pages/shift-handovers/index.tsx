import { OverviewCard } from '@/components/overview-card';
import Table from '@/components/table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { autoOrderedNumber, formatCurrency, formatNumber } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { FileDown, CalendarClock } from 'lucide-react';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Serah Terima',
        href: '/shift-handovers',
    },
];

const columns = [
    { key: 'id', label: 'No', render: autoOrderedNumber },
    { key: 'shift_name', label: 'Shift' },
    {
        key: 'handover_date',
        label: 'Tanggal',
        render: (val: string) => format(new Date(val), 'dd MMMM yyyy', { locale: id })
    },
    { key: 'handing_over_officer_name', label: 'Petugas Serah' },
    { key: 'receiving_officer_name', label: 'Petugas Terima' },
    {
        key: 'total_money',
        label: 'Total Uang',
        render: (_: any, row: any) => {
            const sum = parseFloat(row.total_rice_sale_amount || 0) +
                parseFloat(row.total_wealth_amount || 0) +
                parseFloat(row.total_fidyah_amount || 0) +
                parseFloat(row.total_donation_amount || 0);
            return formatCurrency(sum);
        }
    },
    {
        key: 'total_rice',
        label: 'Total Beras (Kg)',
        render: (_: any, row: any) => {
            const sum = parseFloat(row.total_rice_quantity || 0) +
                parseFloat(row.total_fidyah_quantity || 0) +
                parseFloat(row.total_donation_quantity || 0);
            return formatNumber(sum);
        }
    },
];

export default function ShiftHandoversIndex() {
    const { handovers } = usePage().props as any;

    const totalAllHandovers = handovers.length;

    const onRowClick = (row: any) => {
        router.visit(`/shift-handovers/${row.id}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Riwayat Serah Terima" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-3">
                    <OverviewCard
                        title="Total Serah Terima"
                        value={formatNumber(totalAllHandovers)}
                        subtitle="Jumlah riwayat"
                        icon={CalendarClock}
                        gradient="from-blue-50 to-emerald-100 dark:from-emerald-950 dark:to-emerald-900"
                        iconBg="bg-gradient-to-br from-purple-500 to-purple-600"
                    />
                </div>

                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div className="border-b border-gray-200 p-4 sm:p-6 dark:border-gray-700">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Riwayat Serah Terima
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Daftar riwayat semua penutupan shift.
                                </p>
                            </div>
                            <Link href="/shift-handovers/create" prefetch>
                                <Button className="cursor-pointer">
                                    <FileDown className="mr-2 h-4 w-4" />
                                    Buat Serah Terima
                                </Button>
                            </Link>
                        </div>
                    </div>

                    <Table
                        columns={columns}
                        rowsPerPage={10}
                        data={handovers}
                        onRowClick={onRowClick}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
