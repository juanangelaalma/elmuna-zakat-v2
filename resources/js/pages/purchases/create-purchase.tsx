import AppLayout from '@/layouts/app-layout';
import { purchases } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { Package, DollarSign, ArrowLeft, Save } from 'lucide-react';
import { formatNumber, formatCurrency } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from '@/components/ui/select';
import InputError from '@/components/input-error';
import { Form } from '@inertiajs/react';
import { purchaseStore } from '@/routes'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pembelian',
        href: purchases().url,
    },
    {
        title: 'Baru',
    },
];

export default function CreatePurchase() {
    const { riceItems } = usePage().props; // Asumsikan rice_items dikirim dari controller

    const { data, setData } = useForm({
        rice_item_id: '',
        quantity: '',
        price_per_kg: '',
    });

    // Calculate total value
    const totalValue = data.quantity && data.price_per_kg
        ? parseFloat(data.quantity) * parseFloat(data.price_per_kg)
        : 0;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pembelian Baru" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            Pembelian Baru
                        </h1>
                        <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Tambahkan data pembelian beras baru
                        </p>
                    </div>
                    <Link href={purchases().url}>
                        <Button variant="outline" className="gap-2">
                            <ArrowLeft className="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Form Card */}
                    <div className="lg:col-span-2">
                        <div className="rounded-xl border border-sidebar-border/70 bg-white dark:bg-sidebar-accent shadow-sm">
                            <div className="border-b border-gray-200 dark:border-gray-700 p-6 pl-0">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Informasi Pembelian
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Isi form di bawah untuk menambah pembelian
                                </p>
                            </div>

                            <Form {...purchaseStore.form()} className="p-6 space-y-6 pl-0">


                                {({ processing, errors }) => (
                                    <>
                                        <Label htmlFor="rice_item_id">
                                            Beras <span className="text-red-500">*</span>
                                        </Label>
                                        <Select
                                            id="rice_item_id"
                                            name="rice_item_id"
                                            value={data.rice_item_id}
                                            onValueChange={(value) => setData('rice_item_id', value)}
                                        >
                                            <SelectTrigger>
                                                <div className="flex items-center gap-2">
                                                    <Package className="h-4 w-4 text-gray-400" />
                                                    <SelectValue placeholder="Pilih jenis beras" />
                                                </div>
                                            </SelectTrigger>
                                            <SelectContent>
                                                {riceItems?.map((item) => (
                                                    <SelectItem key={item.id} value={item.id.toString()}>
                                                        {item.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>


                                        {/* Quantity Input */}
                                        <Label htmlFor="quantity">
                                            Jumlah (kg) <span className="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="quantity"
                                            type="number"
                                            name="quantity"
                                            required
                                            autoFocus
                                            tabIndex={1}
                                            autoComplete="quantity"
                                            placeholder="Masukkan jumlah dalam kg"
                                            onChange={(e) => setData('quantity', e.target.value)}
                                        >
                                            <InputError message={errors.quantity} />
                                        </Input>

                                        <Label htmlFor="price">
                                            Harga per kg <span className="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="price_per_kg"
                                            type="number"
                                            name="price_per_kg"
                                            required
                                            autoFocus
                                            tabIndex={1}
                                            placeholder="Masukkan jumlah dalam kg"
                                            onChange={(e) => setData('price_per_kg', e.target.value)}
                                        >
                                            <span className="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-500">
                                                IDR
                                            </span>
                                        <InputError message={errors.price_per_kg} />
                                        </Input>

                                        {/* Action Buttons */}
                                        <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <Link href={purchases().url}>
                                                <Button type="button" variant="outline" className="cursor-pointer">
                                                    Batal
                                                </Button>
                                            </Link>
                                            <Button
                                                type="submit"
                                                disabled={processing}
                                                className="gap-2 cursor-pointer"
                                            >
                                                {processing ? (
                                                    <>
                                                        <div className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                                        Menyimpan...
                                                    </>
                                                ) : (
                                                    <>
                                                        <Save className="h-4 w-4" />
                                                        Simpan Pembelian
                                                    </>
                                                )}
                                            </Button>
                                        </div>
                                    </>
                                )}
                            </Form>
                        </div>
                    </div>

                    {/* Summary Card */}
                    <div className="lg:col-span-1">
                        <div className="rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-950 dark:to-indigo-900 shadow-sm p-6 sticky top-4">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Ringkasan
                            </h3>

                            <div className="space-y-4">
                                {/* Quantity Summary */}
                                <div className="flex items-center justify-between p-3 bg-white/50 dark:bg-white/10 rounded-lg">
                                    <div className="flex items-center gap-3">
                                        <div className="h-10 w-10 rounded-lg bg-blue-500 flex items-center justify-center">
                                            <Package className="h-5 w-5 text-white" />
                                        </div>
                                        <div>
                                            <p className="text-xs text-gray-600 dark:text-gray-400">Jumlah</p>
                                            <p className="text-lg font-bold text-gray-900 dark:text-white">
                                                {data.quantity ? `${formatNumber(parseFloat(data.quantity))} kg` : '-'}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Price Summary */}
                                <div className="flex items-center justify-between p-3 bg-white/50 dark:bg-white/10 rounded-lg">
                                    <div className="flex items-center gap-3">
                                        <div className="h-10 w-10 rounded-lg bg-emerald-500 flex items-center justify-center">
                                            <DollarSign className="h-5 w-5 text-white" />
                                        </div>
                                        <div>
                                            <p className="text-xs text-gray-600 dark:text-gray-400">Harga/kg</p>
                                            <p className="text-lg font-bold text-gray-900 dark:text-white">
                                                {data.price_per_kg ? formatCurrency(parseFloat(data.price_per_kg)) : '-'}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Divider */}
                                <div className="border-t-2 border-indigo-200 dark:border-indigo-800"></div>

                                {/* Total Value */}
                                <div className="p-4 bg-white/70 dark:bg-white/20 rounded-lg">
                                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Nilai</p>
                                    <p className="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                        {totalValue > 0 ? formatCurrency(totalValue) : 'Rp 0'}
                                    </p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        {data.quantity && data.price_per_kg
                                            ? `${formatNumber(parseFloat(data.quantity))} kg × ${formatCurrency(parseFloat(data.price_per_kg))}`
                                            : 'Isi form untuk melihat total'
                                        }
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
