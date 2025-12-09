import { Label } from "@/components/ui/label"
import { Input } from '@/components/ui/input';
import { RiceItem, TransactionItem } from "@/types";
import { usePage } from '@inertiajs/react';
import { SharedData } from '@/types';
import { useEffect } from "react";

const Rice = ({ transactionItem, setTransactionItem }: { transactionItem: TransactionItem | null, setTransactionItem: (item: TransactionItem) => void }) => {
    const { defaultValue } = usePage<SharedData>().props

    const handleCustomerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem) {
            setTransactionItem({ ...transactionItem, customer: e.target.value });
        }
    };

    const handleQuantityChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem && 'quantity' in transactionItem.detail) {
            setTransactionItem({
                ...transactionItem,
                detail: { ...transactionItem.detail, quantity: Number(e.target.value) }
            });
        }
    };

    useEffect(() => {
        if (transactionItem) {
            setTransactionItem({
                ...transactionItem,
                detail: { ...transactionItem.detail, quantity: defaultValue.rice_quantity },
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
                <Label htmlFor="quantity">Quantity</Label>
                <Input
                    id="quantity"
                    type="number"
                    value={(transactionItem?.detail as RiceItem)?.quantity || ''}
                    onChange={handleQuantityChange}
                    placeholder="Quantity"
                    required
                    className="w-full"
                />
            </div>
        </>
    );
}

export default Rice;
