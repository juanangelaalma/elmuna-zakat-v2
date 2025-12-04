import { Label } from "@/components/ui/label"
import { Input } from '@/components/ui/input';
import { RiceSalesItem, TransactionItem } from "@/types";

const RiceSale = ({ transactionItem, setTransactionItem }: { transactionItem: TransactionItem | null, setTransactionItem: (item: TransactionItem) => void }) => {
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

    const handleAmountChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem && 'amount' in transactionItem.detail) {
            setTransactionItem({
                ...transactionItem,
                detail: { ...transactionItem.detail, amount: Number(e.target.value) }
            });
        }
    };

    return (
        <>
            <div className="grid grid-cols-1 gap-2 text-start">
                <Label htmlFor="customer">Customer</Label>
                <Input
                    id="customer"
                    value={transactionItem?.customer}
                    onChange={handleCustomerChange}
                    placeholder="Nama Customer"
                    required
                    className="w-full"
                />
            </div>

            <div className="grid grid-cols-1 gap-2 text-start">
                <Label htmlFor="quantity">Quantity</Label>
                <Input
                    id="quantity"
                    type="number"
                    value={(transactionItem?.detail as RiceSalesItem)?.quantity || ''}
                    onChange={handleQuantityChange}
                    placeholder="Quantity"
                    required
                    className="w-full"
                />
            </div>

            <div className="grid grid-cols-1 gap-2 text-start">
                <Label htmlFor="amount">Jumlah</Label>
                <Input
                    id="amount"
                    type="number"
                    value={(transactionItem?.detail as RiceSalesItem)?.amount || ''}
                    onChange={handleAmountChange}
                    placeholder="Amount"
                    required
                    className="w-full"
                />
            </div>
        </>
    );
}

export default RiceSale;
