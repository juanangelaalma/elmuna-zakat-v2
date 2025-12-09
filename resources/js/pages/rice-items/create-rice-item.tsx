import AppLayout from '@/layouts/app-layout';
import { riceItems } from '@/routes';
import { SharedData, type BreadcrumbItem } from '@/types';
import { Head, usePage, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { Package, DollarSign, ArrowLeft, Save } from 'lucide-react';
import { formatNumber, formatCurrency } from '@/lib/utils';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/input-error';
import { Form } from '@inertiajs/react';
import { riceItemStore } from '@/routes'
import { useEffect } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Brand',
        href: riceItems().url,
    },
    {
        title: 'Baru',
        href: '',
    },
];

export default function CreatePurchase() {
    const { defaultValue } = usePage<SharedData>().props

    const { data, setData } = useForm({
        name: '',
        unit: '',
    });

    useEffect(() => {
        setData('unit', defaultValue.unit)
    }, [defaultValue])

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pembelian Baru" />
            <div className="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            Brand Baru
                        </h1>
                        <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Tambahkan data brand baru
                        </p>
                    </div>
                    <Link href={riceItems().url}>
                        <Button variant="outline" className="gap-2">
                            <ArrowLeft className="h-4 w-4" />
                            Kembali
                        </Button>
                    </Link>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Form Card */}
                    <div className="lg:col-span-2">
                        <div className="rounded-xl px-4 border border-sidebar-border/70 bg-white dark:bg-sidebar-accent shadow-sm">
                            <div className="border-b border-gray-200 dark:border-gray-700 p-6 pl-0">
                                <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                                    Informasi Pembelian
                                </h2>
                                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Isi form di bawah untuk menambah pembelian
                                </p>
                            </div>

                            <Form {...riceItemStore.form()} className="p-6 space-y-6 pl-0">


                                {({ processing, errors }) => (
                                    <>
                                        <Label htmlFor="name">
                                            Nama <span className="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="name"
                                            type="text"
                                            name="name"
                                            required
                                            autoFocus
                                            value={data.name}
                                            tabIndex={1}
                                            placeholder="Masukkan nama"
                                            onChange={(e) => setData('name', e.target.value)}
                                        >
                                            <InputError message={errors.quantity} />
                                        </Input>

                                        <Label htmlFor="unit">
                                            Unit <span className="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="unit"
                                            type="text"
                                            name="unit"
                                            required
                                            tabIndex={1}
                                            value={data.unit}
                                            placeholder="Masukkan jumlah dalam kg"
                                            onChange={(e) => setData('unit', e.target.value)}
                                        >
                                            <InputError message={errors.unit} />
                                        </Input>

                                        {/* Action Buttons */}
                                        <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <Link href={riceItems().url}>
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
                                                        Simpan
                                                    </>
                                                )}
                                            </Button>
                                        </div>
                                    </>
                                )}
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
