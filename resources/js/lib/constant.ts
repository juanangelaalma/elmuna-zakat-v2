import { HandCoins, Heart, Package, Utensils, Wallet } from 'lucide-react';

export const RICE_SALES_ID = 'RICE_SALES';
export const ZAKAT_RICE_ID = 'RICE';
export const INFAQ_ID = 'DONATION';
export const FIDYAH_ID = 'FIDYAH';
export const ZAKAT_MALL_ID = 'WEALTH';

export const TRANSACTION_ITEM_TYPES = [
    {
        id: RICE_SALES_ID,
        label: 'Penjualan Beras',
        icon: Package,
    },
    {
        id: ZAKAT_RICE_ID,
        label: 'Zakat Beras',
        icon: HandCoins,
    },
    {
        id: INFAQ_ID,
        label: 'Infaq',
        icon: Heart,
    },
    {
        id: FIDYAH_ID,
        label: 'Fidyah',
        icon: Utensils,
    },
    {
        id: ZAKAT_MALL_ID,
        label: 'Zakat Mall',
        icon: Wallet,
    },
];
