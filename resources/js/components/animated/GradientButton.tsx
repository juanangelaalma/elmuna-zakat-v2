import { motion } from 'framer-motion';
import { ReactNode } from 'react';
import { cn } from '@/lib/utils';

interface GradientButtonProps {
    children: ReactNode;
    className?: string;
    variant?: 'primary' | 'secondary' | 'accent';
    onClick?: () => void;
    href?: string;
}

const variantStyles = {
    primary: 'bg-gradient-to-br from-primary via-primary/90 to-primary/80 text-primary-foreground shadow-[0_4px_16px_-2px_oklch(0.65_0.15_240/0.4)]',
    secondary: 'bg-gradient-to-br from-secondary via-secondary/90 to-secondary/80 text-secondary-foreground shadow-[0_4px_16px_-2px_oklch(0.85_0.08_160/0.4)]',
    accent: 'bg-gradient-to-br from-accent via-accent/90 to-accent/80 text-accent-foreground shadow-[0_4px_16px_-2px_oklch(0.78_0.12_280/0.4)]',
};

export default function GradientButton({ children, className, variant = 'primary', onClick, href }: GradientButtonProps) {
    const Component = href ? motion.a : motion.button;
    const props = href ? { href } : { onClick };

    return (
        <Component
            {...props}
            whileHover={{ scale: 1.05, y: -2 }}
            whileTap={{ scale: 0.98 }}
            transition={{ type: 'spring', stiffness: 400, damping: 17 }}
            className={cn(
                'relative inline-flex items-center justify-center',
                'px-8 py-4 rounded-xl font-medium',
                'overflow-hidden',
                'transition-all duration-300',
                'hover:shadow-[0_8px_24px_-4px_currentColor/0.5]',
                variantStyles[variant],
                className
            )}
        >
            <motion.span
                className="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0"
                whileHover={{ opacity: 1 }}
                transition={{ duration: 0.3 }}
            />
            <span className="relative z-10">{children}</span>
        </Component>
    );
}
