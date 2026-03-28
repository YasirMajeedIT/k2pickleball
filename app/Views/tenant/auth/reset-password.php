<?php
/**
 * Tenant Auth — Reset Password (Standalone, no layout)
 * K2 Navy/Gold Theme. POSTs to /api/auth/reset-password. Expects ?token=X in URL.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Sports Club', ENT_QUOTES, 'UTF-8');
$logoUrl = $branding['logo_url'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — <?= $orgName ?></title>
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
    </style>
</head>
<body class="bg-navy-950 text-white min-h-screen flex items-center justify-center p-6">
    <script>const baseApi = window.location.origin;</script>

    <div class="w-full max-w-md" x-data="resetPwPage()">
        <div class="text-center mb-8">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-12 mx-auto mb-4 object-contain">
            <?php else: ?>
                <div class="w-12 h-12 rounded-xl gradient-gold-bg flex items-center justify-center mx-auto mb-4">
                    <span class="text-lg font-extrabold text-navy-950"><?= mb_substr($orgName, 0, 1) ?></span>
                </div>
            <?php endif; ?>
            <h1 class="text-2xl font-display font-extrabold text-white">Set New Password</h1>
            <p class="text-slate-400 text-sm mt-2">Enter your new password below.</p>
        </div>

        <!-- Success -->
        <div x-show="done" x-transition class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-8 text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-white font-bold mb-1">Password Updated!</p>
            <p class="text-slate-400 text-sm">Your password has been reset. You can now sign in.</p>
            <a href="/login" class="inline-block mt-6 px-6 py-2.5 rounded-xl gradient-gold-bg text-navy-950 font-bold text-sm hover:shadow-lg hover:shadow-gold-500/20 transition-all">Sign In</a>
        </div>

        <!-- Form -->
        <form x-show="!done" @submit.prevent="submit()" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">New Password</label>
                <input type="password" x-model="password" required minlength="8"
                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                       placeholder="Min. 8 characters">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password</label>
                <input type="password" x-model="passwordConfirm" required
                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                       placeholder="Re-enter password">
            </div>

            <div x-show="error" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-400" x-text="error"></div>

            <button type="submit" :disabled="loading" class="w-full py-3.5 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-lg hover:shadow-gold-500/20 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="loading ? 'Resetting...' : 'Reset Password'"></span>
            </button>

            <p class="text-center text-sm text-slate-500">
                <a href="/login" class="text-gold-500 hover:text-gold-400 font-semibold transition-colors">&larr; Back to Sign In</a>
            </p>
        </form>
    </div>

    <script>
    function resetPwPage() {
        return {
            password: '', passwordConfirm: '', loading: false, error: '', done: false,
            get token() {
                return new URLSearchParams(window.location.search).get('token') || '';
            },
            async submit() {
                this.error = '';
                if (this.password.length < 8) { this.error = 'Password must be at least 8 characters.'; return; }
                if (this.password !== this.passwordConfirm) { this.error = 'Passwords do not match.'; return; }
                if (!this.token) { this.error = 'Invalid or missing reset token.'; return; }
                this.loading = true;
                try {
                    const resp = await fetch(baseApi + '/api/auth/reset-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token: this.token, password: this.password, password_confirmation: this.passwordConfirm })
                    });
                    const json = await resp.json();
                    if (json.status === 'success') { this.done = true; }
                    else { this.error = json.message || 'Failed to reset password.'; }
                } catch(e) { this.error = 'Network error. Please try again.'; }
                finally { this.loading = false; }
            }
        };
    }
    </script>
</body>
</html>
