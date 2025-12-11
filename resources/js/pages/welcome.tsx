import AnimatedIcon from '@/components/animated/AnimatedIcon';
import CursorTracker from '@/components/animated/CursorTracker';
import FloatingCard from '@/components/animated/FloatingCard';
import GradientButton from '@/components/animated/GradientButton';
import ParallaxSection from '@/components/animated/ParallaxSection';
import AppLogo from '@/components/app-logo';
import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, BarChart3, LucideProps, Shield, Sparkles, Zap } from 'lucide-react';
import { lazy, Suspense, useEffect, useState } from 'react';

const Hero3DScene = lazy(() => import('@/components/three/Hero3DScene'));
const Hero3DSceneMobile = lazy(() => import('@/components/three/Hero3DSceneMobile'));

interface FeatureProps {
    icon: React.ComponentType<LucideProps>;
    title: string;
    description: string;
    delay: number;
}

function Feature({ icon: Icon, title, description, delay }: FeatureProps) {
    return (
        <FloatingCard delay={delay} className="group">
            <div className="mb-4 inline-flex rounded-xl bg-gradient-to-br from-primary/10 to-accent/10 p-3">
                <AnimatedIcon icon={Icon} size={24} animation="float" className="text-primary" />
            </div>
            <h3 className="mb-2 text-lg font-semibold text-foreground">{title}</h3>
            <p className="text-sm text-muted-foreground leading-relaxed">{description}</p>
        </FloatingCard>
    );
}

export default function WelcomeNew({ canRegister = true }: { canRegister?: boolean }) {
    const { auth } = usePage<SharedData>().props;
    const [isMobile, setIsMobile] = useState(false);

    useEffect(() => {
        const checkMobile = () => {
            setIsMobile(window.innerWidth < 1024);
        };
        checkMobile();
        window.addEventListener('resize', checkMobile);
        return () => window.removeEventListener('resize', checkMobile);
    }, []);

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700"
                    rel="stylesheet"
                />
            </Head>

            <CursorTracker />

            <div className="relative min-h-screen overflow-hidden bg-gradient-to-br from-background via-background to-muted/30">
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,oklch(0.78_0.12_280/0.15),transparent_50%)]" />
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,oklch(0.65_0.15_240/0.1),transparent_50%)]" />

                <header className="relative z-10 px-6 pt-8 lg:px-8">
                    <nav className="mx-auto flex max-w-7xl items-center justify-between">
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.6 }}
                            className="flex items-center gap-2"
                        >
                            <AppLogo />
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.6 }}
                            className="flex items-center gap-3"
                        >
                            {auth.user ? (
                                <Link href={dashboard()}>
                                    <GradientButton variant="primary">
                                        Dashboard <ArrowRight className="ml-2 h-4 w-4" />
                                    </GradientButton>
                                </Link>
                            ) : (
                                <>
                                    <Link href={login()}>
                                        <motion.button
                                            whileHover={{ scale: 1.05 }}
                                            whileTap={{ scale: 0.95 }}
                                            className="rounded-xl px-6 py-2.5 text-sm font-medium text-foreground transition-colors hover:text-primary"
                                        >
                                            Masuk
                                        </motion.button>
                                    </Link>
                                    {canRegister && (
                                        <Link href={register()}>
                                            <GradientButton variant="primary">Daftar</GradientButton>
                                        </Link>
                                    )}
                                </>
                            )}
                        </motion.div>
                    </nav>
                </header>

                <section className="relative z-10 px-6 py-16 lg:px-8 lg:py-24">
                    <div className="mx-auto max-w-7xl">
                        <div className="grid gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                            <div>
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ duration: 0.6, delay: 0.2 }}
                                    className="mb-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary/10 to-accent/10 px-4 py-2 text-sm font-medium text-primary"
                                >
                                    <Sparkles className="h-4 w-4" />
                                    <span>Masjid Al Munawwar</span>
                                </motion.div>

                                <motion.h1
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ duration: 0.6, delay: 0.3 }}
                                    className="mb-6 text-5xl font-bold leading-tight lg:text-7xl"
                                >
                                    <span className="bg-gradient-to-r from-foreground via-primary to-accent bg-clip-text text-transparent">
                                        Elmuna Zakat
                                    </span>
                                    <br />
                                    <span className="text-foreground">Al Munawwar</span>
                                </motion.h1>

                                <motion.p
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ duration: 0.6, delay: 0.4 }}
                                    className="mb-8 text-lg text-muted-foreground leading-relaxed lg:text-xl"
                                >
                                    Sistem manajemen zakat digital untuk Masjid Al Munawwar.
                                    Dikembangkan oleh Remas Al Munawwar untuk memudahkan pengelolaan
                                    zakat, infaq, dan sedekah dengan teknologi modern.
                                </motion.p>

                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ duration: 0.6, delay: 0.5 }}
                                    className="flex flex-col gap-4 sm:flex-row"
                                >
                                    {auth.user ? (
                                        <Link href={dashboard()}>
                                            <GradientButton variant="primary" className="w-full sm:w-auto">
                                                Buka Dashboard <ArrowRight className="ml-2 h-5 w-5" />
                                            </GradientButton>
                                        </Link>
                                    ) : (
                                        <Link href={login()}>
                                            <GradientButton variant="primary" className="w-full sm:w-auto">
                                                Masuk
                                            </GradientButton>
                                        </Link>
                                    )}
                                </motion.div>

                                <motion.div
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    transition={{ duration: 0.6, delay: 0.7 }}
                                    className="mt-12 flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-8"
                                >
                                    <div className="text-sm text-muted-foreground">
                                        <span className="font-semibold text-foreground">Dikembangkan oleh:</span>
                                        <br />
                                        <span className="text-base font-medium text-primary">Remas Al Munawwar</span>
                                    </div>
                                    <div className="hidden h-12 w-px bg-border sm:block" />
                                    <div className="text-sm text-muted-foreground">
                                        <span className="font-semibold text-foreground">Untuk:</span>
                                        <br />
                                        <span className="text-base font-medium text-accent">Masjid Al Munawwar</span>
                                    </div>
                                </motion.div>
                            </div>

                            <motion.div
                                initial={{ opacity: 0, scale: 0.9 }}
                                animate={{ opacity: 1, scale: 1 }}
                                transition={{ duration: 0.8, delay: 0.4 }}
                                className="relative h-[400px] lg:h-[600px]"
                            >
                                <Suspense
                                    fallback={
                                        <div className="flex h-full items-center justify-center">
                                            <motion.div
                                                animate={{ rotate: 360 }}
                                                transition={{ duration: 1, repeat: Infinity as number, ease: 'linear' as const }}
                                                className="h-12 w-12 rounded-full border-4 border-primary/20 border-t-primary"
                                            />
                                        </div>
                                    }
                                >
                                    {isMobile ? (
                                        <Hero3DSceneMobile className="h-full w-full" />
                                    ) : (
                                        <Hero3DScene className="h-full w-full" />
                                    )}
                                </Suspense>
                            </motion.div>
                        </div>
                    </div>
                </section>

                <ParallaxSection offset={30}>
                    <section className="relative z-10 px-6 py-16 lg:px-8 lg:py-24">
                        <div className="mx-auto max-w-7xl">
                            <motion.div
                                initial={{ opacity: 0, y: 20 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.6 }}
                                className="mb-12 text-center"
                            >
                                <h2 className="mb-4 text-4xl font-bold lg:text-5xl">
                                    <span className="bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                                        Fitur Sistem
                                    </span>
                                </h2>
                                <p className="mx-auto max-w-2xl text-lg text-muted-foreground">
                                    Sistem lengkap untuk pengelolaan zakat, infaq, dan sedekah di Masjid Al Munawwar
                                </p>
                            </motion.div>

                            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                <Feature
                                    icon={Zap}
                                    title="Mudah Digunakan"
                                    description="Interface yang intuitif memudahkan pengurus dalam mencatat dan mengelola data zakat"
                                    delay={0.1}
                                />
                                <Feature
                                    icon={Shield}
                                    title="Data Terjaga"
                                    description="Sistem keamanan yang baik menjaga data muzakki dan transaksi dengan aman"
                                    delay={0.2}
                                />
                                <Feature
                                    icon={BarChart3}
                                    title="Laporan Lengkap"
                                    description="Dashboard dengan visualisasi data untuk memudahkan monitoring dan pelaporan"
                                    delay={0.3}
                                />
                            </div>
                        </div>
                    </section>
                </ParallaxSection>

                <ParallaxSection offset={50}>
                    <section className="relative z-10 px-6 py-16 lg:px-8">
                        <div className="mx-auto max-w-4xl">
                            <FloatingCard className="bg-gradient-to-br from-primary/5 via-accent/5 to-secondary/5 p-12 text-center">
                                <motion.div
                                    initial={{ opacity: 0, scale: 0.9 }}
                                    whileInView={{ opacity: 1, scale: 1 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.6 }}
                                >
                                    <h2 className="mb-4 text-3xl font-bold lg:text-4xl">
                                        Tentang Sistem Ini
                                    </h2>
                                    <p className="mb-8 text-lg text-muted-foreground leading-relaxed">
                                        Elmuna Zakat adalah sistem manajemen zakat digital yang dikembangkan
                                        khusus untuk Masjid Al Munawwar oleh tim Remas Al Munawwar.
                                        Sistem ini tidak diperjualbelikan dan merupakan bagian dari upaya
                                        meningkatkan pelayanan kepada jamaah dalam pengelolaan zakat, infaq, dan sedekah.
                                    </p>
                                    {!auth.user && (
                                        <Link href={login()}>
                                            <GradientButton variant="primary">
                                                Akses Sistem <ArrowRight className="ml-2 h-5 w-5" />
                                            </GradientButton>
                                        </Link>
                                    )}
                                </motion.div>
                            </FloatingCard>
                        </div>
                    </section>
                </ParallaxSection>

                <footer className="relative z-10 border-t border-border/50 px-6 py-8 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <div className="flex flex-col items-center justify-between gap-4 md:flex-row">
                            <div className="flex items-center gap-2">
                                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-accent">
                                    <Sparkles className="h-4 w-4 text-white" />
                                </div>
                                <span className="font-semibold text-foreground">Elmuna Zakat</span>
                            </div>
                            <p className="text-sm text-muted-foreground text-center">
                                © 2024 Masjid Al Munawwar. Developed by Remas Al Munawwar
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
