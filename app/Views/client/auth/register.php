<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — K2 Pickleball</title>
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
<body class="h-full bg-surface-950 font-sans text-white antialiased" x-data="registerPage()">
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
                <h1 class="text-3xl font-extrabold leading-tight">Start managing your<br><span class="gradient-text">facility today</span></h1>
                <p class="mt-4 text-surface-400">Create your free account in minutes. No credit card required.</p>

                <!-- Steps indicator -->
                <div class="mt-12 space-y-6">
                    <template x-for="(s, i) in stepLabels" :key="i">
                        <div class="flex items-center gap-4">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors" :class="step > i + 1 ? 'bg-brand-600 text-white' : step === i + 1 ? 'bg-brand-500/20 border border-brand-500 text-brand-400' : 'bg-surface-800 text-surface-500'">
                                <template x-if="step > i + 1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                </template>
                                <template x-if="step <= i + 1">
                                    <span x-text="i + 1"></span>
                                </template>
                            </div>
                            <span class="text-sm" :class="step === i + 1 ? 'text-white font-medium' : 'text-surface-500'" x-text="s"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right panel - form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-lg">
                <!-- Mobile logo -->
                <div class="lg:hidden mb-8">
                    <a href="<?= $baseUrl ?>/" class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xl font-bold">K2 Pickleball</span>
                    </a>
                </div>

                <!-- Mobile step indicator -->
                <div class="lg:hidden flex items-center gap-2 mb-6">
                    <template x-for="i in 3" :key="i">
                        <div class="h-1.5 flex-1 rounded-full transition-colors" :class="step >= i ? 'bg-brand-500' : 'bg-surface-800'"></div>
                    </template>
                </div>

                <!-- Error -->
                <div x-show="error" x-transition class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm" x-text="error"></div>

                <!-- Step 1: Account -->
                <div x-show="step === 1" x-transition>
                    <h2 class="text-2xl font-extrabold">Create your account</h2>
                    <p class="mt-2 text-sm text-surface-400">
                        Already have an account?
                        <a href="<?= $baseUrl ?>/login" class="text-brand-400 hover:text-brand-300 font-medium">Sign in</a>
                    </p>

                    <form @submit.prevent="nextStep" class="mt-8 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-surface-300 mb-2">First Name</label>
                                <input type="text" x-model="form.first_name" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="John">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-300 mb-2">Last Name</label>
                                <input type="text" x-model="form.last_name" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Doe">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Email</label>
                            <input type="email" x-model="form.email" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="john@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Password</label>
                            <input type="password" x-model="form.password" required minlength="8" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Min. 8 characters">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Confirm Password</label>
                            <input type="password" x-model="form.password_confirmation" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Repeat your password">
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                            Continue
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Step 2: Organization -->
                <div x-show="step === 2" x-transition>
                    <h2 class="text-2xl font-extrabold">Set up your organization</h2>
                    <p class="mt-2 text-sm text-surface-400">Tell us about your facility so we can personalize your experience.</p>

                    <form @submit.prevent="nextStep" class="mt-8 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Organization Name</label>
                            <input type="text" x-model="form.org_name" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Ace Pickleball Club">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Slug (URL identifier)</label>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-surface-500">k2pickleball.com/</span>
                                <input type="text" x-model="form.org_slug" required pattern="[a-z0-9\-]+" class="flex-1 px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="ace-pickleball">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Phone (optional)</label>
                            <input type="tel" x-model="form.org_phone" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="(555) 123-4567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Address (optional)</label>
                            <input type="text" x-model="form.org_address" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="123 Main St, City, State">
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="step = 1" class="px-6 py-3.5 text-base font-medium text-surface-300 hover:text-white border border-surface-700 hover:border-surface-600 rounded-2xl transition-all">Back</button>
                            <button type="submit" class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                                Continue
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Plan Selection -->
                <div x-show="step === 3" x-transition>
                    <h2 class="text-2xl font-extrabold">Choose your plan</h2>
                    <p class="mt-2 text-sm text-surface-400">Start free and upgrade anytime. No credit card required.</p>

                    <div class="mt-8 space-y-4">
                        <template x-for="plan in plans" :key="plan.id">
                            <label class="block cursor-pointer">
                                <div class="p-5 rounded-2xl border transition-all" :class="form.plan_id == plan.id ? 'border-brand-500/60 bg-brand-500/5' : 'border-surface-700/60 bg-surface-900/30 hover:border-surface-600'">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="plan" :value="plan.id" x-model="form.plan_id" class="text-brand-600 focus:ring-brand-500 bg-surface-900 border-surface-600">
                                            <div>
                                                <div class="text-base font-semibold text-white" x-text="plan.name"></div>
                                                <div class="text-xs text-surface-400 mt-0.5" x-text="plan.description"></div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-bold text-white" x-text="plan.price_monthly == 0 ? 'Free' : '$' + Math.round(plan.price_monthly)"></div>
                                            <div x-show="plan.price_monthly > 0" class="text-xs text-surface-500">/month</div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </template>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="step = 2" class="px-6 py-3.5 text-base font-medium text-surface-300 hover:text-white border border-surface-700 hover:border-surface-600 rounded-2xl transition-all">Back</button>
                        <button @click="submitRegistration" :disabled="loading" class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                            <span x-show="!loading">Create Account</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                Creating account...
                            </span>
                        </button>
                    </div>
                </div>

                <p class="mt-8 text-center text-xs text-surface-500">
                    By creating an account, you agree to our Terms of Service and Privacy Policy.
                </p>
            </div>
        </div>
    </div>

    <script>
    function registerPage() {
        const baseUrl = '<?= $baseUrl ?>';
        return {
            step: 1,
            stepLabels: ['Create your account', 'Set up organization', 'Choose your plan'],
            form: {
                first_name: '', last_name: '', email: '', password: '', password_confirmation: '',
                org_name: '', org_slug: '', org_phone: '', org_address: '',
                plan_id: null
            },
            plans: [],
            loading: false,
            error: '',
            init() {
                // Fetch plans
                fetch(baseUrl + '/api/plans')
                    .then(r => r.json())
                    .then(data => {
                        const planData = data.data || data;
                        this.plans = planData.map(p => ({
                            ...p,
                            price_monthly: parseFloat(p.price_monthly) || 0,
                            description: p.description || ''
                        }));
                        // Pre-select free plan
                        const free = this.plans.find(p => p.price_monthly === 0);
                        if (free) this.form.plan_id = free.id;
                    })
                    .catch(() => {
                        this.plans = [
                            { id: 1, name: 'Free', description: 'Up to 4 courts, 10 users', price_monthly: 0 },
                            { id: 2, name: 'Professional', description: '20 courts, 100 users, payments', price_monthly: 49.99 },
                            { id: 3, name: 'Enterprise', description: 'Unlimited everything', price_monthly: 149.99 }
                        ];
                        this.form.plan_id = 1;
                    });
            },
            nextStep() {
                this.error = '';
                if (this.step === 1) {
                    if (this.form.password !== this.form.password_confirmation) {
                        this.error = 'Passwords do not match.';
                        return;
                    }
                    if (this.form.password.length < 8) {
                        this.error = 'Password must be at least 8 characters.';
                        return;
                    }
                }
                if (this.step === 2) {
                    // Auto-generate slug if empty
                    if (!this.form.org_slug) {
                        this.form.org_slug = this.form.org_name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                    }
                }
                this.step++;
            },
            async submitRegistration() {
                this.error = '';
                this.loading = true;
                try {
                    // Step 1: Register user
                    const regRes = await fetch(baseUrl + '/api/auth/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            first_name: this.form.first_name,
                            last_name: this.form.last_name,
                            email: this.form.email,
                            password: this.form.password,
                            password_confirmation: this.form.password_confirmation
                        })
                    });
                    const regData = await regRes.json();
                    if (!regRes.ok) {
                        this.error = regData.error || regData.message || 'Registration failed.';
                        this.loading = false;
                        return;
                    }

                    // Step 2: Login to get tokens
                    const loginRes = await fetch(baseUrl + '/api/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.form.email, password: this.form.password })
                    });
                    const loginData = await loginRes.json();
                    if (!loginRes.ok) {
                        this.error = 'Account created but login failed. Please sign in manually.';
                        this.loading = false;
                        setTimeout(() => window.location.href = baseUrl + '/login', 2000);
                        return;
                    }
                    const token = loginData.access_token;
                    localStorage.setItem('access_token', token);
                    localStorage.setItem('refresh_token', loginData.refresh_token);
                    if (loginData.user) localStorage.setItem('user', JSON.stringify(loginData.user));

                    // Step 3: Create organization
                    const orgRes = await fetch(baseUrl + '/api/organizations', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                        body: JSON.stringify({
                            name: this.form.org_name,
                            slug: this.form.org_slug,
                            phone: this.form.org_phone || undefined,
                            address: this.form.org_address || undefined
                        })
                    });
                    const orgData = await orgRes.json();

                    // Step 4: Create subscription
                    if (orgRes.ok && this.form.plan_id) {
                        const orgId = orgData.data?.id || orgData.id;
                        await fetch(baseUrl + '/api/subscriptions', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                            body: JSON.stringify({
                                organization_id: orgId,
                                plan_id: this.form.plan_id,
                                billing_cycle: 'monthly'
                            })
                        });
                    }

                    // Redirect to portal
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
