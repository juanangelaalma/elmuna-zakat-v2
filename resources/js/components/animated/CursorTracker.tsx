import { motion } from 'framer-motion';
import { useEffect, useState } from 'react';

interface Particle {
    id: number;
    x: number;
    y: number;
}

export default function CursorTracker() {
    const [mousePosition, setMousePosition] = useState({ x: 0, y: 0 });
    const [particles, setParticles] = useState<Particle[]>([]);
    const [isMoving, setIsMoving] = useState(false);

    useEffect(() => {
        let timeout: NodeJS.Timeout;
        let particleId = 0;

        const handleMouseMove = (e: MouseEvent) => {
            setMousePosition({ x: e.clientX, y: e.clientY });
            setIsMoving(true);

            if (Math.random() > 0.9) {
                const newParticle: Particle = {
                    id: particleId++,
                    x: e.clientX,
                    y: e.clientY,
                };
                setParticles((prev) => [...prev.slice(-5), newParticle]);
            }

            clearTimeout(timeout);
            timeout = setTimeout(() => setIsMoving(false), 100);
        };

        window.addEventListener('mousemove', handleMouseMove);
        return () => {
            window.removeEventListener('mousemove', handleMouseMove);
            clearTimeout(timeout);
        };
    }, []);

    useEffect(() => {
        const timer = setInterval(() => {
            setParticles((prev) => prev.slice(1));
        }, 1000);
        return () => clearInterval(timer);
    }, []);

    return (
        <>
            <motion.div
                className="pointer-events-none fixed z-50 mix-blend-difference hidden lg:block"
                animate={{
                    x: mousePosition.x - 16,
                    y: mousePosition.y - 16,
                    scale: isMoving ? 1 : 0.8,
                }}
                transition={{
                    type: 'spring',
                    stiffness: 500,
                    damping: 28,
                    mass: 0.5,
                }}
            >
                <div className="h-8 w-8 rounded-full border-2 border-primary/50" />
            </motion.div>

            {particles.map((particle) => (
                <motion.div
                    key={particle.id}
                    className="pointer-events-none fixed z-40 hidden lg:block"
                    initial={{
                        x: particle.x - 4,
                        y: particle.y - 4,
                        opacity: 0.6,
                        scale: 1,
                    }}
                    animate={{
                        opacity: 0,
                        scale: 0,
                    }}
                    transition={{ duration: 1 }}
                >
                    <div className="h-2 w-2 rounded-full bg-gradient-to-br from-primary to-accent" />
                </motion.div>
            ))}
        </>
    );
}
