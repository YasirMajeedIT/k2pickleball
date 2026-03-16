<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857' },
                        surface: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a', 950: '#020617' }
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text { background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
</head>
<body class="h-full bg-surface-950 font-sans text-white antialiased" x-data="loginPage()" x-init="init()">
    <div class="min-h-full flex">
        <!-- Left panel - branding -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-surface-900 to-surface-950 items-center justify-center p-12">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-brand-500/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-brand-600/3 rounded-full blur-3xl"></div>
            </div>
            <div class="relative max-w-md">
                <a href="<?= $baseUrl ?>/" class="flex items-center gap-3 mb-12">
                    <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xl font-bold">K2 Pickleball</span>
                </a>
                <h1 class="text-3xl font-extrabold leading-tight">Welcome back to<br><span class="gradient-text">your dashboard</span></h1>
                <p class="mt-4 text-surface-400">Manage your courts, members, and bookings — all in one place.</p>
                <div class="mt-12 space-y-4">
                    <div class="flex items-center gap-3 text-sm text-surface-400">
                        <svg class="w-5 h-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        Real-time court availability
                    </div>
                    <div class="flex items-center gap-3 text-sm text-surface-400">
                        <svg class="w-5 h-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        Member & booking management
                    </div>
                    <div class="flex items-center gap-3 text-sm text-surface-400">
                        <svg class="w-5 h-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                        Revenue analytics & reports
                    </div>
                </div>
            </div>
        </div>

        <!-- Right panel - form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile logo -->
                <div class="lg:hidden mb-8">
                    <a href="<?= $baseUrl ?>/" class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xl font-bold">K2 Pickleball</span>
                    </a>
                </div>

                <h2 class="text-2xl font-extrabold">Sign in to your account</h2>
                <p class="mt-2 text-sm text-surface-400">
                    Don't have an account?
                    <a href="<?= $baseUrl ?>/register" class="text-brand-400 hover:text-brand-300 font-medium">Create one free</a>
                </p>

                <div class="mt-6">
                    <div id="google-signin-btn" class="flex justify-center"></div>
                    <div class="relative my-5 flex items-center gap-3">
                        <div class="flex-1 border-t border-surface-800"></div>
                        <span class="text-xs text-surface-500 uppercase tracking-wide">or sign in with email</span>
                        <div class="flex-1 border-t border-surface-800"></div>
                    </div>
                </div>

                <form @submit.prevent="login" class="mt-4 space-y-5">
                    <!-- Unverified email notice -->
                    <div x-show="unverified" x-transition class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-sm">
                        Please verify your email address before signing in.
                        <button type="button" @click="resendVerification()" class="ml-1 underline font-medium hover:text-amber-200">Resend verification email.</button>
                    </div>
                    <!-- Error -->
                    <div x-show="error && !unverified" x-transition class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm" x-text="error"></div>
                    <!-- Success info -->
                    <div x-show="info" x-transition class="p-4 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm" x-text="info"></div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Email address</label>
                        <input type="email" x-model="email" required autofocus class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="you@example.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-surface-300">Password</label>
                            <a href="<?= $baseUrl ?>/forgot-password" class="text-xs text-brand-400 hover:text-brand-300">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" x-model="password" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors pr-11" placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-500 hover:text-surface-300">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                        <span x-show="!loading">Sign In</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Signing in...
                        </span>
                    </button>
                </form>

                <p class="mt-8 text-center text-xs text-surface-500">
                    By signing in, you agree to our <a href="<?= $baseUrl ?>/terms" class="hover:text-surface-300 underline">Terms of Service</a> and <a href="<?= $baseUrl ?>/privacy-policy" class="hover:text-surface-300 underline">Privacy Policy</a>.
                </p>
            </div>
        </div>
    </div>

    <script>
    const GOOGLE_CID = '<?= htmlspecialchars($_ENV["GOOGLE_CLIENT_ID"] ?? "", ENT_QUOTES) ?>';
    function loginPage() {
        const baseUrl = '<?= $baseUrl ?>';
        return {
            email: '',
            password: '',
            showPass: false,
            loading: false,
            error: '',
            unverified: false,
            info: '',
            googleSvg: '<svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>',
            init() {
                const el = document.getElementById('google-signin-btn');
                if (!el) return;
                // Always show a styled button immediately
                el.innerHTML = '<button type="button" data-google-btn class="w-full flex items-center justify-center gap-3 px-6 py-3 rounded-xl bg-white text-gray-800 font-medium text-sm hover:bg-gray-100 active:bg-gray-200 transition-colors shadow-sm border border-gray-200">' + this.googleSvg + 'Sign in with Google</button>';
                if (!GOOGLE_CID) {
                    el.querySelector('button').addEventListener('click', () => {
                        this.error = 'Google login is not configured. Set GOOGLE_CLIENT_ID in .env';
                    });
                    return;
                }
                // Try to load real GSI button — replaces placeholder when library loads
                this.waitForGoogle(() => {
                    google.accounts.id.initialize({ client_id: GOOGLE_CID, callback: r => this.googleCallback(r) });
                    google.accounts.id.renderButton(el, { theme: 'filled_black', size: 'large', text: 'signin_with', width: el.offsetWidth || 400 });
                });
            },
            waitForGoogle(callback, tries = 0) {
                if (typeof google !== 'undefined' && google.accounts) {
                    callback();
                } else if (tries < 50) {
                    setTimeout(() => this.waitForGoogle(callback, tries + 1), 100);
                }
            },
            async login() {
                this.error = ''; this.unverified = false; this.info = '';
                this.loading = true;
                try {
                    const res = await fetch(baseUrl + '/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email, password: this.password })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        const msg = data.message || data.error || 'Invalid email or password.';
                        if (msg.toLowerCase().includes('verify') || msg.toLowerCase().includes('verification')) {
                            this.unverified = true;
                        } else {
                            this.error = msg;
                        }
                        this.loading = false;
                        return;
                    }
                    const d = data.data || data;
                    localStorage.setItem('access_token', d.access_token);
                    localStorage.setItem('refresh_token', d.refresh_token);
                    if (d.user) localStorage.setItem('user', JSON.stringify(d.user));

                    // Complete pending registration (org + subscription) if exists
                    await this.completePendingRegistration(d.access_token);

                    window.location.href = baseUrl + '/admin';
                } catch (e) {
                    this.error = 'Network error. Please try again.';
                    this.loading = false;
                }
            },

            async completePendingRegistration(token) {
                const raw = sessionStorage.getItem('k2_pending_reg');
                if (!raw) return;
                try {
                    const reg = JSON.parse(raw);
                    // Create organization
                    if (reg.org_name) {
                        const orgRes = await fetch(baseUrl + '/api/organizations', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                            body: JSON.stringify({
                                name: reg.org_name,
                                slug: reg.org_slug || undefined,
                                email: reg.email || undefined,
                                phone: reg.org_phone || undefined,
                                address_line1: reg.org_address || undefined
                            })
                        });
                        const orgData = await orgRes.json();
                        const orgId = orgData.data?.id || orgData.id;

                        // Create subscription if org was created and plan selected
                        if (orgId && reg.plan_id) {
                            // Process payment for paid plans
                            if (reg.payment_nonce && reg.plan_price > 0) {
                                const amount = Math.round(reg.plan_price * 100);
                                const payRes = await fetch(baseUrl + '/api/payments', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                                    body: JSON.stringify({ source_id: reg.payment_nonce, amount, currency: 'USD', description: 'Subscription payment' })
                                });
                                if (!payRes.ok) {
                                    console.warn('Payment failed:', await payRes.json());
                                }
                            }
                            await fetch(baseUrl + '/api/subscriptions/create-for-org', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                                body: JSON.stringify({
                                    organization_id: orgId,
                                    plan_id: reg.plan_id,
                                    billing_cycle: 'monthly'
                                })
                            });
                        }
                    }
                } catch (e) {
                    console.warn('Post-registration setup:', e);
                }
                sessionStorage.removeItem('k2_pending_reg');
            },
            async resendVerification() {
                this.info = '';
                if (!this.email) { this.error = 'Please enter your email address above first.'; this.unverified = false; return; }
                try {
                    await fetch(baseUrl + '/api/auth/resend-verification', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email })
                    });
                    this.unverified = false;
                    this.info = 'Verification email sent! Check your inbox.';
                } catch { this.error = 'Failed to send. Please try again.'; this.unverified = false; }
            },
            async googleCallback(response) {
                this.loading = true; this.error = ''; this.unverified = false;
                try {
                    const res = await fetch(baseUrl + '/api/auth/google', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ credential: response.credential })
                    });
                    const data = await res.json();
                    if (!res.ok) { this.error = data.message || 'Google sign-in failed.'; this.loading = false; return; }
                    const d = data.data || data;
                    localStorage.setItem('access_token', d.access_token);
                    localStorage.setItem('refresh_token', d.refresh_token);
                    if (d.user) localStorage.setItem('user', JSON.stringify(d.user));
                    window.location.href = baseUrl + '/admin';
                } catch { this.error = 'Google sign-in failed. Please try again.'; this.loading = false; }
            }
        }
    }
    </script>
</body>
</html>
