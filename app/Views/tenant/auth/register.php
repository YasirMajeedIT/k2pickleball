<?php
/**
 * Tenant Auth — Register Page (Standalone, no layout)
 * K2 Navy/Gold Theme. POSTs to /api/auth/register with organization_id + role_slug:'player'.
 */
$orgName     = htmlspecialchars($org['name'] ?? 'Sports Club', ENT_QUOTES, 'UTF-8');
$orgId       = (int)($org['id'] ?? 0);
$primaryColor = $branding['primary_color'] ?? '#d4af37';
$logoUrl     = $branding['logo_url'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — <?= $orgName ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844', 700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4' },
                    gold: { 300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d', 700:'#9c7c24', 800:'#7d6420' },
                },
                fontFamily: { display: ['"Plus Jakarta Sans"', 'sans-serif'], body: ['Inter', 'sans-serif'] },
            }
        }
    }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-gold-bg { background: linear-gradient(135deg, #e8c84e 0%, #d4af37 50%, #b8952d 100%); }
        .grid-bg { background-image: linear-gradient(rgba(212,175,55,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(212,175,55,.04) 1px, transparent 1px); background-size: 40px 40px; }
        .glass { background: rgba(11,22,41,.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
    </style>
</head>
<body class="bg-navy-950 text-white min-h-screen flex">
    <script>
        const baseApi = window.location.origin;
        const ORG_ID = <?= $orgId ?>;
    </script>

    <!-- Left Panel — Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-navy-900 items-center justify-center overflow-hidden">
        <div class="absolute inset-0 grid-bg opacity-30"></div>
        <div class="absolute top-1/3 left-1/3 w-64 h-64 rounded-full bg-gold-500/5 blur-3xl"></div>
        <div class="relative text-center px-12 max-w-md">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-16 mx-auto mb-6 object-contain">
            <?php else: ?>
                <div class="w-16 h-16 rounded-2xl gradient-gold-bg flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-extrabold text-navy-950"><?= mb_substr($orgName, 0, 1) ?></span>
                </div>
            <?php endif; ?>
            <h2 class="text-3xl font-display font-extrabold text-white mb-3">Join <?= $orgName ?></h2>
            <p class="text-slate-400">Create your free account to book courts, register for classes, and manage your schedule.</p>
        </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 overflow-y-auto">
        <div class="w-full max-w-md" x-data="registerPage()">
            <div class="lg:hidden text-center mb-8">
                <?php if ($logoUrl): ?>
                    <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-12 mx-auto mb-3 object-contain">
                <?php endif; ?>
                <h1 class="text-xl font-display font-bold text-white"><?= $orgName ?></h1>
            </div>

            <h1 class="text-2xl font-display font-extrabold text-white mb-2">Create Your Account</h1>
            <p class="text-slate-400 mb-8">Get started in seconds.</p>

            <form @submit.prevent="submit()" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">First Name *</label>
                        <input type="text" x-model="form.first_name" required
                               class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                               placeholder="John">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Last Name *</label>
                        <input type="text" x-model="form.last_name" required
                               class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                               placeholder="Doe">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email *</label>
                    <input type="email" x-model="form.email" required
                           class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                           placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone</label>
                    <input type="tel" x-model="form.phone"
                           class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                           placeholder="(555) 123-4567">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password *</label>
                    <input type="password" x-model="form.password" required minlength="8"
                           class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                           placeholder="Min. 8 characters">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Confirm Password *</label>
                    <input type="password" x-model="form.password_confirmation" required
                           class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                           placeholder="Re-enter password">
                </div>

                <div x-show="error" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-400" x-text="error"></div>

                <button type="submit" :disabled="loading" class="w-full py-3.5 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-lg hover:shadow-gold-500/20 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="loading ? 'Creating Account...' : 'Create Account'"></span>
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-8">
                Already have an account? <a href="/login" class="text-gold-500 hover:text-gold-400 font-semibold transition-colors">Sign In</a>
            </p>
            <p class="text-center mt-4">
                <a href="/" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">&larr; Back to <?= $orgName ?></a>
            </p>
        </div>
    </div>

    <script>
    function registerPage() {
        return {
            form: { first_name: '', last_name: '', email: '', phone: '', password: '', password_confirmation: '' },
            loading: false, error: '',
            async submit() {
                this.error = '';
                if (this.form.password.length < 8) { this.error = 'Password must be at least 8 characters.'; return; }
                if (this.form.password !== this.form.password_confirmation) { this.error = 'Passwords do not match.'; return; }
                this.loading = true;
                try {
                    const resp = await fetch(baseApi + '/api/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ...this.form, organization_id: ORG_ID, role_slug: 'player' })
                    });
                    const json = await resp.json();
                    if (json.success || json.data?.token || json.data?.access_token) {
                        const token = json.data?.access_token || json.data?.token;
                        if (token) {
                            localStorage.setItem('player_token', token);
                            if (json.data.refresh_token) localStorage.setItem('player_refresh', json.data.refresh_token);
                            window.location.href = '/dashboard';
                        } else {
                            window.location.href = '/login?registered=1';
                        }
                    } else {
                        this.error = json.message || 'Registration failed. Please try again.';
                    }
                } catch(e) { this.error = 'Network error. Please try again.'; }
                finally { this.loading = false; }
            }
        };
    }
    </script>
</body>
</html>
