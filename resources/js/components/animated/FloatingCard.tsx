import { motion } from 'framer-motion';
import { ReactNode } from 'react';
import { cn } from '@/lib/utils';

interface FloatingCardProps {
    children: ReactNode;
    className?: string;
    delay?: number;
    hover?: boolean;
}

export default function FloatingCard({ children, className, delay = 0, hover = true }: FloatingCardProps) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay, ease: [0.22, 1, 0.36, 1] }}
            whileHover={hover ? { y: -8, scale: 1.02 } : undefined}
            className={cn(
                'rounded-2xl bg-card/80 backdrop-blur-xl p-6',
                'shadow-[0_8px_32px_-4px_rgba(0,0,0,0.08)]',
                'border border-border/50',
                'transition-shadow duration-300',
                hover && 'hover:shadow-[0_20px_48px_-8px_rgba(0,0,0,0.12)]',
                className
            )}
        >
            {children}
        </motion.div>
    );
}
