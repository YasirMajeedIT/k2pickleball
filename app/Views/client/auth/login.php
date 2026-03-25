<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login — K2Pickleball.com</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#060d1a', 900:'#0b1629', 850:'#101f36', 800:'#162844', 700:'#1e3658', 600:'#27466e', 500:'#3160a0', 400:'#4a7ec4' },
                    gold: { 300:'#f0d878', 400:'#e8c84e', 500:'#d4af37', 600:'#b8952d', 700:'#9c7c24', 800:'#7d6420' }
                },
                fontFamily: { display: ['Poppins', 'sans-serif'], body: ['Poppins', 'sans-serif'] }
            }
        }
    }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-gold { background: linear-gradient(135deg, #f0d878, #d4af37, #b8952d); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-gold-bg { background: linear-gradient(135deg, #f0d878, #d4af37, #b8952d); }
        .grid-bg { background-image: radial-gradient(rgba(212,175,55,0.08) 1px, transparent 1px); background-size: 32px 32px; }
    </style>
</head>
<body class="bg-navy-950 text-white min-h-screen flex">
    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-navy-900 items-center justify-center overflow-hidden">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-gold-500/5 rounded-full blur-[120px]"></div>
        <div class="relative text-center px-12">
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="relative">
                    <svg class="w-12 h-12 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                        <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
                    </svg>
                </div>
                <span class="text-3xl font-extrabold tracking-tight">K2 <span class="gradient-gold">Pickleball</span></span>
            </div>
            <h2 class="text-2xl font-bold text-white mb-4">Welcome Back, Partner</h2>
            <p class="text-slate-400 leading-relaxed max-w-sm mx-auto">Access your facility dashboard, manage operations, and track performance — all in one place.</p>
            <div class="mt-12 grid grid-cols-2 gap-4 max-w-xs mx-auto">
                <div class="text-center p-4 rounded-xl bg-navy-800/40 border border-navy-700/40">
                    <div class="text-2xl font-extrabold gradient-gold">$1M+</div>
                    <div class="text-xs text-slate-500 mt-1">Revenue Generated</div>
                </div>
                <div class="text-center p-4 rounded-xl bg-navy-800/40 border border-navy-700/40">
                    <div class="text-2xl font-extrabold gradient-gold">94%</div>
                    <div class="text-xs text-slate-500 mt-1">Court Utilization</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md" x-data="loginForm()">
            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <svg class="w-8 h-8 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                    <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
                </svg>
                <span class="text-xl font-extrabold">K2 <span class="gradient-gold">Pickleball</span></span>
            </div>

            <h1 class="text-3xl font-extrabold text-white">Partner Login</h1>
            <p class="mt-2 text-sm text-slate-400">Sign in to your facility management dashboard.</p>

            <!-- Error/Success Messages -->
            <div x-show="errorMsg" x-cloak class="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-sm text-red-400" x-text="errorMsg"></div>
            <div x-show="successMsg" x-cloak class="mt-4 p-3 rounded-xl bg-green-500/10 border border-green-500/30 text-sm text-green-400" x-text="successMsg"></div>

            <form class="mt-8 space-y-5" @submit.prevent="handleLogin()">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" x-model="email" required autocomplete="email" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="you@example.com">
                    <p x-show="errors.email" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.email"></p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-medium text-slate-300">Password</label>
                        <a href="<?= $baseUrl ?>/forgot-password" class="text-xs font-medium text-gold-500 hover:text-gold-400 transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="password" required autocomplete="current-password" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm pr-11" placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    <p x-show="errors.password" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.password"></p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" class="w-4 h-4 rounded border-navy-700 bg-navy-900/60 text-gold-500 focus:ring-gold-500/20">
                    <label for="remember" class="text-sm text-slate-400">Keep me signed in</label>
                </div>

                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300 disabled:opacity-70">
                    <span x-show="!loading">Sign In</span>
                    <svg x-show="loading" x-cloak class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-show="loading" x-cloak>Signing in...</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center gap-3">
                <div class="flex-1 h-px bg-navy-700/60"></div>
                <span class="text-xs text-slate-500 uppercase tracking-wider">or</span>
                <div class="flex-1 h-px bg-navy-700/60"></div>
            </div>

            <!-- Google Sign In -->
            <button @click="googleLogin()" :disabled="loading" class="w-full flex items-center justify-center gap-3 px-6 py-3.5 text-sm font-medium text-white bg-navy-800/60 border border-navy-700/60 rounded-xl hover:bg-navy-800 hover:border-gold-500/30 transition-all duration-300 disabled:opacity-70">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continue with Google
            </button>

            <p class="mt-8 text-center text-sm text-slate-500">
                Don't have an account? <a href="<?= $baseUrl ?>/register" class="font-medium text-gold-500 hover:text-gold-400 transition-colors">Register here</a>
            </p>
            <p class="mt-2 text-center text-sm text-slate-500">
                Want to learn more? <a href="<?= $baseUrl ?>/demo" class="font-medium text-gold-500 hover:text-gold-400 transition-colors">Schedule a consultation</a>
            </p>
        </div>
    </div>

    <script>
    function loginForm() {
        return {
            email: '',
            password: '',
            showPassword: false,
            loading: false,
            errorMsg: '',
            successMsg: '',
            errors: {},

            validate() {
                this.errors = {};
                if (!this.email.trim()) this.errors.email = 'Email is required';
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) this.errors.email = 'Enter a valid email address';
                if (!this.password) this.errors.password = 'Password is required';
                else if (this.password.length < 6) this.errors.password = 'Password must be at least 6 characters';
                return Object.keys(this.errors).length === 0;
            },

            async handleLogin() {
                this.errorMsg = '';
                this.successMsg = '';
                if (!this.validate()) return;

                this.loading = true;
                try {
                    const res = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email, password: this.password })
                    });
                    const json = await res.json();
                    if (json.status === 'success') {
                        const data = json.data || json;
                        localStorage.setItem('access_token', data.access_token);
                        localStorage.setItem('refresh_token', data.refresh_token);
                        this.successMsg = 'Login successful! Redirecting...';
                        setTimeout(() => { window.location.href = '/admin'; }, 600);
                    } else {
                        this.errorMsg = json.message || 'Invalid email or password. Please try again.';
                    }
                } catch (e) {
                    this.errorMsg = 'Connection error. Please try again.';
                } finally {
                    this.loading = false;
                }
            },

            googleLogin() {
                this.errorMsg = '';
                const clientId = '<?= htmlspecialchars($_ENV["GOOGLE_CLIENT_ID"] ?? "", ENT_QUOTES, "UTF-8") ?>';
                if (!clientId) {
                    this.errorMsg = 'Google sign-in is not configured on this server.';
                    return;
                }
                // Use ID token flow — no client secret needed on the frontend
                google.accounts.id.initialize({
                    client_id: clientId,
                    callback: async (response) => {
                        this.loading = true;
                        try {
                            const res = await fetch('/api/auth/google', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ credential: response.credential })
                            });
                            const json = await res.json();
                            if (json.status === 'success') {
                                const data = json.data || json;
                                localStorage.setItem('access_token', data.access_token);
                                localStorage.setItem('refresh_token', data.refresh_token);
                                this.successMsg = 'Login successful! Redirecting...';
                                setTimeout(() => { window.location.href = '/admin'; }, 600);
                            } else {
                                this.errorMsg = json.message || 'Google login failed. Please try again.';
                            }
                        } catch (e) {
                            this.errorMsg = 'Connection error. Please try again.';
                        } finally {
                            this.loading = false;
                        }
                    },
                    context: 'signin',
                    auto_select: false,
                    cancel_on_tap_outside: true,
                });
                google.accounts.id.prompt((notification) => {
                    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                        // One-Tap blocked — render the standard button as fallback
                        const wrap = document.getElementById('google-btn-fallback-login');
                        if (wrap) {
                            wrap.style.display = 'flex';
                            google.accounts.id.renderButton(wrap, {
                                type: 'standard', theme: 'outline', size: 'large',
                                text: 'signin_with', width: wrap.offsetWidth || 400,
                            });
                        }
                    }
                });
            }
        }
    }
    </script>
</body>
</html>
