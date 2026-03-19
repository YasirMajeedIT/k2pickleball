<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — K2 Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844', 700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4' },
                    gold: { 300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d', 700:'#9c7c24', 800:'#7d6420' }
                },
                fontFamily: { display: ['Plus Jakarta Sans', 'sans-serif'], body: ['Inter', 'sans-serif'] }
            }
        }
    }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-gold { background: linear-gradient(135deg, #f0d878, #d4af37, #b8952d); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-gold-bg { background: linear-gradient(135deg, #f0d878, #d4af37, #b8952d); }
        .grid-bg { background-image: radial-gradient(rgba(212,175,55,0.08) 1px, transparent 1px); background-size: 32px 32px; }
    </style>
</head>
<body class="bg-navy-950 text-white min-h-screen flex items-center justify-center px-6 py-12">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[300px] bg-gold-500/5 rounded-full blur-[120px]"></div>

    <div class="relative w-full max-w-md" x-data="{ loading: false, sent: false }">
        <!-- Logo -->
        <div class="flex items-center justify-center gap-2 mb-10">
            <svg class="w-8 h-8 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
            </svg>
            <span class="font-display text-xl font-extrabold">K2 <span class="gradient-gold">Platform</span></span>
        </div>

        <!-- Pre-send state -->
        <div x-show="!sent">
            <h1 class="font-display text-3xl font-extrabold text-white text-center">Forgot Password?</h1>
            <p class="mt-2 text-sm text-slate-400 text-center">Enter your email address and we'll send you a link to reset your password.</p>

            <form class="mt-8 space-y-5" @submit.prevent="loading = true; setTimeout(() => { loading = false; sent = true; }, 1500)">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" required autocomplete="email" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="you@example.com">
                </div>

                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300 disabled:opacity-70">
                    <span x-show="!loading">Send Reset Link</span>
                    <span x-show="loading" x-cloak>Sending...</span>
                </button>
            </form>
        </div>

        <!-- Post-send state -->
        <div x-show="sent" x-cloak class="text-center">
            <div class="h-16 w-16 rounded-full bg-gold-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>
            <h1 class="font-display text-3xl font-extrabold text-white">Check Your Email</h1>
            <p class="mt-3 text-sm text-slate-400 max-w-sm mx-auto">We've sent a password reset link to your email address. The link will expire in 60 minutes.</p>
            <button @click="sent = false" class="mt-6 text-sm font-medium text-gold-500 hover:text-gold-400 transition-colors">Didn't receive it? Try again</button>
        </div>

        <p class="mt-8 text-center text-sm text-slate-500">
            <a href="<?= $baseUrl ?>/login" class="font-medium text-gold-500 hover:text-gold-400 transition-colors">&larr; Back to sign in</a>
        </p>
    </div>
</body>
</html>
