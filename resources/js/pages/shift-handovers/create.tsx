import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import AppLayout from '@/layouts/app-layout';
import { formatCurrency, formatNumber } from '@/lib/utils';
import { BreadcrumbItem, SharedData } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useEffect } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Serah Terima',
        href: '/shift-handovers',
    },
    {
        title: 'Buat Serah Terima',
        href: '/shift-handovers/create',
    },
];

export default function ShiftHandoverCreate() {
    const { auth, unsettledTransactions } = usePage<SharedData & { unsettledTransactions: any[] }>().props;

    const { data, setData, post, processing, errors } = useForm({
        handing_over_officer_name: auth.user.name,
        receiving_officer_name: '',
        shift_name: 'Shift 1',
        transaction_ids: unsettledTransactions.map((t) => t.id),
    });

    const handleCheckboxChange = (id: number, checked: boolean) => {
        if (checked) {
            setData('transaction_ids', [...data.transaction_ids, id]);
        } else {
            setData(
                'transaction_ids',
                data.transaction_ids.filter((tId) => tId !== id)
            );
        }
    };

    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            setData('transaction_ids', unsettledTransactions.map((t) => t.id));
        } else {
            setData('transaction_ids', []);
        }
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/shift-handovers');
    };

    // Calculate preview totals based on selected transaction IDs
    const selectedTransactions = unsettledTransactions.filter((t) =>
        data.transaction_ids.includes(t.id)
    );

    const totalPreviewMoney = selectedTransactions.reduce((acc, curr) => acc + parseFloat(curr.total_transaction_amount || 0), 0);
    const totalPreviewRice = selectedTransactions.reduce((acc, curr) => acc + parseFloat(curr.total_transaction_quantity || 0), 0);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Buat Serah Terima" />
            <div className="flex flex-1 flex-col overflow-x-auto rounded-xl p-4">
                <Card className="mx-auto w-full">
                    <CardHeader className="w-full">
                        <CardTitle>Buat Serah Terima Baru</CardTitle>
                        <CardDescription>
                            Tutup shift saat ini dan serahkan transaksi ke petugas shift berikutnya.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="handing_over_officer_name">
                                        Petugas Penyerah
                                        <span className="ml-1 text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="handing_over_officer_name"
                                        value={data.handing_over_officer_name}
                                        onChange={(e) =>
                                            setData('handing_over_officer_name', e.target.value)
                                        }
                                        placeholder="Nama Petugas Penyerah"
                                        required
                                    />
                                    {errors.handing_over_officer_name && (
                                        <p className="text-sm text-red-500">
                                            {errors.handing_over_officer_name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="receiving_officer_name">
                                        Petugas Penerima
                                        <span className="ml-1 text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="receiving_officer_name"
                                        value={data.receiving_officer_name}
                                        onChange={(e) =>
                                            setData('receiving_officer_name', e.target.value)
                                        }
                                        placeholder="Nama Petugas Penerima"
                                        required
                                    />
                                    {errors.receiving_officer_name && (
                                        <p className="text-sm text-red-500">
                                            {errors.receiving_officer_name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="shift_name">
                                        Shift
                                        <span className="ml-1 text-red-500">*</span>
                                    </Label>
                                    <Select
                                        value={data.shift_name}
                                        onValueChange={(value) => setData('shift_name', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Pilih Shift" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Shift 1">Shift 1</SelectItem>
                                            <SelectItem value="Shift 2">Shift 2</SelectItem>
                                            <SelectItem value="Shift 3">Shift 3</SelectItem>
                                            <SelectItem value="Shift Tambahan">Shift Tambahan</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.shift_name && (
                                        <p className="text-sm text-red-500">
                                            {errors.shift_name}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="space-y-4">
                                <div>
                                    <h3 className="text-lg font-medium">Transaksi Belum Diserahterimakan</h3>
                                    <p className="text-sm text-muted-foreground">Pilih transaksi yang akan dimasukkan ke laporan serah terima ini.</p>
                                    {errors.transaction_ids && (
                                        <p className="text-sm text-red-500 mt-1">
                                            {errors.transaction_ids}
                                        </p>
                                    )}
                                </div>

                                <div className="rounded-md border">
                                    <div className="flex items-center p-3 border-b bg-muted/50">
                                        <Checkbox
                                            id="select-all"
                                            checked={data.transaction_ids.length === unsettledTransactions.length && unsettledTransactions.length > 0}
                                            onCheckedChange={handleSelectAll}
                                            disabled={unsettledTransactions.length === 0}
                                        />
                                        <Label htmlFor="select-all" className="ml-2 font-medium cursor-pointer">
                                            Pilih Semua ({unsettledTransactions.length})
                                        </Label>
                                    </div>
                                    <div className="max-h-[300px] overflow-y-auto">
                                        {unsettledTransactions.length > 0 ? (
                                            <table className="w-full text-sm text-left">
                                                <thead className="text-xs uppercase bg-muted/20 sticky top-0">
                                                    <tr>
                                                        <th className="px-4 py-3 w-[50px]"></th>
                                                        <th className="px-4 py-3">No. Transaksi</th>
                                                        <th className="px-4 py-3">Muzakki</th>
                                                        <th className="px-4 py-3">Total Uang</th>
                                                        <th className="px-4 py-3">Total Beras</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {unsettledTransactions.map((trx) => (
                                                        <tr key={trx.id} className="border-b last:border-0 hover:bg-muted/10">
                                                            <td className="px-4 py-3">
                                                                <Checkbox
                                                                    id={`trx-${trx.id}`}
                                                                    checked={data.transaction_ids.includes(trx.id)}
                                                                    onCheckedChange={(checked) => handleCheckboxChange(trx.id, checked as boolean)}
                                                                />
                                                            </td>
                                                            <td className="px-4 py-3">
                                                                <label htmlFor={`trx-${trx.id}`} className="cursor-pointer font-medium text-blue-600 hover:underline">
                                                                    {trx.transaction_number}
                                                                </label>
                                                            </td>
                                                            <td className="px-4 py-3">{trx.customer}</td>
                                                            <td className="px-4 py-3">{formatCurrency(parseFloat(trx.total_transaction_amount || 0))}</td>
                                                            <td className="px-4 py-3">{formatNumber(parseFloat(trx.total_transaction_quantity || 0))} kg</td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        ) : (
                                            <div className="p-8 text-center text-muted-foreground">
                                                Tidak ada transaksi yang belum diserahterimakan.
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className="mt-6 ml-auto w-full rounded-xl border bg-muted/40 p-4 md:w-1/2 lg:w-1/3">
                                <div className="mb-3 flex items-center justify-between">
                                    <h3 className="text-sm font-semibold text-muted-foreground">
                                        Estimasi Total Serah Terima
                                    </h3>
                                </div>

                                <div className="space-y-2">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-muted-foreground">Total Uang</span>
                                        <span className="text-base font-semibold text-emerald-600">
                                            {formatCurrency(totalPreviewMoney)}
                                        </span>
                                    </div>

                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-muted-foreground">Total Beras</span>
                                        <span className="text-base font-semibold text-blue-600">
                                            {formatNumber(totalPreviewRice)} kg
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div className="flex justify-end gap-4">
                                <Link href="/shift-handovers">
                                    <Button variant="outline" type="button">
                                        Batal
                                    </Button>
                                </Link>
                                <Button
                                    type="submit"
                                    disabled={processing || data.transaction_ids.length === 0}
                                >
                                    Buat Serah Terima
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
