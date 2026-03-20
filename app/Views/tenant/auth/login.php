<?php
/**
 * Tenant Auth — Login Page (Standalone, no layout)
 * K2 Navy/Gold Theme. POSTs to /api/auth/login.
 */
$orgName     = htmlspecialchars($org['name'] ?? 'Sports Club', ENT_QUOTES, 'UTF-8');
$primaryColor = $branding['primary_color'] ?? '#d4af37';
$logoUrl     = $branding['logo_url'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — <?= $orgName ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
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
    </script>

    <!-- Left Panel — Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-navy-900 items-center justify-center overflow-hidden">
        <div class="absolute inset-0 grid-bg opacity-30"></div>
        <div class="absolute top-1/4 left-1/4 w-64 h-64 rounded-full bg-gold-500/5 blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-48 h-48 rounded-full bg-gold-500/5 blur-3xl"></div>
        <div class="relative text-center px-12 max-w-md">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-16 mx-auto mb-6 object-contain">
            <?php else: ?>
                <div class="w-16 h-16 rounded-2xl gradient-gold-bg flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-extrabold text-navy-950"><?= mb_substr($orgName, 0, 1) ?></span>
                </div>
            <?php endif; ?>
            <h2 class="text-3xl font-display font-extrabold text-white mb-3"><?= $orgName ?></h2>
            <p class="text-slate-400"><?= htmlspecialchars($branding['tagline'] ?? 'Welcome back! Sign in to access your account.', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- Right Panel — Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md" x-data="loginPage()">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <?php if ($logoUrl): ?>
                    <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $orgName ?>" class="h-12 mx-auto mb-3 object-contain">
                <?php endif; ?>
                <h1 class="text-xl font-display font-bold text-white"><?= $orgName ?></h1>
            </div>

            <h1 class="text-2xl font-display font-extrabold text-white mb-2">Welcome Back</h1>
            <p class="text-slate-400 mb-8">Sign in to your account to continue.</p>

            <form @submit.prevent="submit()" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                    <input type="email" x-model="email" required autofocus
                           class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                           placeholder="you@example.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <input :type="showPw ? 'text' : 'password'" x-model="password" required
                               class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all pr-12"
                               placeholder="••••••••">
                        <button type="button" @click="showPw = !showPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg x-show="!showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded bg-navy-800 border-navy-700 text-gold-500 focus:ring-gold-500/50">
                        <span class="text-sm text-slate-400">Remember me</span>
                    </label>
                    <a href="/forgot-password" class="text-sm text-gold-500 hover:text-gold-400 transition-colors">Forgot password?</a>
                </div>

                <div x-show="error" class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-400" x-text="error"></div>

                <button type="submit" :disabled="loading" class="w-full py-3.5 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-lg hover:shadow-gold-500/20 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="loading ? 'Signing in...' : 'Sign In'"></span>
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-6">
                <span class="flex-1 h-px bg-navy-700"></span>
                <span class="text-xs text-slate-500 uppercase tracking-wider">or</span>
                <span class="flex-1 h-px bg-navy-700"></span>
            </div>

            <!-- Google Sign-In -->
            <button @click="googleLogin()" :disabled="googleLoading" type="button"
                    class="w-full py-3 rounded-xl bg-navy-800 border border-navy-700 hover:border-gold-500/30 hover:bg-navy-850 transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                <span class="text-sm font-semibold text-white" x-text="googleLoading ? 'Signing in...' : 'Continue with Google'"></span>
            </button>

            <p class="text-center text-sm text-slate-500 mt-8">
                Don't have an account? <a href="/register" class="text-gold-500 hover:text-gold-400 font-semibold transition-colors">Create one</a>
            </p>
            <p class="text-center mt-4">
                <a href="/" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">&larr; Back to <?= $orgName ?></a>
            </p>
        </div>
    </div>

    <script>
    function loginPage() {
        return {
            email: '', password: '', loading: false, error: '', showPw: false, googleLoading: false,
            async submit() {
                this.error = '';
                if (!this.email || !this.password) { this.error = 'Please enter your email and password.'; return; }
                this.loading = true;
                try {
                    const resp = await fetch(baseApi + '/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email, password: this.password })
                    });
                    const json = await resp.json();
                    if (json.status === 'success' && json.data?.access_token) {
                        localStorage.setItem('player_token', json.data.access_token);
                        if (json.data.refresh_token) localStorage.setItem('player_refresh', json.data.refresh_token);
                        window.location.href = '/dashboard';
                    } else {
                        this.error = json.message || 'Invalid credentials.';
                    }
                } catch(e) { this.error = 'Network error. Please try again.'; }
                finally { this.loading = false; }
            },
            googleLogin() {
                this.error = '';
                this.googleLoading = true;
                const clientId = '<?= htmlspecialchars($_ENV['GOOGLE_CLIENT_ID'] ?? '', ENT_QUOTES, 'UTF-8') ?>';
                if (!clientId) { this.error = 'Google Sign-In is not configured.'; this.googleLoading = false; return; }
                const self = this;
                google.accounts.id.initialize({
                    client_id: clientId,
                    callback: async (response) => {
                        try {
                            const resp = await fetch(baseApi + '/api/auth/google', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ credential: response.credential, organization_id: <?= (int)($org['id'] ?? 0) ?> })
                            });
                            const json = await resp.json();
                            if (json.status === 'success' && json.data?.access_token) {
                                localStorage.setItem('player_token', json.data.access_token);
                                if (json.data.refresh_token) localStorage.setItem('player_refresh', json.data.refresh_token);
                                window.location.href = '/dashboard';
                            } else {
                                self.error = json.message || 'Google sign-in failed.';
                            }
                        } catch(e) { self.error = 'Network error. Please try again.'; }
                        finally { self.googleLoading = false; }
                    },
                    auto_select: false,
                });
                google.accounts.id.prompt((notification) => {
                    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                        /* One-Tap not shown — open the picker popup instead */
                        google.accounts.id.renderButton(
                            document.createElement('div'),
                            { type: 'standard', size: 'large' }
                        );
                        /* Fallback: use the popup approach */
                        const tokenClient = google.accounts.oauth2?.initCodeClient;
                        /* If One-Tap fails, that's OK — the user can try again */
                        self.googleLoading = false;
                    }
                });
            }
        };
    }
    </script>
</body>
</html>
