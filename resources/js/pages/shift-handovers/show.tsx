import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatCurrency, formatNumber } from '@/lib/utils';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';
import { Printer, ArrowLeft } from 'lucide-react';

export default function ShiftHandoverShow() {
    const { handover } = usePage<SharedData & { handover: any }>().props;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Serah Terima',
            href: '/shift-handovers',
        },
        {
            title: `Detail ${handover.shift_name}`,
            href: `/shift-handovers/${handover.id}`,
        },
    ];

    const totalMoney =
        parseFloat(handover.total_rice_sale_amount || 0) +
        parseFloat(handover.total_wealth_amount || 0) +
        parseFloat(handover.total_fidyah_amount || 0) +
        parseFloat(handover.total_donation_amount || 0);

    const totalRice =
        parseFloat(handover.total_rice_quantity || 0) +
        parseFloat(handover.total_fidyah_quantity || 0) +
        parseFloat(handover.total_donation_quantity || 0);

    const handlePrint = () => {
        window.open(`/shift-handovers/${handover.id}/pdf`, '_blank');
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Serah Terima - ${handover.shift_name}`} />
            <div className="flex flex-1 flex-col overflow-x-auto rounded-xl p-4 gap-6">

                <div className="flex justify-between items-center">
                    <Link href="/shift-handovers">
                        <Button variant="outline" className="gap-2">
                            <ArrowLeft className="w-4 h-4" /> Kembali
                        </Button>
                    </Link>
                    <Button onClick={handlePrint} className="gap-2 bg-emerald-600 hover:bg-emerald-700">
                        <Printer className="w-4 h-4" /> Cetak Tanda Terima
                    </Button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Shift</CardTitle>
                            <CardDescription>Detail petugas dan waktu pelaksanaan serah terima.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">Shift</p>
                                    <p className="font-semibold">{handover.shift_name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Tanggal</p>
                                    <p className="font-semibold">
                                        {format(new Date(handover.handover_date), 'dd MMMM yyyy', { locale: id })}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Petugas Penyerah</p>
                                    <p className="font-semibold">{handover.handing_over_officer_name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Petugas Penerima</p>
                                    <p className="font-semibold">{handover.receiving_officer_name}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Ringkasan Penerimaan</CardTitle>
                            <CardDescription>Total uang dan beras yang diserahterimakan.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Zakat Fitrah Beras</span>
                                    <span>{formatNumber(parseFloat(handover.total_rice_quantity || 0))} kg</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Penjualan Beras</span>
                                    <span>{formatCurrency(parseFloat(handover.total_rice_sale_amount || 0))}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Zakat Maal</span>
                                    <span>{formatCurrency(parseFloat(handover.total_wealth_amount || 0))}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Fidyah Uang</span>
                                    <span>{formatCurrency(parseFloat(handover.total_fidyah_amount || 0))}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Fidyah Beras</span>
                                    <span>{formatNumber(parseFloat(handover.total_fidyah_quantity || 0))} kg</span>
                                </div>
                                <div className="flex justify-between border-b pb-2">
                                    <span className="text-muted-foreground">Shodaqoh Uang</span>
                                    <span>{formatCurrency(parseFloat(handover.total_donation_amount || 0))}</span>
                                </div>
                                <div className="flex justify-between font-semibold text-emerald-600 pt-2 border-t mt-2">
                                    <span>TOTAL UANG</span>
                                    <span>{formatCurrency(totalMoney)}</span>
                                </div>
                                <div className="flex justify-between font-semibold text-blue-600">
                                    <span>TOTAL BERAS</span>
                                    <span>{formatNumber(totalRice)} kg</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Daftar Transaksi</CardTitle>
                        <CardDescription>
                            Daftar semua transaksi yang disertakan dalam serah terima shift ini.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="relative overflow-x-auto rounded-md border">
                            <table className="w-full text-sm text-left">
                                <thead className="text-xs uppercase bg-muted/50">
                                    <tr>
                                        <th className="px-5 py-3">No. Transaksi</th>
                                        <th className="px-5 py-3">Muzakki</th>
                                        <th className="px-5 py-3">Tanggal Waktu</th>
                                        <th className="px-5 py-3 text-right">Total Uang</th>
                                        <th className="px-5 py-3 text-right">Total Beras</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {handover.transactions?.map((trx: any) => (
                                        <tr key={trx.id} className="border-b last:border-0 hover:bg-muted/10">
                                            <td className="px-5 py-4 font-medium">{trx.transaction_number}</td>
                                            <td className="px-5 py-4">{trx.customer}</td>
                                            <td className="px-5 py-4">
                                                {format(new Date(trx.created_at || trx.date), 'dd/MM/yyyy HH:mm')}
                                            </td>
                                            <td className="px-5 py-4 text-right">
                                                {formatCurrency(parseFloat(trx.total_transaction_amount || 0))}
                                            </td>
                                            <td className="px-5 py-4 text-right">
                                                {formatNumber(parseFloat(trx.total_transaction_quantity || 0))} kg
                                            </td>
                                        </tr>
                                    ))}
                                    {(!handover.transactions || handover.transactions.length === 0) && (
                                        <tr>
                                            <td colSpan={5} className="px-5 py-8 text-center text-muted-foreground">
                                                Tidak ada data transaksi.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

            </div>
        </AppLayout>
    );
}
