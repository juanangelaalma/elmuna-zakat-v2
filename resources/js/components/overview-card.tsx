import { TrendingUp, type LucideIcon } from 'lucide-react';

interface OverviewCardProps {
    title: string;
    value: string;
    subtitle: string;
    icon: LucideIcon;
    gradient: string;
    iconBg: string;
}

const OverviewCard = ({
    title,
    value,
    subtitle,
    icon: Icon,
    gradient,
    iconBg,
}: OverviewCardProps) => {
    return (
        <div
            className={`relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-gradient-to-br dark:border-sidebar-border ${gradient} p-6 shadow-sm transition-shadow hover:shadow-md`}
        >
            <div className="relative z-10 flex h-full flex-row gap-4">
                <div className="flex items-start justify-between">
                    <div
                        className={`flex h-20 w-20 items-center justify-center rounded-xl ${iconBg} shadow-sm`}
                    >
                        <Icon className="h-10 w-10 text-white" />
                    </div>
                </div>

                <div className="mt-0">
                    <p className="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {title}
                    </p>
                    <h3 className="mt-1 text-3xl font-bold text-gray-900 dark:text-white">
                        {value}
                    </h3>
                    {subtitle && (
                        <p className="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <TrendingUp className="mr-1 h-3 w-3" />
                            {subtitle}
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
};

export { OverviewCard };
