import { motion } from 'framer-motion';
import { LucideIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

interface AnimatedIconProps {
    icon: LucideIcon;
    className?: string;
    size?: number;
    animation?: 'bounce' | 'pulse' | 'spin' | 'float';
}

export default function AnimatedIcon({ icon: Icon, className, size = 24, animation = 'float' }: AnimatedIconProps) {
    const getAnimation = () => {
        switch (animation) {
            case 'bounce':
                return { y: [0, -10, 0] };
            case 'pulse':
                return { scale: [1, 1.1, 1] };
            case 'spin':
                return { rotate: 360 };
            case 'float':
            default:
                return { y: [0, -5, 0], x: [0, 3, 0] };
        }
    };

    const getTransition = () => {
        switch (animation) {
            case 'spin':
                return { duration: 3, repeat: Infinity as number, ease: 'linear' as const };
            default:
                return { duration: 2, repeat: Infinity as number, ease: 'easeInOut' as const };
        }
    };

    return (
        <motion.div
            animate={getAnimation()}
            transition={getTransition()}
            whileHover={{ scale: 1.1 }}
            className={cn('inline-flex', className)}
        >
            <Icon size={size} />
        </motion.div>
    );
}
