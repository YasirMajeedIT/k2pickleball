<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="h-full bg-surface-950 font-sans text-white antialiased" x-data="loginPage()">
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

                <form @submit.prevent="login" class="mt-8 space-y-5">
                    <!-- Error -->
                    <div x-show="error" x-transition class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm" x-text="error"></div>

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
                    By signing in, you agree to our Terms of Service and Privacy Policy.
                </p>
            </div>
        </div>
    </div>

    <script>
    function loginPage() {
        const baseUrl = '<?= $baseUrl ?>';
        return {
            email: '',
            password: '',
            showPass: false,
            loading: false,
            error: '',
            async login() {
                this.error = '';
                this.loading = true;
                try {
                    const res = await fetch(baseUrl + '/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email, password: this.password })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.error = data.error || data.message || 'Invalid email or password.';
                        this.loading = false;
                        return;
                    }
                    localStorage.setItem('access_token', data.access_token);
                    localStorage.setItem('refresh_token', data.refresh_token);
                    if (data.user) localStorage.setItem('user', JSON.stringify(data.user));
                    window.location.href = baseUrl + '/portal';
                } catch (e) {
                    this.error = 'Network error. Please try again.';
                    this.loading = false;
                }
            }
        }
    }
    </script>
</body>
</html>
