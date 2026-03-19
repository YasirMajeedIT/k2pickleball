<?php
/**
 * Tenant Auth — Forgot Password (Standalone, no layout)
 * K2 Navy/Gold Theme. POSTs to /api/auth/forgot-password.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Sports Club', ENT_QUOTES, 'UTF-8');
$logoUrl = $branding['logo_url'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — <?= $orgName ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: { extend: {
            colors: {
                navy: { 950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844', 700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4' },
                gold: { 300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d', 700:'#9c7c24', 800:'#7d6420' },
            },
            fontFamily: { display: ['"Plus Jakarta Sans"', 'sans-serif'], body: ['Inter', 'sans-serif'] },
        }}
    }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-gold-bg { background: linear-gradient(135deg, #e8c84e 0%, #d4af37 50%, #b8952d 100%); }
        .grid-bg { background-image: linear-gradient(rgba(212,175,55,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(212,175,55,.04) 1px, transparent 1px); background-size: 40px 40px; }
    </style>
</head>
<body class="bg-navy-950 text-white min-h-screen flex items-center justify-center p-6">
    <script>const baseApi = window.location.origin;</script>

    <div class="w-full max-w-md" x-data="forgotPwPage()">
        <div class="text-center mb-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-12 mx-auto mb-4 object-contain">
            <?php else: ?>
                <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center mx-auto mb-4">
                    <span class="text-lg font-extrabold text-navy-950"><?= mb_substr($orgName, 0, 1) ?></span>
                </div>
            <?php endif; ?>
            <h1 class="text-2xl font-display font-extrabold text-white">Forgot Your Password?</h1>
            <p class="text-slate-400 text-sm mt-2">Enter your email and we'll send you a reset link.</p>
        </div>

        <!-- Success -->
        <div x-show="sent" x-transition class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-8 text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-white font-bold mb-1">Check Your Email</p>
            <p class="text-slate-400 text-sm">If an account exists with that email, you'll receive a password reset link shortly.</p>
            <a href="/login" class="inline-block mt-6 text-sm text-gold-500 hover:text-gold-400 font-semibold">&larr; Back to Sign In</a>
        </div>

        <!-- Form -->
        <form x-show="!sent" @submit.prevent="submit()" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" x-model="email" required autofocus
                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                       placeholder="you@example.com">
            </div>

            <div x-show="error" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-400" x-text="error"></div>

            <button type="submit" :disabled="loading" class="w-full py-3.5 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-lg hover:shadow-gold-500/20 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="loading ? 'Sending...' : 'Send Reset Link'"></span>
            </button>

            <p class="text-center text-sm text-slate-500">
                <a href="/login" class="text-gold-500 hover:text-gold-400 font-semibold transition-colors">&larr; Back to Sign In</a>
            </p>
        </form>
    </div>

    <script>
    function forgotPwPage() {
        return {
            email: '', loading: false, error: '', sent: false,
            async submit() {
                this.error = '';
                if (!this.email) { this.error = 'Please enter your email.'; return; }
                this.loading = true;
                try {
                    const resp = await fetch(baseApi + '/api/auth/forgot-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email })
                    });
                    this.sent = true;
                } catch(e) { this.error = 'Network error. Please try again.'; }
                finally { this.loading = false; }
            }
        };
    }
    </script>
</body>
</html>
