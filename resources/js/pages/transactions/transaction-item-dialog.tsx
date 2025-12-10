import { Button } from '@/components/ui/button';
import {
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { TRANSACTION_ITEM_TYPES } from '@/lib/constant';
import { cn } from '@/lib/utils';
import { TransactionItem } from '@/types';
import { Dialog } from '@radix-ui/react-dialog';
import { ArrowRight, PlusIcon } from 'lucide-react';
import { useState } from 'react';
import TransactionItemForm from './forms/transaction-item-form';

const TransactionItemDialog = ({
    items,
    setItems,
}: {
    items: Array<TransactionItem>;
    setItems: (items: Array<TransactionItem>) => void;
}) => {
    const [step, setStep] = useState<number>(1);
    const [selectedItem, setSelectedItem] = useState<string | null>(null);
    const [open, setOpen] = useState<boolean>(false);

    const handleNext = () => {
        if (selectedItem) {
            setStep(2);
        }
    };

    const handleBack = () => {
        setStep(1);
    };

    const handleAdd = (newTransactionItem: TransactionItem) => {
        setItems([...items, newTransactionItem]);
        setSelectedItem(null);
        setStep(1);
        setOpen(false);
    };

    return (
        <Dialog
            open={open}
            onOpenChange={(open) => {
                if (!open) {
                    setStep(1);
                    setSelectedItem(null);
                }
                setOpen(open);
            }}
        >
            <DialogTrigger asChild>
                <Button
                    onClick={() => setOpen(true)}
                    variant="outline"
                    className="h-12"
                >
                    <PlusIcon className="mr-2 h-4 w-4" />
                    Tambah Item
                </Button>
            </DialogTrigger>
            <DialogContent className="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>
                        {step === 1
                            ? 'Pilih Jenis Transaksi'
                            : TRANSACTION_ITEM_TYPES.find(
                                  (i) => i.id === selectedItem,
                              )?.label}
                    </DialogTitle>
                </DialogHeader>

                {step === 1 ? (
                    <div className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            {TRANSACTION_ITEM_TYPES.map((item) => (
                                <div
                                    key={item.id}
                                    className={cn(
                                        'cursor-pointer rounded-xl border-2 p-4 transition-all hover:border-primary/50 hover:bg-accent',
                                        selectedItem === item.id
                                            ? 'border-primary bg-accent'
                                            : 'border-muted bg-card',
                                    )}
                                    onClick={() => setSelectedItem(item.id)}
                                >
                                    <div className="flex flex-col items-center gap-2 text-center">
                                        <item.icon className="h-8 w-8 text-muted-foreground" />
                                        <span className="text-sm font-medium">
                                            {item.label}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <div className="flex justify-end pt-4">
                            <Button
                                onClick={handleNext}
                                disabled={!selectedItem}
                                className="w-full sm:w-auto"
                            >
                                Lanjut
                                <ArrowRight className="ml-2 h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                ) : (
                    <TransactionItemForm
                        handleAdd={handleAdd}
                        selectedItem={selectedItem}
                        handleBack={handleBack}
                    />
                )}
            </DialogContent>
        </Dialog>
    );
};

export default TransactionItemDialog;
