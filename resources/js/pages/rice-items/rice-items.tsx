import Table from '@/components/table';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { riceItemCreate, riceItems } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';

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
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pembelian" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div className="border-b border-gray-200 p-4 sm:p-6 dark:border-gray-700">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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
