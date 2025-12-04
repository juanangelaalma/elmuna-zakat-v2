import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, RiceSalesItem, SharedData } from '@/types';
import { Head, useForm, Link, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { FormEventHandler, useState } from 'react';
import { transactions } from '@/routes';
import DatePicker from '@/components/date-picker';
import TransactionItemDialog from '@/pages/transactions/transaction-item-dialog';
import { TransactionItem } from '@/types';
import TransactionItemCard from './forms/transaction-item-card';
import { RICE_SALES_ID } from '@/lib/constant';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: transactions().url,
    },
    {
        title: 'Transaksi Baru',
        href: '/transactions/create',
    },
];

export default function TransactionCreate() {
    const { auth } = usePage<SharedData>().props;

    const { data, setData, post, processing, errors } = useForm({
        date: new Date().toISOString().split('T')[0],
        customer: '',
        address: '',
        wa_number: '',
        officer_name: auth.user.name,
        items: [] as TransactionItem[],
    });

    const setItems = (items: TransactionItem[]) => {
        setData('items', items);
    }

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/transactions/store');
    };

    const moneyTotal = data.items.reduce((total, item) => {
        if ('amount' in item.detail) {
            return total + (item.detail.amount ?? 0);
        }
        return total;
    }, 0);

    const riceTotal = data.items.reduce((total, item) => {
        if (item.item_type !== RICE_SALES_ID && 'quantity' in item.detail) {
            return total + (item.detail.quantity ?? 0);
        }
        return total;
    }, 0);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Transaksi Baru" />
            <div className="flex flex-1 flex-col overflow-x-auto rounded-xl">
                <Card className="mx-auto w-full">
                    <CardHeader className='w-full'>
                        <CardTitle>Transaksi Baru</CardTitle>
                        <CardDescription>
                            Buat transaksi baru dengan mengisi formulir di bawah ini.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="transaction_number">No. Transaksi</Label>
                                    <Input
                                        id="transaction_number"
                                        value="Auto Generated"
                                        disabled
                                        className="bg-gray-100 text-gray-500"
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="date">
                                        Tanggal
                                        <span className="text-red-500 ml-1">*</span>
                                    </Label>
                                    <DatePicker data={data} setData={setData} />
                                    {errors.date && <p className="text-sm text-red-500">{errors.date}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="customer">
                                        Customer
                                        <span className="text-red-500 ml-1">*</span>
                                    </Label>
                                    <Input
                                        id="customer"
                                        value={data.customer}
                                        onChange={(e) => setData('customer', e.target.value)}
                                        placeholder="Nama Customer"
                                        required
                                    />
                                    {errors.customer && <p className="text-sm text-red-500">{errors.customer}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="address">Address</Label>
                                    <Input
                                        id="address"
                                        value={data.address}
                                        onChange={(e) => setData('address', e.target.value)}
                                        placeholder="Alamat Lengkap"
                                        required
                                    />
                                    {errors.address && <p className="text-sm text-red-500">{errors.address}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="wa_number">Wa Number</Label>
                                    <Input
                                        id="wa_number"
                                        value={data.wa_number}
                                        onChange={(e) => setData('wa_number', e.target.value)}
                                        placeholder="Nomor WhatsApp (Contoh: 08123456789)"
                                        required
                                    />
                                    {errors.wa_number && <p className="text-sm text-red-500">{errors.wa_number}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="officer_name">Officer Name</Label>
                                    <Input
                                        id="officer_name"
                                        value={data.officer_name}
                                        onChange={(e) => setData('officer_name', e.target.value)}
                                        placeholder="Nama Petugas"
                                        required
                                    />
                                    {errors.officer_name && <p className="text-sm text-red-500">{errors.officer_name}</p>}
                                </div>
                            </div>
                            <div className="grid grid-cols-1 gap-4">
                                {data.items.map((item, index) => (
                                    <TransactionItemCard
                                        key={index}
                                        item={item}
                                        handleDelete={() => {
                                            const newItems = data.items.filter((i) => i !== item);
                                            setItems(newItems);
                                        }}
                                    />
                                ))}
                            </div>

                            <div className="grid grid-cols-1 gap-4">
                                {errors.items && <p className="text-sm text-red-500 mx-auto">{errors.items}</p>}
                                <TransactionItemDialog items={data.items} setItems={setItems} />
                            </div>

                            <div className="mt-6 rounded-xl border bg-muted/40 p-4 w-full md:w-1/2 lg:w-1/3 mr-0 ml-auto">
                                <div className="mb-3 flex items-center justify-between">
                                    <h3 className="text-sm font-semibold text-muted-foreground">
                                        Ringkasan Transaksi
                                    </h3>
                                </div>

                                <div className="space-y-2">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-muted-foreground">Total Uang</span>
                                        <span className="text-base font-semibold">
                                            Rp {moneyTotal.toLocaleString('id-ID')}
                                        </span>
                                    </div>

                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-muted-foreground">Total Beras</span>
                                        <span className="text-base font-semibold">
                                            {riceTotal} kg
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div className="flex justify-end gap-4">
                                <Link href={transactions().url}>
                                    <Button variant="outline" type="button">
                                        Batal
                                    </Button>
                                </Link>
                                <Button type="submit" disabled={processing}>
                                    Simpan Transaksi
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}