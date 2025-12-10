import {
    SidebarCollapsible,
    SidebarCollapsibleContent,
    SidebarCollapsibleTrigger,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { resolveUrl } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();
    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Platform</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => {
                    if (item.children && item.children.length > 0) {
                        const isParentActive = item.children.some((child) =>
                            page.url.startsWith(resolveUrl(child.href!)),
                        );

                        return (
                            <SidebarCollapsible
                                key={item.title}
                                defaultOpen={isParentActive}
                            >
                                <SidebarMenuItem>
                                    <SidebarCollapsibleTrigger
                                        className="cursor-pointer"
                                        icon={item.icon}
                                        isActive={isParentActive}
                                        tooltip={{ children: item.title }}
                                    >
                                        <span>{item.title}</span>
                                    </SidebarCollapsibleTrigger>
                                </SidebarMenuItem>
                                <SidebarCollapsibleContent>
                                    <SidebarMenu>
                                        {item.children.map((child) => (
                                            <SidebarMenuItem
                                                className="ml-6"
                                                key={child.title}
                                            >
                                                <SidebarMenuButton
                                                    asChild
                                                    isActive={page.url.startsWith(
                                                        resolveUrl(child.href!),
                                                    )}
                                                    tooltip={{
                                                        children: child.title,
                                                    }}
                                                >
                                                    <Link
                                                        href={child.href!}
                                                        prefetch
                                                    >
                                                        {child.icon && (
                                                            <child.icon />
                                                        )}
                                                        <span>
                                                            {child.title}
                                                        </span>
                                                    </Link>
                                                </SidebarMenuButton>
                                            </SidebarMenuItem>
                                        ))}
                                    </SidebarMenu>
                                </SidebarCollapsibleContent>
                            </SidebarCollapsible>
                        );
                    } else {
                        return (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton
                                    asChild
                                    isActive={
                                        !!item.href &&
                                        page.url.startsWith(
                                            resolveUrl(item.href),
                                        )
                                    }
                                    tooltip={{ children: item.title }}
                                >
                                    <Link href={item.href || '#'} prefetch>
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        );
                    }
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
