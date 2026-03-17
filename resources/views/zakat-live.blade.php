<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Zakat Live — UPZ AL MUNAWWAR</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#2d6a4f",
                        secondary: "#d4a017",
                        "bg-light": "#f8fafc",
                        "surface": "#ffffff",
                        "text-dark": "#1e293b",
                        "text-muted": "#64748b",
                    },
                    fontFamily: { display: ["Inter", "sans-serif"] },
                    boxShadow: {
                        soft: '0 4px 6px -1px rgba(0,0,0,.05),0 2px 4px -1px rgba(0,0,0,.03)',
                        card: '0 10px 15px -3px rgba(0,0,0,.05),0 4px 6px -2px rgba(0,0,0,.025)',
                    },
                    screens: {
                        'tv': '1920px',
                        '4k': '2560px',
                    },
                },
            },
        };
    </script>
    <style>
        :root {
            /* Scale factor based on viewport width — adapts from HD to 4K/TV */
            --scale: clamp(1rem, 1.2vw, 1.8rem);
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Marquee ── */
        .marquee-wrap { overflow: hidden; white-space: nowrap; }
        .marquee-inner { display: inline-block; animation: marquee 600s linear infinite; }
        @keyframes marquee {
            0%   { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }

        /* ── Bar chart ── */
        .bar-col { transition: height .5s ease; }

        /* ── Fade-in on data refresh ── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
        .fade-in { animation: fadeIn .4s ease both; }

        /* ── Live dot pulse ── */
        @keyframes livePulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .35; }
        }
        .live-dot { animation: livePulse 1.5s ease-in-out infinite; }

        /* ── Fluid typography helpers ── */
        .text-fluid-xs   { font-size: clamp(.625rem,  .65vw, 1rem); }
        .text-fluid-sm   { font-size: clamp(.75rem,   .8vw,  1.15rem); }
        .text-fluid-base { font-size: clamp(.875rem,  .95vw, 1.35rem); }
        .text-fluid-lg   { font-size: clamp(1rem,     1.1vw, 1.6rem); }
        .text-fluid-xl   { font-size: clamp(1.125rem, 1.3vw, 1.9rem); }
        .text-fluid-2xl  { font-size: clamp(1.25rem,  1.6vw, 2.4rem); }
        .text-fluid-3xl  { font-size: clamp(1.5rem,   2vw,   3rem); }
        .text-fluid-4xl  { font-size: clamp(1.875rem, 2.8vw, 4.2rem); }
        .text-fluid-5xl  { font-size: clamp(2.25rem,  3.5vw, 5.5rem); }
        .text-fluid-6xl  { font-size: clamp(3rem,     4.5vw, 7rem); }

        /* ── Icon scale ── */
        .icon-fluid      { font-size: clamp(1.25rem, 1.8vw, 2.8rem) !important; }
        .icon-fluid-lg   { font-size: clamp(2rem,    3vw,   4.5rem) !important; }
        .icon-fluid-xl   { font-size: clamp(4rem,    6vw,   9rem)   !important; }

        /* ── Dot / ring scale ── */
        .dot-fluid { width: clamp(.5rem, .7vw, 1rem); height: clamp(.5rem, .7vw, 1rem); }

        /* ── Card padding ── */
        .p-fluid { padding: clamp(1rem, 1.4vw, 2rem); }
        .gap-fluid { gap: clamp(.75rem, 1.2vw, 2rem); }

        /* ── Rounded scale ── */
        .rounded-fluid { border-radius: clamp(.75rem, 1.2vw, 1.75rem); }
    </style>
</head>
<body class="bg-bg-light text-text-dark font-display min-h-screen lg:h-screen lg:max-h-screen flex flex-col overflow-y-auto lg:overflow-hidden selection:bg-primary/20 selection:text-primary">

<div class="flex-1 flex flex-col gap-fluid p-fluid lg:min-h-0">

    {{-- ═══════════════════ HEADER ═══════════════════ --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-[clamp(1rem,1.4vw,2rem)]">
        <div class="flex items-center gap-fluid">
            {{-- Logo icon --}}
            <div class="bg-white rounded-full flex items-center justify-center text-primary border border-slate-100 shadow-soft"
                 style="width:clamp(3rem,5vw,7rem);height:clamp(3rem,5vw,7rem)">
                <span class="material-symbols-outlined icon-fluid-lg">mosque</span>
            </div>
            <div>
                <h1 class="text-fluid-3xl font-black tracking-tight text-slate-800">UPZ AL MUNAWWAR</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="live-dot dot-fluid rounded-full bg-green-500 inline-block shrink-0"></span>
                    <p class="text-fluid-sm text-text-muted font-semibold uppercase tracking-wider">Dashboard Zakat Real-Time</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-fluid">
            {{-- Masehi --}}
            <div class="hidden sm:flex items-center gap-[clamp(.75rem,1vw,1.5rem)] bg-white px-[clamp(1rem,1.5vw,2rem)] py-[clamp(.5rem,.8vw,1.2rem)] rounded-fluid border border-slate-100 shadow-soft">
                <span class="material-symbols-outlined text-secondary icon-fluid">calendar_month</span>
                <div class="flex flex-col text-right">
                    <span class="text-fluid-xs text-text-muted uppercase font-bold tracking-wider">Masehi</span>
                    <span id="date-masehi" class="text-fluid-base font-bold text-slate-700">—</span>
                </div>
            </div>
            {{-- Hijriah --}}
            <div class="hidden sm:flex items-center gap-[clamp(.75rem,1vw,1.5rem)] bg-white px-[clamp(1rem,1.5vw,2rem)] py-[clamp(.5rem,.8vw,1.2rem)] rounded-fluid border border-slate-100 shadow-soft">
                <span class="material-symbols-outlined text-primary icon-fluid">nights_stay</span>
                <div class="flex flex-col text-right">
                    <span class="text-fluid-xs text-text-muted uppercase font-bold tracking-wider">Hijriah</span>
                    <span id="date-hijriah" class="text-fluid-base font-bold text-secondary">—</span>
                </div>
            </div>
            {{-- Jam --}}
            <div class="flex items-center gap-[clamp(.5rem,.8vw,1.2rem)] bg-gradient-to-br from-primary to-[#1b4332] px-[clamp(1.25rem,2vw,3rem)] py-[clamp(.5rem,.8vw,1.2rem)] rounded-fluid shadow-lg shadow-primary/20 text-white">
                <span class="material-symbols-outlined icon-fluid">schedule</span>
                <span id="clock" class="text-fluid-2xl font-bold font-mono tracking-widest">00:00:00</span>
            </div>
        </div>
    </header>

    {{-- ═══════════════════ KARTU RINGKASAN HARI INI ═══════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-fluid">
        {{-- Muzakki Total --}}
        <div class="bg-surface p-fluid rounded-fluid shadow-card border border-slate-100 relative overflow-hidden group">
            <div class="absolute -top-2 -right-2 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined icon-fluid-xl text-secondary">group</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-[clamp(.5rem,.8vw,1.2rem)] mb-[clamp(.5rem,.6vw,1rem)]">
                    <div class="bg-slate-50 rounded-[clamp(.5rem,.7vw,1rem)] p-[clamp(.5rem,.7vw,1rem)]">
                        <span class="material-symbols-outlined text-secondary icon-fluid">person</span>
                    </div>
                    <p class="text-fluid-xs text-text-muted font-bold uppercase tracking-wider">Muzakki Total</p>
                </div>
                <p class="text-fluid-5xl font-bold text-slate-800 mt-[clamp(.5rem,.8vw,1rem)]">
                    <span id="today-muzakki">—</span>
                    <span class="text-fluid-lg font-medium text-slate-400"> Orang</span>
                </p>
            </div>
        </div>

        {{-- Beras Total --}}
        <div class="bg-surface p-fluid rounded-fluid shadow-card border border-slate-100 relative overflow-hidden group">
            <div class="absolute -top-2 -right-2 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined icon-fluid-xl text-primary">grain</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-[clamp(.5rem,.8vw,1.2rem)] mb-[clamp(.5rem,.6vw,1rem)]">
                    <div class="bg-slate-50 rounded-[clamp(.5rem,.7vw,1rem)] p-[clamp(.5rem,.7vw,1rem)]">
                        <span class="material-symbols-outlined text-primary icon-fluid">grain</span>
                    </div>
                    <p class="text-fluid-xs text-text-muted font-bold uppercase tracking-wider">Beras Terkumpul Total</p>
                </div>
                <p class="text-fluid-5xl font-bold text-secondary mt-[clamp(.5rem,.8vw,1rem)]">
                    <span id="today-rice">—</span>
                    <span class="text-fluid-lg font-medium text-slate-400"> kg</span>
                </p>
            </div>
        </div>

        {{-- Uang Total --}}
        <div class="bg-surface p-fluid rounded-fluid shadow-card border border-slate-100 relative overflow-hidden group">
            <div class="absolute -top-2 -right-2 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined icon-fluid-xl text-emerald-600">payments</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-[clamp(.5rem,.8vw,1.2rem)] mb-[clamp(.5rem,.6vw,1rem)]">
                    <div class="bg-slate-50 rounded-[clamp(.5rem,.7vw,1rem)] p-[clamp(.5rem,.7vw,1rem)]">
                        <span class="material-symbols-outlined text-emerald-600 icon-fluid">payments</span>
                    </div>
                    <p class="text-fluid-xs text-text-muted font-bold uppercase tracking-wider">Total Dana Terkumpul</p>
                </div>
                <p id="today-money" class="text-fluid-4xl font-bold text-slate-800 mt-[clamp(.5rem,.8vw,1rem)] tracking-tight">—</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ MAIN CONTENT GRID ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-fluid lg:flex-1 lg:min-h-0">

        {{-- ─── Kolom Kiri: Stok + Chart ─── --}}
        <div class="lg:col-span-4 flex flex-col gap-fluid lg:min-h-0">

            {{-- Stok Beras --}}
            <div class="bg-surface p-fluid rounded-fluid shadow-card border border-slate-100 flex flex-col">
                <div class="flex justify-between items-start mb-[clamp(.75rem,1vw,1.5rem)]">
                    <div>
                        <h3 class="text-fluid-lg font-bold text-slate-800">Status Stok Beras</h3>
                        <p class="text-fluid-sm text-text-muted mt-1 font-medium">Total beras: <span id="stock-total">—</span> kg</p>
                    </div>
                    <div class="bg-slate-50 px-[clamp(.75rem,1vw,1.5rem)] py-[clamp(.3rem,.5vw,.75rem)] rounded-[clamp(.5rem,.7vw,1rem)] border border-slate-100">
                        <span id="stock-pct-label" class="text-secondary text-fluid-sm font-bold">—</span>
                    </div>
                </div>
                {{-- Progress bar --}}
                <div class="relative w-full bg-slate-100 rounded-full overflow-hidden border border-slate-200 shadow-inner mb-[clamp(.75rem,1vw,1.5rem)]"
                     style="height:clamp(2rem,2.5vw,3.5rem)">
                    <div id="stock-bar"
                         class="absolute top-0 left-0 h-full bg-gradient-to-r from-primary/80 to-primary rounded-full flex items-center justify-end transition-all duration-700"
                         style="width:0%;padding-right:clamp(.75rem,1.2vw,2rem)">
                        <span id="stock-bar-label" class="text-white font-bold text-fluid-sm drop-shadow-sm whitespace-nowrap">0 kg</span>
                    </div>
                </div>
                <div class="flex items-center justify-between bg-slate-50 rounded-[clamp(.75rem,1vw,1.25rem)] border border-slate-100 mt-auto"
                     style="padding:clamp(.75rem,1vw,1.5rem)">
                    <div class="flex items-center gap-[clamp(.75rem,1vw,1.5rem)]">
                        <div class="bg-white rounded-[clamp(.5rem,.7vw,1rem)] text-secondary shadow-sm"
                             style="padding:clamp(.5rem,.7vw,1rem)">
                            <span class="material-symbols-outlined icon-fluid">sell</span>
                        </div>
                        <div>
                            <p class="text-fluid-xs text-text-muted uppercase font-bold tracking-wider">Harga Beras per 3kg</p>
                            <p id="stock-price" class="text-slate-800 font-bold text-fluid-lg">—</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Tren 7 Hari --}}
            <div class="hidden bg-surface p-fluid rounded-fluid shadow-card border border-slate-100 flex-1 flex-col lg:min-h-0">
                <div class="flex justify-between items-center mb-[clamp(.75rem,1vw,1.5rem)]">
                    <h3 class="text-fluid-lg font-bold text-slate-800">Tren Beras (7 Hari)</h3>
                    <span class="text-fluid-xs font-bold text-primary bg-primary/10 px-[clamp(.4rem,.6vw,1rem)] py-[clamp(.25rem,.4vw,.6rem)] rounded">kg / hari</span>
                </div>
                <div id="chart-container" class="flex-1 flex items-end justify-between gap-fluid min-h-0">
                    {{-- Bars rendered by JS --}}
                </div>
            </div>

            {{-- Live Feed Transaksi --}}
            <div class="bg-surface rounded-fluid shadow-card border border-slate-100 p-fluid flex flex-col flex-1 lg:min-h-0">
                <div class="flex items-center gap-[clamp(.5rem,.7vw,1rem)] mb-[clamp(.5rem,.8vw,1.25rem)]">
                    <span class="live-dot dot-fluid rounded-full bg-red-500 inline-block shrink-0"></span>
                    <h3 class="text-fluid-base font-bold text-slate-800">Transaksi Terakhir</h3>
                </div>
                <div id="live-feed" class="space-y-[clamp(.5rem,.7vw,1rem)] text-fluid-sm overflow-y-auto flex-1 min-h-0">
                    <div class="text-text-muted text-center">Memuat…</div>
                </div>
            </div>

        </div>

        {{-- ─── Kolom Tengah: Tabel Rekapitulasi ─── --}}
        <div class="lg:col-span-5 bg-surface rounded-fluid shadow-card border border-slate-100 overflow-hidden flex flex-col lg:min-h-0">
            <div class="border-b border-slate-100 bg-slate-50 flex justify-between items-center"
                 style="padding:clamp(.75rem,1.2vw,1.75rem)">
                <div>
                    <h3 class="text-fluid-lg font-bold text-slate-800">Rekapitulasi Keseluruhan</h3>
                    <p class="text-fluid-sm text-text-muted mt-1">Total muzakki: <span id="overall-muzakki" class="font-semibold text-primary">—</span> orang</p>
                </div>
                <div class="text-right">
                    <p class="text-fluid-xs text-text-muted uppercase font-bold tracking-wider">Total Beras</p>
                    <p id="overall-rice" class="text-fluid-lg font-bold text-primary">— kg</p>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto min-h-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="text-fluid-xs font-bold text-text-muted uppercase tracking-wider bg-slate-50/70"
                                style="padding:clamp(.75rem,1.1vw,1.5rem)">Kategori</th>
                            <th class="text-fluid-xs font-bold text-text-muted uppercase tracking-wider text-right bg-slate-50/70"
                                style="padding:clamp(.75rem,1.1vw,1.5rem)">Muzakki</th>
                            <th class="text-fluid-xs font-bold text-text-muted uppercase tracking-wider text-right bg-slate-50/70"
                                style="padding:clamp(.75rem,1.1vw,1.5rem)">Total</th>
                        </tr>
                    </thead>
                    <tbody id="breakdown-table" class="divide-y divide-slate-100">
                        <tr><td colspan="3" class="p-4 text-center text-text-muted text-fluid-sm">Memuat data…</td></tr>
                    </tbody>
                </table>
            </div>
            {{-- Total Uang --}}
            <div class="border-t border-slate-100 bg-slate-50 flex items-center justify-between"
                 style="padding:clamp(.75rem,1.2vw,1.75rem)">
                <span class="text-fluid-xs font-bold text-text-muted uppercase tracking-wider">Total Dana Terhimpun</span>
                <span id="overall-money" class="text-fluid-xl font-bold text-primary">—</span>
            </div>
        </div>

        {{-- ─── Kolom Kanan: Penerima Manfaat + Live Feed ─── --}}
        <div class="lg:col-span-3 flex flex-col gap-fluid lg:min-h-0">

            {{-- Estimasi Penerima Manfaat --}}
            <div class="bg-gradient-to-br from-primary to-[#1b4332] rounded-fluid shadow-xl shadow-primary/20 p-fluid flex flex-col justify-center items-center text-center relative overflow-hidden lg:flex-1 lg:min-h-0">
                <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image:radial-gradient(circle at 2px 2px,white 1px,transparent 0);background-size:24px 24px;"></div>
                <div class="relative z-10 w-full flex flex-col items-center">
                    <div class="bg-white/10 rounded-full flex items-center justify-center mx-auto backdrop-blur-md ring-1 ring-white/20 shadow-lg mb-[clamp(.75rem,1.2vw,2rem)]"
                         style="width:clamp(3.5rem,5.5vw,8rem);height:clamp(3.5rem,5.5vw,8rem)">
                        <span class="material-symbols-outlined text-white icon-fluid-lg">volunteer_activism</span>
                    </div>
                    <h3 class="text-white/80 text-fluid-xs font-bold uppercase tracking-wider mb-2">Estimasi Penerima Manfaat</h3>
                    <p id="beneficiaries" class="text-white text-fluid-6xl font-bold mb-2 tracking-tight">—</p>
                    <p class="text-secondary text-fluid-base font-medium mb-1">Jiwa dapat disantuni</p>
                    <p id="beneficiary-kg-label" class="text-white/60 text-fluid-xs font-medium mb-[clamp(1rem,1.8vw,3rem)]">Berdasarkan alokasi — kg per orang</p>
                    <div class="w-full h-px bg-white/20 mb-[clamp(.75rem,1.2vw,2rem)]"></div>
                    <div class="grid grid-cols-2 w-full gap-[clamp(.5rem,.8vw,1.25rem)]">
                        <div class="bg-black/20 rounded-[clamp(.75rem,1.1vw,1.5rem)] border border-white/5"
                             style="padding:clamp(.75rem,1.1vw,1.75rem)">
                            <p class="text-fluid-xs text-white/70 mb-1 uppercase tracking-wide font-semibold">Beras Total</p>
                            <p id="overall-rice-small" class="text-fluid-2xl font-bold text-white">— kg</p>
                        </div>
                        <div class="bg-black/20 rounded-[clamp(.75rem,1.1vw,1.5rem)] border border-white/5"
                             style="padding:clamp(.75rem,1.1vw,1.75rem)">
                            <p class="text-fluid-xs text-white/70 mb-1 uppercase tracking-wide font-semibold">Muzakki</p>
                            <p id="overall-muzakki-small" class="text-fluid-2xl font-bold text-white">—</p>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

</div>

{{-- ═══════════════════ FOOTER MARQUEE ═══════════════════ --}}
<footer class="bg-white border-t border-slate-200 flex items-center overflow-hidden z-30 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.04)] shrink-0"
        style="height:clamp(3rem,4.5vw,6rem)">
    <div class="bg-primary h-full flex items-center shrink-0 shadow-lg"
         style="padding:0 clamp(1rem,1.8vw,2.8rem)">
        <span class="text-white font-bold uppercase tracking-wider text-fluid-sm flex items-center gap-[clamp(.5rem,.7vw,1rem)]">
            <span class="material-symbols-outlined icon-fluid animate-pulse">favorite</span>
            Jazakallah
        </span>
    </div>
    <div class="marquee-wrap flex-1 bg-slate-50 h-full flex items-center">
        <div class="marquee-inner flex gap-[clamp(2rem,4vw,6rem)] items-center px-[clamp(.75rem,1.2vw,2rem)]" id="marquee-content">
            <span class="font-bold text-slate-700 text-fluid-base">Terima kasih kepada para Muzakki hari ini…</span>
        </div>
    </div>
</footer>

<script>
// ═══════════════════════════════════════════════
//  UTILITIES
// ═══════════════════════════════════════════════
const $ = id => document.getElementById(id);
const setText = (id, val) => { const el = $(id); if(el) el.textContent = val; };

// ═══════════════════════════════════════════════
//  CLOCK & DATE
// ═══════════════════════════════════════════════
function updateClock() {
    const now = new Date();

    // Jam
    const hh = String(now.getHours()).padStart(2,'0');
    const mm = String(now.getMinutes()).padStart(2,'0');
    const ss = String(now.getSeconds()).padStart(2,'0');
    setText('clock', `${hh}:${mm}:${ss}`);

    // Masehi
    const masehi = now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    setText('date-masehi', masehi);

    // Hijriah (Intl API)
    try {
        const hijriah = now.toLocaleDateString('id-ID-u-ca-islamic-umalqura', { day:'numeric', month:'long', year:'numeric' });
        setText('date-hijriah', hijriah + ' H');
    } catch(e) {
        setText('date-hijriah', '—');
    }
}
setInterval(updateClock, 1000);
updateClock();

// ═══════════════════════════════════════════════
//  COLOR MAP
// ═══════════════════════════════════════════════
const COLOR_MAP = {
    primary:   { dot: 'bg-[#2d6a4f]', ring: 'ring-[#2d6a4f]/20' },
    secondary: { dot: 'bg-[#d4a017]', ring: 'ring-[#d4a017]/20' },
    orange:    { dot: 'bg-orange-500', ring: 'ring-orange-500/20' },
    blue:      { dot: 'bg-blue-500',   ring: 'ring-blue-500/20'  },
    purple:    { dot: 'bg-purple-500', ring: 'ring-purple-500/20'},
};

// ═══════════════════════════════════════════════
//  RENDER FUNCTIONS
// ═══════════════════════════════════════════════
function renderToday(today) {
    setText('today-muzakki', today.muzakki.toLocaleString('id-ID'));
    setText('today-rice', today.total_rice_kg.toLocaleString('id-ID', {minimumFractionDigits:1}));
    setText('today-money', today.total_money_fmt);
}

function renderStock(stock) {
    const avail   = stock.available_kg ?? 0;
    const total   = stock.total_purchased_kg ?? 0;
    const pct     = total > 0 ? Math.min(100, Math.round((avail / total) * 100)) : 0;

    setText('stock-total', total.toLocaleString('id-ID', {minimumFractionDigits:1}));
    setText('stock-pct-label', pct + '% Tersedia');
    setText('stock-bar-label', avail.toLocaleString('id-ID', {minimumFractionDigits:1}) + ' kg');
    setText('stock-price', stock.price_per_pkg_fmt ?? '—');

    const bar = $('stock-bar');
    if (bar) {
        bar.style.width = pct + '%';
        // Turn red when low stock (<20%)
        if (pct < 20) {
            bar.classList.remove('from-primary/80','to-primary');
            bar.classList.add('from-red-600/80','to-red-600');
        }
    }
}

function renderOverall(overall) {
    setText('overall-muzakki', overall.muzakki.toLocaleString('id-ID'));
    setText('overall-muzakki-small', overall.muzakki.toLocaleString('id-ID'));
    setText('overall-rice', overall.total_rice_kg.toLocaleString('id-ID', {minimumFractionDigits:1}) + ' kg');
    setText('overall-rice-small', overall.total_rice_kg.toLocaleString('id-ID', {minimumFractionDigits:1}) + ' kg');
    setText('overall-money', overall.total_money_fmt);
    setText('beneficiaries', overall.estimated_beneficiaries.toLocaleString('id-ID'));

    // Per-person kg label
    if (overall.beneficiary_rice_kg != null) {
        const kg = parseFloat(overall.beneficiary_rice_kg);
        const kgFmt = Number.isInteger(kg) ? kg.toString() : kg.toLocaleString('id-ID');
        setText('beneficiary-kg-label', 'Berdasarkan alokasi ' + kgFmt + ' kg per orang');
    }

    // Breakdown table
    const tbody = $('breakdown-table');
    if (!tbody) return;
    const rows = Object.values(overall.breakdown).map(cat => {
        const c = COLOR_MAP[cat.color] || COLOR_MAP.primary;
        const valDisplay = cat.unit === 'kg'
            ? cat.value.toLocaleString('id-ID', {minimumFractionDigits:1}) + ' kg'
            : (cat.value_fmt ?? '—');
        return `<tr class="hover:bg-slate-50 transition-colors group fade-in">
            <td class="font-semibold text-slate-700 flex items-center gap-[clamp(.5rem,.8vw,1.25rem)] text-fluid-base" style="padding:clamp(.75rem,1.1vw,1.5rem)">
                <div class="dot-fluid rounded-full shrink-0 ${c.dot} shadow-sm ring-2 ${c.ring}"></div>
                ${cat.label}
            </td>
            <td class="text-slate-500 text-right font-medium text-fluid-base" style="padding:clamp(.75rem,1.1vw,1.5rem)">${cat.muzakki.toLocaleString('id-ID')}</td>
            <td class="text-secondary font-bold text-right text-fluid-base" style="padding:clamp(.75rem,1.1vw,1.5rem)">${valDisplay}</td>
        </tr>`;
    });
    tbody.innerHTML = rows.join('');
}

function renderChart(chartData) {
    const container = $('chart-container');
    if (!container || !chartData.length) return;

    const max = Math.max(...chartData.map(d => d.total_rice), 1);

    container.innerHTML = chartData.map((d, i) => {
        const pct = Math.round((d.total_rice / max) * 100);
        const isToday = i === chartData.length - 1;
        const barColor = isToday ? 'bg-primary/90 group-hover:bg-primary' : 'bg-secondary/80 group-hover:bg-secondary';
        return `<div class="flex flex-col items-center flex-1 group" style="gap:clamp(.3rem,.5vw,.75rem)">
            <span class="text-fluid-xs text-text-muted font-semibold">${d.total_rice > 0 ? d.total_rice : ''}</span>
            <div class="w-full bg-slate-100 rounded-t-md relative flex-1 overflow-hidden" style="min-height:8px">
                <div class="absolute bottom-0 left-0 w-full ${barColor} rounded-t-md transition-all duration-700 bar-col" style="height:${pct}%"></div>
            </div>
            <span class="text-fluid-xs font-bold ${isToday ? 'text-primary bg-primary/10 px-1 rounded' : 'text-text-muted'}">${d.day}</span>
        </div>`;
    }).join('');
}

function renderLiveFeed(transactions) {
    const feed = $('live-feed');
    if (!feed) return;
    if (!transactions || transactions.length === 0) {
        feed.innerHTML = '<p class="text-text-muted text-center text-fluid-sm">Belum ada transaksi hari ini.</p>';
        return;
    }
    feed.innerHTML = transactions.map(t => `
        <div class="flex items-center bg-slate-50 rounded-[clamp(.5rem,.8vw,1.25rem)] fade-in"
             style="gap:clamp(.5rem,.8vw,1.25rem);padding:clamp(.5rem,.8vw,1.25rem)">
            <div class="rounded-full bg-primary/10 flex items-center justify-center shrink-0"
                 style="width:clamp(1.5rem,2.5vw,3.5rem);height:clamp(1.5rem,2.5vw,3.5rem)">
                <span class="material-symbols-outlined text-primary icon-fluid">person</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-700 truncate text-fluid-base">${t.name}</p>
                <p class="text-fluid-xs text-text-muted truncate">${t.types}</p>
            </div>
            <span class="text-fluid-xs text-text-muted font-mono shrink-0">${t.created_at ?? ''}</span>
        </div>
    `).join('');
}

function renderMarquee(marqueeData) {
    const el = $('marquee-content');
    if (!el) return;
    if (!marqueeData || marqueeData.length === 0) {
        el.innerHTML = '<span class="font-bold text-slate-700 text-fluid-base">Belum ada muzakki hari ini…</span>';
        return;
    }
    const items = marqueeData.map(d => {
        let badges = [];
        if (d.quantity > 0) {
            let unit = (d.type.includes('Beras') || d.type.includes('Masjid') || d.type === 'Zakat Fitrah') ? ' kg Beras' : ' kg Beras';
            badges.push(`<span class="bg-amber-100 text-amber-700 px-[clamp(.5rem,.8vw,1.25rem)] py-[clamp(.2rem,.3vw,.5rem)] rounded text-fluid-sm font-bold">${d.quantity}${unit}</span>`);
        }
        if (d.amount > 0) {
            const rp = d.amount.toLocaleString('id-ID');
            badges.push(`<span class="bg-green-100 text-green-700 px-[clamp(.5rem,.8vw,1.25rem)] py-[clamp(.2rem,.3vw,.5rem)] rounded text-fluid-sm font-bold">Rp ${rp}</span>`);
        }

        let badgeHtml = badges.length > 0
            ? badges.join('')
            : `<span class="bg-slate-200 text-slate-600 px-[clamp(.5rem,.8vw,1.25rem)] py-[clamp(.2rem,.3vw,.5rem)] rounded text-fluid-sm font-bold">${d.created_at ?? ''}</span>`;

        return `<div class="flex items-center shrink-0" style="gap:clamp(.75rem,1.2vw,2rem)">
            <span class="text-slate-500 text-fluid-base font-medium">${d.name} <span class="text-slate-400 text-fluid-sm">(${d.type})</span></span>
            <div class="flex items-center" style="gap:clamp(.4rem,.6vw,1rem)">${badgeHtml}</div>
        </div>`;
    }).join('<span class="text-slate-300 shrink-0" style="margin:0 clamp(.75rem,1.2vw,2rem)">•</span>');
    // Duplicate for seamless loop
    el.innerHTML = items + '<span class="inline-block w-24"></span>' + items;
}

// ═══════════════════════════════════════════════
//  DATA FETCH & REFRESH
// ═══════════════════════════════════════════════
async function fetchData() {
    try {
        const res  = await fetch('{{ route("zakat-live.data") }}');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();

        renderToday(json.today);
        renderStock(json.stock);
        renderOverall(json.overall);
        renderChart(json.overall.chart_data);
        renderLiveFeed(json.today.recent_transactions);
        renderMarquee(json.marquee);

    } catch (err) {
        console.error('[ZakatLive] Fetch error:', err);
    }
}

// Initial load + auto-refresh every 30s
fetchData();
setInterval(fetchData, 30_000);
</script>
</body>
</html>
