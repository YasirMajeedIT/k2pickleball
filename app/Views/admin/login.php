<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — K2 Pickleball</title>
    <script>window.APP_BASE = '<?= htmlspecialchars($baseUrl ?? '', ENT_QUOTES) ?>';</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans bg-surface-950 min-h-screen flex items-center justify-center p-4 antialiased">
    <!-- Background decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary-700/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary-600/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-[420px]" x-data="loginForm()">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-500/25 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Welcome back</h1>
            <p class="text-sm text-surface-400 mt-1.5">Sign in to K2 Pickleball Admin</p>
        </div>

        <!-- Login Card -->
        <div class="rounded-2xl border border-surface-800/60 bg-surface-900/80 backdrop-blur-xl shadow-2xl p-7">
            <!-- Error Alert -->
            <div x-show="error" x-cloak
                 x-transition:enter="transition duration-200 ease-out"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-5 rounded-xl bg-red-500/10 border border-red-500/20 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-red-300" x-text="error"></p>
            </div>

            <form x-on:submit.prevent="submit()">
                <div class="mb-5">
                    <label class="mb-2 block text-sm font-semibold text-surface-300">Email</label>
                    <div class="relative group">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-surface-500 group-focus-within:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <input type="email" x-model="email" required placeholder="you@example.com"
                               class="w-full rounded-xl border border-surface-700 bg-surface-800/50 py-3 pl-10 pr-4 text-sm text-white placeholder:text-surface-500 outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="mb-2 block text-sm font-semibold text-surface-300">Password</label>
                    <div class="relative group">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-surface-500 group-focus-within:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" x-model="password" required placeholder="Enter your password"
                               class="w-full rounded-xl border border-surface-700 bg-surface-800/50 py-3 pl-10 pr-12 text-sm text-white placeholder:text-surface-500 outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all">
                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-500 hover:text-surface-300 transition-colors">
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" x-model="remember" class="peer sr-only">
                            <div class="h-4 w-4 rounded border border-surface-600 bg-surface-800 peer-checked:bg-primary-500 peer-checked:border-primary-500 transition-colors flex items-center justify-center">
                                <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <span class="text-sm text-surface-400 group-hover:text-surface-300 transition-colors">Remember me</span>
                    </label>
                </div>

                <button type="submit" :disabled="loading"
                        class="flex w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 py-3 px-4 text-sm text-white font-semibold hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 shadow-lg shadow-primary-500/20 hover:shadow-primary-500/30 transition-all">
                    <template x-if="loading">
                        <div class="relative h-5 w-5">
                            <div class="absolute inset-0 rounded-full border-2 border-white/30"></div>
                            <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-white animate-spin"></div>
                        </div>
                    </template>
                    <span x-text="loading ? 'Signing in...' : 'Sign In'"></span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-surface-500 mt-8">&copy; <?= date('Y') ?> K2 Pickleball. All rights reserved.</p>
    </div>

    <script>
    // Clear any stale tokens when visiting login page
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');

    function loginForm() {
        return {
            email: '',
            password: '',
            remember: false,
            showPassword: false,
            loading: false,
            error: '',
            async submit() {
                this.loading = true;
                this.error = '';
                try {
                    const res = await fetch(APP_BASE + '/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email, password: this.password })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.error = data.message || 'Login failed';
                        return;
                    }
                    localStorage.setItem('access_token', data.data.access_token);
                    localStorage.setItem('refresh_token', data.data.refresh_token);
                    window.location.href = APP_BASE + '/admin';
                } catch (e) {
                    this.error = 'Network error. Please try again.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }
    </script>
</body>
</html>
