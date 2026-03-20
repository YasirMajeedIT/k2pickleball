<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Admin Login — K2 Platform</title>
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#faf5ff',100:'#f3e8ff',200:'#e9d5ff',300:'#d8b4fe',400:'#c084fc',500:'#a855f7',600:'#9333ea',700:'#7e22ce',800:'#6b21a8',900:'#581c87',950:'#3b0764' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glow-purple { box-shadow: 0 0 60px rgba(168,85,247,0.15); }
        .grid-bg {
            background-image: radial-gradient(rgba(168,85,247,0.07) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="bg-surface-950 text-white min-h-screen flex antialiased" x-data="platformLogin()">

    <!-- Left branding panel -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-surface-900 items-center justify-center overflow-hidden border-r border-surface-800">
        <div class="absolute inset-0 grid-bg opacity-60"></div>
        <div class="absolute top-[-80px] right-[-80px] w-96 h-96 bg-primary-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-60px] left-[-60px] w-72 h-72 bg-primary-700/10 rounded-full blur-[80px]"></div>

        <div class="relative text-center px-12 max-w-md">
            <!-- Logo -->
            <div class="flex items-center justify-center gap-3 mb-10">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-500/30">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 40 40" fill="currentColor">
                        <path d="M20 3 L24 15 L37 15 L26 23 L30 35 L20 28 L10 35 L14 23 L3 15 L16 15 Z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight">K2 <span class="text-primary-400">Platform</span></span>
            </div>

            <h2 class="text-3xl font-extrabold text-white mb-4 leading-tight">Super Admin<br><span class="text-primary-400">Control Center</span></h2>
            <p class="text-surface-400 leading-relaxed text-sm">Manage all organizations, subscriptions, billing plans, and system-wide settings from one powerful dashboard.</p>

            <!-- Stats grid -->
            <div class="mt-12 grid grid-cols-2 gap-4">
                <div class="rounded-2xl bg-surface-800/40 border border-surface-700/40 p-5 text-left">
                    <div class="text-2xl font-extrabold text-primary-400">Orgs</div>
                    <div class="text-xs text-surface-500 mt-1">Full lifecycle control</div>
                </div>
                <div class="rounded-2xl bg-surface-800/40 border border-surface-700/40 p-5 text-left">
                    <div class="text-2xl font-extrabold text-primary-400">Plans</div>
                    <div class="text-xs text-surface-500 mt-1">Billing & subscriptions</div>
                </div>
                <div class="rounded-2xl bg-surface-800/40 border border-surface-700/40 p-5 text-left">
                    <div class="text-2xl font-extrabold text-primary-400">Users</div>
                    <div class="text-xs text-surface-500 mt-1">System-wide accounts</div>
                </div>
                <div class="rounded-2xl bg-surface-800/40 border border-surface-700/40 p-5 text-left">
                    <div class="text-2xl font-extrabold text-primary-400">Audit</div>
                    <div class="text-xs text-surface-500 mt-1">Logs & compliance</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right login panel -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-700">
                    <svg class="h-5 w-5 text-white" viewBox="0 0 40 40" fill="currentColor">
                        <path d="M20 3 L24 15 L37 15 L26 23 L30 35 L20 28 L10 35 L14 23 L3 15 L16 15 Z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold">K2 <span class="text-primary-400">Platform</span></span>
            </div>

            <h1 class="text-3xl font-extrabold text-white">Admin Login</h1>
            <p class="mt-2 text-sm text-surface-400">Sign in to access the platform control center.</p>

            <!-- Error alert -->
            <div x-show="error" x-cloak class="mt-5 flex items-start gap-3 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <p class="text-sm text-red-300" x-text="error"></p>
            </div>

            <form class="mt-7 space-y-5" @submit.prevent="handleLogin">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Email Address</label>
                    <input
                        type="email"
                        x-model="form.email"
                        required
                        autocomplete="email"
                        :disabled="loading"
                        class="w-full rounded-xl border border-surface-700 bg-surface-800/60 px-4 py-3 text-sm text-white placeholder-surface-500 focus:border-primary-500/60 focus:outline-none focus:ring-1 focus:ring-primary-500/30 disabled:opacity-50 transition-colors"
                        placeholder="admin@k2platform.com"
                    >
                </div>

                <!-- Password -->
                <div x-data="{ showPwd: false }">
                    <label class="block text-sm font-medium text-surface-300 mb-1.5">Password</label>
                    <div class="relative">
                        <input
                            :type="showPwd ? 'text' : 'password'"
                            x-model="form.password"
                            required
                            autocomplete="current-password"
                            :disabled="loading"
                            class="w-full rounded-xl border border-surface-700 bg-surface-800/60 px-4 py-3 pr-11 text-sm text-white placeholder-surface-500 focus:border-primary-500/60 focus:outline-none focus:ring-1 focus:ring-primary-500/30 disabled:opacity-50 transition-colors"
                            placeholder="••••••••••"
                        >
                        <button
                            type="button"
                            @click="showPwd = !showPwd"
                            tabindex="-1"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-200 transition-colors"
                        >
                            <svg x-show="!showPwd" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="showPwd" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="loading"
                    class="mt-2 w-full rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 hover:from-primary-500 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500/50 disabled:opacity-60 disabled:cursor-not-allowed transition-all"
                >
                    <span x-show="!loading">Sign In to Platform</span>
                    <span x-show="loading" x-cloak class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Signing in…
                    </span>
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-surface-600">
                K2 Platform &copy; <?= date('Y') ?> &mdash; Super Admin Access Only
            </p>
        </div>
    </div>

    <script>
        // Redirect if already logged in
        if (localStorage.getItem('access_token')) {
            window.location.replace(APP_BASE + '/platform');
        }

        function platformLogin() {
            return {
                form: { email: '', password: '' },
                loading: false,
                error: '',

                async handleLogin() {
                    this.error = '';
                    this.loading = true;

                    try {
                        const res = await fetch(APP_BASE + '/api/auth/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ email: this.form.email, password: this.form.password }),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            this.error = data.message || 'Invalid email or password.';
                            return;
                        }

                        // Store tokens
                        localStorage.setItem('access_token', data.access_token);
                        if (data.refresh_token) {
                            localStorage.setItem('refresh_token', data.refresh_token);
                        }

                        // Verify this is a platform (super-admin) account
                        const meRes = await fetch(APP_BASE + '/api/auth/me', {
                            headers: { 'Authorization': 'Bearer ' + data.access_token, 'Accept': 'application/json' },
                        });
                        const meData = await meRes.json();
                        const roles = meData.roles ?? [];
                        const isSuperAdmin = roles.some(r => r === 'super-admin' || r === 'super_admin' || (r && r.slug && (r.slug === 'super-admin' || r.slug === 'super_admin')));

                        if (!isSuperAdmin) {
                            localStorage.removeItem('access_token');
                            localStorage.removeItem('refresh_token');
                            this.error = 'Access denied. This portal is for platform administrators only.';
                            return;
                        }

                        window.location.href = APP_BASE + '/platform';

                    } catch (err) {
                        this.error = 'Network error. Please check your connection and try again.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</body>
</html>
