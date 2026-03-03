<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Zakat Live — Masjid Al Munawwar</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
                },
            },
        };
    </script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .marquee-wrap { overflow: hidden; white-space: nowrap; }
        .marquee-inner { display: inline-block; animation: marquee 50s linear infinite; }
        @keyframes marquee {
            0%   { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }

        /* Bar chart */
        .bar-col { transition: height .5s ease; }

        /* Fade-in on data refresh */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
        .fade-in { animation: fadeIn .4s ease both; }

        /* Live dot pulse */
        @keyframes livePulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .35; }
        }
        .live-dot { animation: livePulse 1.5s ease-in-out infinite; }
    </style>
</head>
<body class="bg-bg-light text-text-dark font-display min-h-screen flex flex-col overflow-y-auto lg:overflow-hidden selection:bg-primary/20 selection:text-primary">

<div class="flex-1 flex flex-col p-4 md:p-5 gap-5">

    {{-- ═══════════════════ HEADER ═══════════════════ --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex items-center gap-4">
            <div class="size-14 bg-white rounded-full flex items-center justify-center text-primary border border-slate-100 shadow-soft">
                <span class="material-symbols-outlined text-4xl">mosque</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-800">Masjid Al Munawwar</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="live-dot size-2 rounded-full bg-green-500 inline-block"></span>
                    <p class="text-text-muted text-xs font-semibold uppercase tracking-wider">Dashboard Zakat Real-Time</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 sm:gap-3">
            {{-- Masehi --}}
            <div class="hidden sm:flex items-center gap-2.5 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-soft">
                <span class="material-symbols-outlined text-secondary text-2xl">calendar_month</span>
                <div class="flex flex-col text-right">
                    <span class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Masehi</span>
                    <span id="date-masehi" class="text-sm font-bold text-slate-700">—</span>
                </div>
            </div>
            {{-- Hijriah --}}
            <div class="hidden sm:flex items-center gap-2.5 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-soft">
                <span class="material-symbols-outlined text-primary text-2xl">nights_stay</span>
                <div class="flex flex-col text-right">
                    <span class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Hijriah</span>
                    <span id="date-hijriah" class="text-sm font-bold text-secondary">—</span>
                </div>
            </div>
            {{-- Jam --}}
            <div class="flex items-center gap-2 bg-gradient-to-br from-primary to-[#1b4332] px-5 py-2 rounded-xl shadow-lg shadow-primary/20 text-white">
                <span class="material-symbols-outlined text-xl">schedule</span>
                <span id="clock" class="text-2xl font-bold font-mono tracking-widest">00:00:00</span>
            </div>
        </div>
    </header>

    {{-- ═══════════════════ KARTU RINGKASAN HARI INI ═══════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        {{-- Muzakki Hari Ini --}}
        <div class="bg-surface p-5 rounded-2xl shadow-card border border-slate-100 relative overflow-hidden group">
            <div class="absolute -top-2 -right-2 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-9xl text-secondary">group</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 bg-slate-50 rounded-lg">
                        <span class="material-symbols-outlined text-secondary">person</span>
                    </div>
                    <p class="text-text-muted text-xs font-bold uppercase tracking-wider">Muzakki Hari Ini</p>
                </div>
                <p class="text-5xl font-bold text-slate-800 mt-3">
                    <span id="today-muzakki">—</span>
                    <span class="text-lg font-medium text-slate-400"> Orang</span>
                </p>
            </div>
        </div>

        {{-- Beras Hari Ini --}}
        <div class="bg-surface p-5 rounded-2xl shadow-card border border-slate-100 relative overflow-hidden group">
            <div class="absolute -top-2 -right-2 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-9xl text-primary">grain</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 bg-slate-50 rounded-lg">
                        <span class="material-symbols-outlined text-primary">grain</span>
                    </div>
                    <p class="text-text-muted text-xs font-bold uppercase tracking-wider">Beras Terkumpul Hari Ini</p>
                </div>
                <p class="text-5xl font-bold text-secondary mt-3">
                    <span id="today-rice">—</span>
                    <span class="text-lg font-medium text-slate-400"> kg</span>
                </p>
            </div>
        </div>

        {{-- Uang Hari Ini --}}
        <div class="bg-surface p-5 rounded-2xl shadow-card border border-slate-100 relative overflow-hidden group">
            <div class="absolute -top-2 -right-2 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <span class="material-symbols-outlined text-9xl text-emerald-600">payments</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 bg-slate-50 rounded-lg">
                        <span class="material-symbols-outlined text-emerald-600">payments</span>
                    </div>
                    <p class="text-text-muted text-xs font-bold uppercase tracking-wider">Total Dana Hari Ini</p>
                </div>
                <p id="today-money" class="text-4xl font-bold text-slate-800 mt-3 tracking-tight">—</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════ MAIN CONTENT GRID ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:flex-1 lg:min-h-0">

        {{-- ─── Kolom Kiri: Stok + Chart ─── --}}
        <div class="lg:col-span-4 flex flex-col gap-5">

            {{-- Stok Beras --}}
            <div class="bg-surface p-5 rounded-2xl shadow-card border border-slate-100 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Status Stok Beras</h3>
                        <p class="text-xs text-text-muted mt-0.5 font-medium">Total pembelian: <span id="stock-total">—</span> kg</p>
                    </div>
                    <div class="bg-slate-50 px-3 py-1 rounded-lg border border-slate-100">
                        <span id="stock-pct-label" class="text-secondary text-xs font-bold">—</span>
                    </div>
                </div>
                <div class="relative h-12 w-full bg-slate-100 rounded-full overflow-hidden border border-slate-200 shadow-inner mb-4">
                    <div id="stock-bar" class="absolute top-0 left-0 h-full bg-gradient-to-r from-primary/80 to-primary rounded-full flex items-center justify-end px-4 transition-all duration-700" style="width:0%">
                        <span id="stock-bar-label" class="text-white font-bold text-sm drop-shadow-sm whitespace-nowrap">0 kg</span>
                    </div>
                </div>
                <div class="flex items-center justify-between bg-slate-50 p-3.5 rounded-xl border border-slate-100 mt-auto">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white rounded-lg text-secondary shadow-sm">
                            <span class="material-symbols-outlined text-lg">sell</span>
                        </div>
                        <div>
                            <p class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Harga Beras / Porsi</p>
                            <p id="stock-price" class="text-slate-800 font-bold text-base">—</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Tren 7 Hari --}}
            <div class="bg-surface p-5 rounded-2xl shadow-card border border-slate-100 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-slate-800">Tren Beras (7 Hari)</h3>
                    <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-1 rounded">kg / hari</span>
                </div>
                <div id="chart-container" class="flex-1 flex items-end justify-between gap-2 h-32">
                    {{-- Bars rendered by JS --}}
                </div>
            </div>

        </div>

        {{-- ─── Kolom Tengah: Tabel Rekapitulasi ─── --}}
        <div class="lg:col-span-5 bg-surface rounded-2xl shadow-card border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Rekapitulasi Keseluruhan</h3>
                    <p class="text-xs text-text-muted mt-0.5">Total muzakki: <span id="overall-muzakki" class="font-semibold text-primary">—</span> orang</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-text-muted uppercase font-bold tracking-wider">Total Beras</p>
                    <p id="overall-rice" class="text-sm font-bold text-primary">— kg</p>
                </div>
            </div>
            <div class="flex-1 overflow-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="p-3.5 text-[10px] font-bold text-text-muted uppercase tracking-wider bg-slate-50/70">Kategori</th>
                            <th class="p-3.5 text-[10px] font-bold text-text-muted uppercase tracking-wider text-right bg-slate-50/70">Muzakki</th>
                            <th class="p-3.5 text-[10px] font-bold text-text-muted uppercase tracking-wider text-right bg-slate-50/70">Total</th>
                        </tr>
                    </thead>
                    <tbody id="breakdown-table" class="divide-y divide-slate-100 text-sm">
                        <tr><td colspan="3" class="p-4 text-center text-text-muted text-xs">Memuat data…</td></tr>
                    </tbody>
                </table>
            </div>
            {{-- Total Uang --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Total Dana Terhimpun</span>
                <span id="overall-money" class="text-lg font-bold text-primary">—</span>
            </div>
        </div>

        {{-- ─── Kolom Kanan: Penerima Manfaat + Live Feed ─── --}}
        <div class="lg:col-span-3 flex flex-col gap-5">

            {{-- Estimasi Penerima Manfaat --}}
            <div class="bg-gradient-to-br from-primary to-[#1b4332] rounded-2xl shadow-xl shadow-primary/20 p-5 flex flex-col justify-center items-center text-center relative overflow-hidden lg:flex-1">
                <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image:radial-gradient(circle at 2px 2px,white 1px,transparent 0);background-size:24px 24px;"></div>
                <div class="relative z-10 w-full flex flex-col items-center">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-md ring-1 ring-white/20 shadow-lg">
                        <span class="material-symbols-outlined text-4xl text-white">volunteer_activism</span>
                    </div>
                    <h3 class="text-white/80 text-[10px] font-bold uppercase tracking-wider mb-1">Estimasi Penerima Manfaat</h3>
                    <p id="beneficiaries" class="text-white text-5xl font-bold mb-1 tracking-tight">—</p>
                    <p class="text-secondary text-sm font-medium mb-1">Jiwa dapat disantuni</p>
                    <p id="beneficiary-kg-label" class="text-white/60 text-xs font-medium mb-6">Berdasarkan alokasi — kg per orang</p>
                    <div class="w-full h-px bg-white/20 mb-4"></div>
                    <div class="grid grid-cols-2 gap-3 w-full">
                        <div class="bg-black/20 p-3 rounded-xl border border-white/5">
                            <p class="text-[10px] text-white/70 mb-1 uppercase tracking-wide font-semibold">Beras Total</p>
                            <p id="overall-rice-small" class="text-xl font-bold text-white">— kg</p>
                        </div>
                        <div class="bg-black/20 p-3 rounded-xl border border-white/5">
                            <p class="text-[10px] text-white/70 mb-1 uppercase tracking-wide font-semibold">Muzakki</p>
                            <p id="overall-muzakki-small" class="text-xl font-bold text-white">—</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Feed Transaksi --}}
            <div class="bg-surface rounded-2xl shadow-card border border-slate-100 p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-3">
                    <span class="live-dot size-2 rounded-full bg-red-500 inline-block"></span>
                    <h3 class="text-sm font-bold text-slate-800">Transaksi Terakhir</h3>
                </div>
                <div id="live-feed" class="space-y-2 text-xs">
                    <div class="text-text-muted text-center">Memuat…</div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ═══════════════════ FOOTER MARQUEE ═══════════════════ --}}
<footer class="bg-white border-t border-slate-200 h-14 flex items-center overflow-hidden z-30 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.04)]">
    <div class="bg-primary px-6 h-full flex items-center shrink-0 shadow-lg">
        <span class="text-white font-bold uppercase tracking-wider text-xs flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base animate-pulse">favorite</span>
            Jazakallah
        </span>
    </div>
    <div class="marquee-wrap flex-1 py-2 bg-slate-50 h-full flex items-center">
        <div class="marquee-inner flex gap-12 items-center px-4" id="marquee-content">
            <span class="font-bold text-slate-700 text-sm">Terima kasih kepada para Muzakki hari ini…</span>
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
            <td class="p-3.5 font-semibold text-slate-700 flex items-center gap-3">
                <div class="size-2.5 rounded-full ${c.dot} shadow-sm ring-2 ${c.ring}"></div>
                ${cat.label}
            </td>
            <td class="p-3.5 text-slate-500 text-right font-medium">${cat.muzakki.toLocaleString('id-ID')}</td>
            <td class="p-3.5 text-secondary font-bold text-right">${valDisplay}</td>
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
        return `<div class="flex flex-col items-center gap-1.5 flex-1 group">
            <span class="text-[9px] text-text-muted font-semibold">${d.total_rice > 0 ? d.total_rice : ''}</span>
            <div class="w-full bg-slate-100 rounded-t-md relative flex-1 overflow-hidden" style="min-height:8px">
                <div class="absolute bottom-0 left-0 w-full ${barColor} rounded-t-md transition-all duration-700 bar-col" style="height:${pct}%"></div>
            </div>
            <span class="text-[10px] ${isToday ? 'text-primary font-bold bg-primary/10 px-1 rounded' : 'text-text-muted font-bold'}">${d.day}</span>
        </div>`;
    }).join('');
}

function renderLiveFeed(transactions) {
    const feed = $('live-feed');
    if (!feed) return;
    if (!transactions || transactions.length === 0) {
        feed.innerHTML = '<p class="text-text-muted text-center">Belum ada transaksi hari ini.</p>';
        return;
    }
    feed.innerHTML = transactions.map(t => `
        <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-2 fade-in">
            <div class="size-6 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary" style="font-size:14px">person</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-700 truncate">${t.name}</p>
                <p class="text-[10px] text-text-muted truncate">${t.types}</p>
            </div>
            <span class="text-[10px] text-text-muted font-mono shrink-0">${t.created_at ?? ''}</span>
        </div>
    `).join('');
}

function renderMarquee(marqueeData) {
    const el = $('marquee-content');
    if (!el) return;
    if (!marqueeData || marqueeData.length === 0) {
        el.innerHTML = '<span class="font-bold text-slate-700 text-sm">Belum ada muzakki hari ini…</span>';
        return;
    }
    const items = marqueeData.map(d => {
        let badges = [];
        if (d.quantity > 0) {
            let unit = (d.type.includes('Beras') || d.type.includes('Masjid') || d.type === 'Zakat Fitrah') ? ' kg Beras' : ' kg Beras';
            badges.push(`<span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-sm font-bold">${d.quantity}${unit}</span>`);
        }
        if (d.amount > 0) {
            const rp = d.amount.toLocaleString('id-ID');
            badges.push(`<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-sm font-bold">Rp ${rp}</span>`);
        }

        let badgeHtml = badges.length > 0 
            ? badges.join('') 
            : `<span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded text-sm font-bold">${d.created_at ?? ''}</span>`;

        return `<div class="flex items-center gap-3 shrink-0">
            <span class="text-slate-500 text-sm font-medium">${d.name} <span class="text-slate-400 text-xs">(${d.type})</span></span>
            <div class="flex items-center gap-1.5">${badgeHtml}</div>
        </div>`;
    }).join('<span class="text-slate-300 shrink-0 mx-2">•</span>');
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
