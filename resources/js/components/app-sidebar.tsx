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
        title: 'Purchase',
        icon: ShoppingCart, // Parent icon
        children: [
            {
                title: 'Purchase',
                href: purchases(),
                icon: ShoppingCart,
            },
            {
                title: 'RiceItem',
                href: riceItems(), // Assuming riceItems() function exists for the route
                icon: Package2,
            },
        ],
    },
    {
        title: 'Transaction',
        icon: TicketSlash,
        children: [
            {
                title: 'All Transactions',
                href: transactions(),
                icon: TicketSlash,
            },
            {
                title: 'Rice Sales',
                href: riceSales(),
                icon: ShoppingCart,
            },
            {
                title: 'Rice',
                href: rice(),
                icon: Package2,
            },
            {
                title: 'Donations',
                href: donations(),
                icon: ShoppingCart,
            },
            {
                title: 'Fidyahs',
                href: fidyahs(),
                icon: Package2,
            },
            {
                title: 'Wealths',
                href: wealths(),
                icon: ShoppingCart,
            },
        ]
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Default Value',
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
