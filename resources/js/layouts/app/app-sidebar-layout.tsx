import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren } from 'react';

export default function AppSidebarLayout({
    children,
    breadcrumbs = [],
}: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
                <p className="text-center text-xs text-gray-400 dark:text-gray-600 italic py-3 pb-5">
                    Built with{' '}
                    <span className="text-red-400 not-italic">❤</span>
                    {' '}by{' '}
                    <span className="font-semibold text-emerald-600 dark:text-emerald-400 not-italic">Remas El Muna</span>
                </p>
            </AppContent>
        </AppShell>
    );
}
