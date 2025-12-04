import { Label } from "@/components/ui/label"
import { Input } from '@/components/ui/input';
import { WealthItem, TransactionItem } from "@/types";

const Wealth = ({ transactionItem, setTransactionItem }: { transactionItem: TransactionItem | null, setTransactionItem: (item: TransactionItem) => void }) => {
    const handleCustomerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem) {
            setTransactionItem({ ...transactionItem, customer: e.target.value });
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
                <Label htmlFor="amount">Jumlah</Label>
                <Input
                    id="amount"
                    type="number"
                    value={(transactionItem?.detail as WealthItem)?.amount || ''}
                    onChange={handleAmountChange}
                    placeholder="Jumlah"
                    required
                    className="w-full"
                />
            </div>
        </>
    );
}

export default Wealth;
