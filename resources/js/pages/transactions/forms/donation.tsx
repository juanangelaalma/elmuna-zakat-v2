import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { DonationItem, TransactionItem } from '@/types';

const Donation = ({
    transactionItem,
    setTransactionItem,
}: {
    transactionItem: TransactionItem | null;
    setTransactionItem: (item: TransactionItem) => void;
}) => {
    const handleCustomerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (transactionItem) {
            setTransactionItem({
                ...transactionItem,
                customer: e.target.value,
            });
        }
    };

    const handleDonationTypeChange = (value: string) => {
        if (transactionItem && 'donation_type' in transactionItem.detail) {
            // Reset field yang tidak relevan saat tipe berganti
            setTransactionItem({
                ...transactionItem,
                detail: {
                    ...(transactionItem.detail as DonationItem),
                    donation_type: value,
                    quantity: value === 'rice' ? (transactionItem.detail as DonationItem).quantity : null,
                    amount: value === 'money' ? (transactionItem.detail as DonationItem).amount : null,
                } as DonationItem,
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

            {transactionItem?.detail &&
                'donation_type' in transactionItem.detail ? (
                <>
                    <div className="grid grid-cols-1 gap-2 text-start">
                        <Label htmlFor="price">Tipe Donasi</Label>
                        <Select
                            value={transactionItem?.detail.donation_type}
                            onValueChange={handleDonationTypeChange}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih Tipe Donasi" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="money">Uang</SelectItem>
                                <SelectItem value="rice">Beras</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    {transactionItem.detail.donation_type === 'money' ? (
                        <div className="grid grid-cols-1 gap-2 text-start">
                            <Label htmlFor="price">Amount</Label>
                            <Input
                                id="price"
                                type="number"
                                value={transactionItem?.detail.amount || ''}
                                onChange={handleAmountChange}
                                placeholder="Amount"
                                required
                                className="w-full"
                            />
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 gap-2 text-start">
                            <Label htmlFor="quantity">Quantity</Label>
                            <Input
                                id="quantity"
                                type="number"
                                value={transactionItem?.detail.quantity || ''}
                                onChange={handleQuantityChange}
                                placeholder="Quantity"
                                required
                                className="w-full"
                            />
                        </div>
                    )}
                </>
            ) : null}
        </>
    );
};

export default Donation;
