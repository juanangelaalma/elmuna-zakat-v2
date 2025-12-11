import { Sparkles } from 'lucide-react';

export default function AppLogo() {
    return (
        <>
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-accent">
                <Sparkles className="h-5 w-5 text-white" />
            </div>
            <span className="text-xl font-bold bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                ElmunaZakat
            </span>
        </>
    );
}
