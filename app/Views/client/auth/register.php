<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration — K2Pickleball.com</title>
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
    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-navy-900 items-center justify-center overflow-hidden">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-gold-500/5 rounded-full blur-[120px]"></div>
        <div class="relative text-center px-12">
            <div class="flex items-center justify-center gap-3 mb-8">
                <svg class="w-12 h-12 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                    <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
                </svg>
                <span class="text-3xl font-extrabold tracking-tight">K2 <span class="gradient-gold">Pickleball</span></span>
            </div>
            <h2 class="text-2xl font-bold text-white mb-4">Start Your K2Pickleball Partnership</h2>
            <p class="text-slate-400 leading-relaxed max-w-sm mx-auto">Create your organization account to get started with a 7-day free trial — full access to the K2Pickleball platform and operational support.</p>
            <div class="mt-10 space-y-3 text-left max-w-xs mx-auto">
                <?php
                $benefits = ['Create your organization in minutes', '7-day free trial — no credit card required', 'Full K2Pickleball Platform access', 'Dedicated launch support & guidance'];
                foreach ($benefits as $b): ?>
                <div class="flex items-center gap-3 text-sm">
                    <svg class="w-5 h-5 text-gold-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    <span class="text-slate-300"><?= $b ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 overflow-y-auto">
        <div class="w-full max-w-md" x-data="registerForm()">
            <div class="lg:hidden flex items-center justify-center gap-2 mb-10">
                <svg class="w-8 h-8 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                    <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
                </svg>
                <span class="text-xl font-extrabold">K2 <span class="gradient-gold">Pickleball</span></span>
            </div>

            <h1 class="text-3xl font-extrabold text-white">Create Your Organization</h1>
            <p class="mt-2 text-sm text-slate-400">Register and set up your pickleball facility in minutes.</p>

            <!-- Error/Success Messages -->
            <div x-show="errorMsg" x-cloak class="mt-4 p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-sm text-red-400" x-text="errorMsg"></div>
            <div x-show="successMsg" x-cloak class="mt-4 p-3 rounded-xl bg-green-500/10 border border-green-500/30 text-sm text-green-400" x-text="successMsg"></div>

            <!-- Google Sign Up -->
            <button @click="googleSignUp()" :disabled="loading" class="mt-6 w-full flex items-center justify-center gap-3 px-6 py-3.5 text-sm font-medium text-white bg-navy-800/60 border border-navy-700/60 rounded-xl hover:bg-navy-800 hover:border-gold-500/30 transition-all duration-300 disabled:opacity-70">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Sign up with Google
            </button>

            <!-- Divider -->
            <div class="my-6 flex items-center gap-3">
                <div class="flex-1 h-px bg-navy-700/60"></div>
                <span class="text-xs text-slate-500 uppercase tracking-wider">or register with email</span>
                <div class="flex-1 h-px bg-navy-700/60"></div>
            </div>

            <form class="space-y-5" @submit.prevent="handleRegister()">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">First Name</label>
                        <input type="text" x-model="firstName" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="John">
                        <p x-show="errors.firstName" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.firstName"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Last Name</label>
                        <input type="text" x-model="lastName" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Smith">
                        <p x-show="errors.lastName" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.lastName"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" x-model="email" required autocomplete="email" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="you@example.com">
                    <p x-show="errors.email" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.email"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Phone Number</label>
                    <input type="tel" x-model="phone" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="(555) 123-4567">
                </div>

                <!-- Organization Details -->
                <div class="pt-2 pb-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="h-px flex-1 bg-navy-700/60"></div>
                        <span class="text-xs text-gold-500 uppercase tracking-wider font-semibold">Organization Details</span>
                        <div class="h-px flex-1 bg-navy-700/60"></div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Organization Name</label>
                    <input type="text" x-model="orgName" @input="autoSlug()" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="e.g. Tampa Bay Pickleball Club">
                    <p x-show="errors.orgName" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.orgName"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Organization Slug <span class="text-slate-500">(URL)</span></label>
                    <div class="relative">
                        <input type="text" x-model="orgSlug" required class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="tampa-bay-pickleball">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500">.k2pickleball.com</div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Lowercase letters, numbers, and hyphens only. This will be your facility's subdomain.</p>
                    <p x-show="errors.orgSlug" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.orgSlug"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="password" required autocomplete="new-password" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm pr-11" placeholder="Minimum 8 characters">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    <p x-show="errors.password" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.password"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Confirm Password</label>
                    <input type="password" x-model="passwordConfirmation" required autocomplete="new-password" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Repeat your password">
                    <p x-show="errors.passwordConfirmation" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.passwordConfirmation"></p>
                </div>

                <div class="flex items-start gap-2">
                    <input type="checkbox" x-model="agreed" id="terms" class="w-4 h-4 mt-0.5 rounded border-navy-700 bg-navy-900/60 text-gold-500 focus:ring-gold-500/20">
                    <label for="terms" class="text-sm text-slate-400">I agree to the <a href="<?= $baseUrl ?>/terms" class="text-gold-500 hover:text-gold-400">Terms of Service</a> and <a href="<?= $baseUrl ?>/privacy-policy" class="text-gold-500 hover:text-gold-400">Privacy Policy</a>.</label>
                </div>
                <p x-show="errors.agreed" x-cloak class="text-xs text-red-400" x-text="errors.agreed"></p>

                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300 disabled:opacity-70">
                    <span x-show="!loading">Create Organization & Account</span>
                    <svg x-show="loading" x-cloak class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-show="loading" x-cloak>Setting up your organization...</span>
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-500">
                Already have an account? <a href="<?= $baseUrl ?>/login" class="font-medium text-gold-500 hover:text-gold-400 transition-colors">Sign in</a>
            </p>
        </div>
    </div>

    <script>
    function registerForm() {
        return {
            firstName: '',
            lastName: '',
            email: '',
            phone: '',
            orgName: '',
            orgSlug: '',
            password: '',
            passwordConfirmation: '',
            showPassword: false,
            agreed: false,
            loading: false,
            errorMsg: '',
            successMsg: '',
            errors: {},

            autoSlug() {
                this.orgSlug = this.orgName
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            },

            validate() {
                this.errors = {};
                if (!this.firstName.trim()) this.errors.firstName = 'First name is required';
                if (!this.lastName.trim()) this.errors.lastName = 'Last name is required';
                if (!this.email.trim()) this.errors.email = 'Email is required';
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) this.errors.email = 'Enter a valid email address';
                if (!this.orgName.trim()) this.errors.orgName = 'Organization name is required';
                if (!this.orgSlug.trim()) this.errors.orgSlug = 'Organization slug is required';
                else if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(this.orgSlug)) this.errors.orgSlug = 'Only lowercase letters, numbers, and hyphens';
                if (!this.password) this.errors.password = 'Password is required';
                else if (this.password.length < 8) this.errors.password = 'Password must be at least 8 characters';
                if (this.password !== this.passwordConfirmation) this.errors.passwordConfirmation = 'Passwords do not match';
                if (!this.agreed) this.errors.agreed = 'You must agree to the terms';
                return Object.keys(this.errors).length === 0;
            },

            async handleRegister() {
                this.errorMsg = '';
                this.successMsg = '';
                if (!this.validate()) return;

                this.loading = true;
                try {
                    const res = await fetch('/api/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            first_name: this.firstName.trim(),
                            last_name: this.lastName.trim(),
                            email: this.email.trim(),
                            phone: this.phone.trim(),
                            password: this.password,
                            password_confirmation: this.passwordConfirmation,
                            organization_name: this.orgName.trim(),
                            organization_slug: this.orgSlug.trim()
                        })
                    });
                    const json = await res.json();
                    if (json.status === 'success') {
                        const data = json.data || json;
                        if (data.access_token) {
                            localStorage.setItem('access_token', data.access_token);
                            localStorage.setItem('refresh_token', data.refresh_token);
                            this.successMsg = 'Account created! Redirecting to dashboard...';
                            setTimeout(() => { window.location.href = '/admin'; }, 800);
                        } else {
                            this.successMsg = 'Account created! Please check your email to verify, then log in.';
                            setTimeout(() => { window.location.href = '<?= $baseUrl ?>/login'; }, 2000);
                        }
                    } else {
                        this.errorMsg = json.message || 'Registration failed. Please try again.';
                    }
                } catch (e) {
                    this.errorMsg = 'Connection error. Please try again.';
                } finally {
                    this.loading = false;
                }
            },

            googleSignUp() {
                this.errorMsg = '';
                const client = google.accounts.oauth2.initCodeClient({
                    client_id: '<?= $_ENV["GOOGLE_CLIENT_ID"] ?? "" ?>',
                    scope: 'openid email profile',
                    ux_mode: 'popup',
                    callback: async (response) => {
                        if (response.error) { this.errorMsg = 'Google sign-in was cancelled.'; return; }
                        this.loading = true;
                        try {
                            const tokenRes = await fetch('https://oauth2.googleapis.com/token', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: new URLSearchParams({
                                    code: response.code,
                                    client_id: '<?= $_ENV["GOOGLE_CLIENT_ID"] ?? "" ?>',
                                    client_secret: '<?= $_ENV["GOOGLE_CLIENT_SECRET"] ?? "" ?>',
                                    redirect_uri: 'postmessage',
                                    grant_type: 'authorization_code'
                                })
                            });
                            const tokenData = await tokenRes.json();
                            if (!tokenData.id_token) { this.errorMsg = 'Google authentication failed.'; this.loading = false; return; }
                            const res = await fetch('/api/auth/google', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id_token: tokenData.id_token })
                            });
                            const json = await res.json();
                            if (json.status === 'success') {
                                const data = json.data || json;
                                localStorage.setItem('access_token', data.access_token);
                                localStorage.setItem('refresh_token', data.refresh_token);
                                this.successMsg = 'Account created! Redirecting...';
                                setTimeout(() => { window.location.href = '/admin'; }, 600);
                            } else {
                                this.errorMsg = json.message || 'Google sign-up failed. Please try again.';
                            }
                        } catch (e) {
                            this.errorMsg = 'Connection error. Please try again.';
                        } finally {
                            this.loading = false;
                        }
                    }
                });
                client.requestCode();
            }
        }
    }
    </script>
</body>
</html>
