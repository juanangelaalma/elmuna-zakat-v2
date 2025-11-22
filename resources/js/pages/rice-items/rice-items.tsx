import AppLayout from '@/layouts/app-layout';
import { riceItems, riceItemCreate } from '@/routes';
import { Plus } from 'lucide-react';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import Table from '@/components/table';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Brand',
        href: riceItems().url,
    },
];

const columns = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Nama' },
    { key: 'unit', label: 'Unit' },
];

export default function RiceItems() {
    const { riceItems } = usePage().props;

    const onRowClick = (row) => {
        router.visit(`/rice-items/${row.id}`);
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pembelian" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div className="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Daftar Brand Beras
                                </h2>
                            </div>
                            <Link href={riceItemCreate()} prefetch>
                                <Button className="cursor-pointer">
                                    <Plus className="mr-2 h-4 w-4" />
                                    Brand Baru
                                </Button>
                            </Link>
                        </div>
                    </div>

                    <Table
                        columns={columns}
                        rowsPerPage={10}
                        data={riceItems}
                        onRowClick={onRowClick}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
