import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RiceSalesItem, SharedData, TransactionItem } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

const RiceSale = ({
    transactionItem,
    setTransactionItem,
}: {
    transactionItem: TransactionItem | null;
    setTransactionItem: (item: TransactionItem) => void;
}) => {
    const { defaultValue } = usePage<SharedData>().props;

    const handleCustomerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem) {
            setTransactionItem({
                ...transactionItem,
                customer: e.target.value,
            });
        }
    };

    const handleQuantityChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem && 'quantity' in transactionItem.detail) {
            setTransactionItem({
                ...transactionItem,
                detail: {
                    ...transactionItem.detail,
                    quantity: Number(e.target.value),
                },
            });
        }
    };

    const handleAmountChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem && 'amount' in transactionItem.detail) {
            setTransactionItem({
                ...transactionItem,
                detail: {
                    ...transactionItem.detail,
                    amount: Number(e.target.value),
                },
            });
        }
    };

    useEffect(() => {
        if (transactionItem) {
            setTransactionItem({
                ...transactionItem,
                detail: {
                    ...transactionItem.detail,
                    quantity: defaultValue.rice_sales_quantity,
                    amount: defaultValue.rice_sales_amount,
                },
            });
        }
    }, [defaultValue]);

    return (
        <>
            <div className="grid grid-cols-1 gap-2 text-start">
                <Label htmlFor="customer">Muzakki</Label>
                <Input
                    id="customer"
                    value={transactionItem?.customer}
                    onChange={handleCustomerChange}
                    placeholder="Nama Muzakki"
                    required
                    className="w-full"
                />
            </div>

            <div className="grid grid-cols-1 gap-2 text-start">
                <Label htmlFor="quantity">Jumlah (Kg)</Label>
                <Input
                    id="quantity"
                    type="number"
                    value={
                        (transactionItem?.detail as RiceSalesItem)?.quantity ||
                        ''
                    }
                    onChange={handleQuantityChange}
                    placeholder="Jumlah"
                    required
                    className="w-full"
                />
            </div>

            <div className="grid grid-cols-1 gap-2 text-start">
                <Label htmlFor="amount">Nominal</Label>
                <Input
                    id="amount"
                    type="number"
                    value={
                        (transactionItem?.detail as RiceSalesItem)?.amount || ''
                    }
                    onChange={handleAmountChange}
                    placeholder="Nominal"
                    required
                    className="w-full"
                />
            </div>
        </>
    );
};

export default RiceSale;
