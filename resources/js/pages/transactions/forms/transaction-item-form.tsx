import { Button } from '@/components/ui/button';
import {
    FIDYAH_ID,
    INFAQ_ID,
    RICE_SALES_ID,
    ZAKAT_MALL_ID,
    ZAKAT_RICE_ID,
} from '@/lib/constant';
import { TransactionItem } from '@/types';
import { useState } from 'react';
import Donation from './donation';
import Fidyah from './fidyah';
import Rice from './rice';
import RiceSale from './rice-sales';
import Wealth from './wealth';

const TransactionItemForm = ({
    selectedItem,
    handleBack,
    handleAdd,
}: {
    selectedItem: string | null;
    handleBack: () => void;
    handleAdd: (newTransactionItem: TransactionItem) => void;
    item_type: string;
}) => {
    const [transactionItem, setTransactionItem] = useState<TransactionItem>({
        customer: '',
        item_type: selectedItem,
        detail: {
            quantity: null,
            amount: null,
            donation_type: '',
            fidyah_type: '',
        },
    });

    let content;
    switch (selectedItem) {
        case RICE_SALES_ID:
            content = (
                <RiceSale
                    transactionItem={transactionItem}
                    setTransactionItem={setTransactionItem}
                />
            );
            break;
        case ZAKAT_RICE_ID:
            content = (
                <Rice
                    transactionItem={transactionItem}
                    setTransactionItem={setTransactionItem}
                />
            );
            break;
        case INFAQ_ID:
            content = (
                <Donation
                    transactionItem={transactionItem}
                    setTransactionItem={setTransactionItem}
                />
            );
            break;
        case FIDYAH_ID:
            content = (
                <Fidyah
                    transactionItem={transactionItem}
                    setTransactionItem={setTransactionItem}
                />
            );
            break;
        case ZAKAT_MALL_ID:
            content = (
                <Wealth
                    transactionItem={transactionItem}
                    setTransactionItem={setTransactionItem}
                />
            );
            break;
        default:
            content = <p>Unknown</p>;
    }

    return (
        <div className="space-y-4">
            <div className="py-4">
                <div className="w-full">
                    <form className="space-y-3">{content}</form>
                </div>
            </div>
            <div className="flex justify-between pt-4">
                <Button variant="outline" onClick={handleBack}>
                    Kembali
                </Button>
                <Button onClick={() => handleAdd(transactionItem)}>
                    Tambah
                </Button>
            </div>
        </div>
    );
};

export default TransactionItemForm;
