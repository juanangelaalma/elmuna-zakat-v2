import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, ArchiveRestore, Trash2, Calendar, User } from 'lucide-react';

interface TrashedTransaction {
    id: number;
    transaction_number: string;
    date: string;
    customer: string;
    officer_name: string;
    deleted_at: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transaksi', href: '/transactions/index' },
    { title: 'Transaksi Dihapus', href: '#' },
];

export default function Trash() {
    const { trashedTransactions } = usePage<{ trashedTransactions: TrashedTransaction[] }>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Transaksi Dihapus" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="rounded-lg bg-red-100 p-2 dark:bg-red-900">
                            <Trash2 className="h-5 w-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <h1 className="text-lg font-semibold text-gray-900 dark:text-white">
                                Transaksi Dihapus
                            </h1>
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                {trashedTransactions.length} transaksi dihapus
                            </p>
                        </div>
                    </div>
                    <Link href="/transactions/index">
                        <Button variant="outline" className="gap-2">
                            <ArrowLeft className="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                </div>

                {/* Table Card */}
                <div className="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    {trashedTransactions.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <Trash2 className="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600" />
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Tidak ada transaksi yang dihapus
                            </p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                                    <tr>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">No. Nota</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Muzakki</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Tanggal</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Petugas</th>
                                        <th className="px-4 py-3 text-left font-medium text-muted-foreground">Dihapus Pada</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-sidebar-border/50 dark:divide-sidebar-border">
                                    {trashedTransactions.map((trx) => (
                                        <tr
                                            key={trx.id}
                                            className="bg-background transition-colors hover:bg-muted/30"
                                        >
                                            <td className="px-4 py-3">
                                                <span className="rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-700 dark:bg-red-900 dark:text-red-300">
                                                    {trx.transaction_number}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-2">
                                                    <User className="h-3.5 w-3.5 text-muted-foreground" />
                                                    <span className="font-medium text-foreground">{trx.customer}</span>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center gap-2 text-muted-foreground">
                                                    <Calendar className="h-3.5 w-3.5" />
                                                    {new Date(trx.date).toLocaleDateString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'long',
                                                        year: 'numeric',
                                                    })}
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {trx.officer_name}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {new Date(trx.deleted_at).toLocaleDateString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
