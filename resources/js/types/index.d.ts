import {
    FIDYAH_ID,
    INFAQ_ID,
    RICE_SALES_ID,
    ZAKAT_MALL_ID,
    ZAKAT_RICE_ID,
} from '@/lib/constant';
import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href?: NonNullable<InertiaLinkProps['href']>; // Make href optional for parent items
    icon?: LucideIcon | null;
    isActive?: boolean;
    children?: NavItem[]; // Add children property
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    defaultValue: DefaultValue;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface RiceSalesItem {
    quantity: number;
    amount: number | null;
}

export interface RiceItem {
    quantity: number;
}

export interface DonationItem {
    donation_type: string;
    amount: number | null;
    quantity: number | null;
}

export interface FidyahItem {
    fidyah_type: string;
    amount: number | null;
    quantity: number | null;
}

export interface WealthItem {
    amount: number;
}

export interface TransactionItem {
    customer: string;
    item_type:
        | RICE_SALES_ID
        | ZAKAT_RICE_ID
        | INFAQ_ID
        | FIDYAH_ID
        | ZAKAT_MALL_ID;
    detail: RiceItem | RiceSalesItem | DonationItem | FidyahItem | WealthItem;
}

export interface DefaultValue {
    rice_sales_quantity: number;
    rice_sales_amount: number;
    rice_quantity: number;
    fidyah_quantity: number;
    fidyah_amount: number;
    unit: string;
}
