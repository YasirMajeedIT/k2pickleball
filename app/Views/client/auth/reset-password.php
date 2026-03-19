<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password — K2 Platform</title>
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

    <div class="relative w-full max-w-md" x-data="{ showPassword: false, showConfirm: false, loading: false, done: false }">
        <div class="flex items-center justify-center gap-2 mb-10">
            <svg class="w-8 h-8 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
            </svg>
            <span class="font-display text-xl font-extrabold">K2 <span class="gradient-gold">Platform</span></span>
        </div>

        <div x-show="!done">
            <h1 class="font-display text-3xl font-extrabold text-white text-center">Set New Password</h1>
            <p class="mt-2 text-sm text-slate-400 text-center">Choose a strong password for your account.</p>

            <form class="mt-8 space-y-5" @submit.prevent="loading = true; setTimeout(() => { loading = false; done = true; }, 1500)">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">New Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" required autocomplete="new-password" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm pr-11" placeholder="Minimum 8 characters">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" required autocomplete="new-password" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm pr-11" placeholder="Re-enter password">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300 disabled:opacity-70">
                    <span x-show="!loading">Reset Password</span>
                    <span x-show="loading" x-cloak>Resetting...</span>
                </button>
            </form>
        </div>

        <!-- Success -->
        <div x-show="done" x-cloak class="text-center">
            <div class="h-16 w-16 rounded-full bg-gold-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
            </div>
            <h1 class="font-display text-3xl font-extrabold text-white">Password Updated</h1>
            <p class="mt-3 text-sm text-slate-400">Your password has been reset. You can now sign in with your new password.</p>
            <a href="<?= $baseUrl ?>/login" class="mt-6 inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300">
                Sign In
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</body>
</html>
