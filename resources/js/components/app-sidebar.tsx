import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {
    dashboard,
    defaultValue,
    purchases,
    riceItems,
    transactions,
} from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {
    BookOpen,
    Folder,
    LayoutGrid,
    Package2,
    Settings,
    ShoppingCart,
    TicketSlash,
    Receipt,
    Trash2,
} from 'lucide-react';
import AppLogo from './app-logo';
import { donations, fidyahs, rice, riceSales, wealths } from '@/routes/transactions';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Pembelian',
        icon: ShoppingCart, // Parent icon
        children: [
            {
                title: 'Data Pembelian',
                href: purchases(),
                icon: ShoppingCart,
            },
            {
                title: 'Stok Beras',
                href: riceItems(), // Assuming riceItems() function exists for the route
                icon: Package2,
            },
        ],
    },
    {
        title: 'Transaksi',
        icon: TicketSlash,
        children: [
            {
                title: 'Semua Transaksi',
                href: transactions(),
                icon: TicketSlash,
            },
            {
                title: 'Penjualan Beras',
                href: riceSales(),
                icon: ShoppingCart,
            },
            {
                title: 'Zakat Fitrah',
                href: rice(),
                icon: Package2,
            },
            {
                title: 'Infak/Sedekah',
                href: donations(),
                icon: ShoppingCart,
            },
            {
                title: 'Fidyah',
                href: fidyahs(),
                icon: Package2,
            },
            {
                title: 'Zakat Mal',
                href: wealths(),
                icon: ShoppingCart,
            },
            {
                title: 'Sampah Transaksi',
                href: '/transactions/trash',
                icon: Trash2,
            },
        ]
    },
    {
        title: 'Pengeluaran',
        href: '/expenses',
        icon: Receipt,
    },
    {
        title: 'Serah Terima',
        href: '/shift-handovers',
        icon: Folder,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Pengaturan Nilai',
        href: defaultValue(),
        icon: Settings,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
