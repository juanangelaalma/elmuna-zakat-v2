import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    FIDYAH_ID,
    INFAQ_ID,
    RICE_SALES_ID,
    TRANSACTION_ITEM_TYPES,
    ZAKAT_MALL_ID,
    ZAKAT_RICE_ID,
} from '@/lib/constant';
import {
    DonationItem,
    FidyahItem,
    RiceItem,
    RiceSalesItem,
    TransactionItem,
    WealthItem,
} from '@/types';
import { Trash2 } from 'lucide-react';

const TransactionItemCard = ({
    item,
    handleDelete,
}: {
    item: TransactionItem;
    handleDelete: (item: TransactionItem) => void;
}) => {
    const transactionType = TRANSACTION_ITEM_TYPES.find(
        (t) => t.id === item.item_type,
    );
    const Icon = transactionType?.icon;

    const formatCurrency = (amount: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(amount);
    };

    const renderDetails = () => {
        switch (item.item_type) {
            case RICE_SALES_ID:
                const riceSales = item.detail as RiceSalesItem;
                return (
                    <>
                        {/* <DetailItem label="Quantity" value={`${riceSales.quantity} Kg`} /> */}
                        <DetailItem
                            label="Total"
                            value={formatCurrency(Number(riceSales.amount))}
                            highlight
                        />
                    </>
                );

            case ZAKAT_RICE_ID:
                const zakat = item.detail as RiceItem;
                return (
                    <DetailItem
                        label="Quantity"
                        value={`${zakat.quantity} Kg`}
                        highlight
                    />
                );

            case INFAQ_ID:
                const infaq = item.detail as DonationItem;
                return (
                    <>
                        <DetailItem
                            label="Tipe Donasi"
                            value={
                                infaq.donation_type === 'money'
                                    ? 'Uang'
                                    : 'Beras'
                            }
                        />
                        {infaq.donation_type === 'money' &&
                            infaq.amount !== null && (
                                <DetailItem
                                    label="Jumlah"
                                    value={formatCurrency(infaq.amount)}
                                    highlight
                                />
                            )}
                        {infaq.donation_type === 'rice' &&
                            infaq.quantity !== null && (
                                <DetailItem
                                    label="Quantity"
                                    value={`${infaq.quantity} Kg`}
                                    highlight
                                />
                            )}
                    </>
                );

            case FIDYAH_ID:
                const fidyah = item.detail as FidyahItem;
                return (
                    <>
                        <DetailItem
                            label="Tipe Fidyah"
                            value={
                                fidyah.fidyah_type === 'money'
                                    ? 'Uang'
                                    : 'Beras'
                            }
                        />
                        {fidyah.fidyah_type === 'money' &&
                            fidyah.amount !== null && (
                                <DetailItem
                                    label="Jumlah"
                                    value={formatCurrency(fidyah.amount)}
                                    highlight
                                />
                            )}
                        {fidyah.fidyah_type === 'rice' &&
                            fidyah.quantity !== null && (
                                <DetailItem
                                    label="Quantity"
                                    value={`${fidyah.quantity} Kg`}
                                    highlight
                                />
                            )}
                    </>
                );

            case ZAKAT_MALL_ID:
                const wealth = item.detail as WealthItem;
                return (
                    <DetailItem
                        label="Jumlah"
                        value={formatCurrency(wealth.amount)}
                        highlight
                    />
                );

            default:
                return null;
        }
    };

    return (
        <Card className="border border-border transition-colors hover:border-primary/50">
            <CardContent className="p-4 py-0">
                <div className="flex items-start justify-between gap-4">
                    {/* Left Section: Icon, Type & Customer */}
                    <div className="flex min-w-0 flex-1 items-start gap-3">
                        {Icon && (
                            <div className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                <Icon className="h-5 w-5 text-primary" />
                            </div>
                        )}
                        <div className="min-w-0 flex-1">
                            <h3 className="mb-1 text-base font-semibold">
                                {transactionType?.label}
                            </h3>
                            <p className="truncate text-sm text-muted-foreground">
                                {item.customer}
                            </p>
                        </div>
                    </div>

                    {/* Middle Section: Details */}
                    <div className="flex flex-shrink-0 items-center gap-6">
                        {renderDetails()}
                    </div>

                    {/* Right Section: Delete Button */}
                    <Button
                        variant="ghost"
                        size="icon"
                        type="button"
                        className="h-9 w-9 flex-shrink-0 text-destructive hover:bg-destructive/10 hover:text-destructive"
                        onClick={() => handleDelete(item)}
                    >
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
};

// Helper component for consistent detail items
const DetailItem = ({
    label,
    value,
    highlight = false,
}: {
    label: string;
    value: string;
    highlight?: boolean;
}) => (
    <div className="text-right">
        <p className="mb-0.5 text-xs text-muted-foreground">{label}</p>
        <p
            className={`text-sm font-semibold ${highlight ? 'text-primary' : ''}`}
        >
            {value}
        </p>
    </div>
);

export default TransactionItemCard;
