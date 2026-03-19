<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email — K2 Pickleball</title>
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
</head>
<body class="h-full bg-surface-950 font-sans text-white antialiased flex items-center justify-center min-h-screen p-6" x-data="verifyEmailPage()" x-init="init()">

    <div class="w-full max-w-md text-center">
        <!-- Logo -->
        <a href="<?= $baseUrl ?>/" class="inline-flex items-center gap-3 mb-10">
            <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-xl font-bold">K2 Pickleball</span>
        </a>

        <!-- Verifying state -->
        <div x-show="status === 'verifying'" class="space-y-4">
            <div class="h-16 w-16 rounded-full bg-brand-500/10 flex items-center justify-center mx-auto">
                <svg class="animate-spin h-8 w-8 text-brand-400" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Verifying your email…</h1>
            <p class="text-surface-400 text-sm">Please wait while we verify your email address.</p>
        </div>

        <!-- Success state -->
        <div x-show="status === 'success'" x-transition class="space-y-4">
            <div class="h-16 w-16 rounded-full bg-brand-500/15 flex items-center justify-center mx-auto">
                <svg class="w-9 h-9 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl font-bold">Email verified!</h1>
            <p class="text-surface-400 text-sm">Your email address has been verified. A welcome email is on its way.</p>
            <a :href="'<?= $baseUrl ?>/login'" class="inline-flex items-center gap-2 mt-4 px-8 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                Sign In to Your Account
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <!-- Error state -->
        <div x-show="status === 'error'" x-transition class="space-y-4">
            <div class="h-16 w-16 rounded-full bg-red-500/15 flex items-center justify-center mx-auto">
                <svg class="w-9 h-9 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h1 class="text-2xl font-bold">Verification failed</h1>
            <p class="text-surface-400 text-sm" x-text="errorMessage">This link may have expired or already been used.</p>
            <p class="text-surface-500 text-sm">Enter your email to receive a new verification link.</p>

            <form @submit.prevent="resend" class="mt-4 space-y-3">
                <input type="email" x-model="resendEmail" required
                    placeholder="your@email.com"
                    class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors text-center">
                <button type="submit" :disabled="resending" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-2xl transition-all hover:-translate-y-0.5">
                    <span x-show="!resending">Resend Verification Email</span>
                    <span x-show="resending" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Sending…
                    </span>
                </button>
                <div x-show="resendSuccess" x-transition class="p-3 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm">
                    ✓ Verification email sent. Please check your inbox.
                </div>
            </form>

            <a href="<?= $baseUrl ?>/login" class="block mt-2 text-sm text-surface-400 hover:text-white transition-colors">Back to Sign In</a>
        </div>

        <!-- No-token state (page landed without token) -->
        <div x-show="status === 'no-token'" x-transition class="space-y-4">
            <div class="h-16 w-16 rounded-full bg-amber-500/15 flex items-center justify-center mx-auto">
                <svg class="w-9 h-9 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h1 class="text-2xl font-bold">Check your inbox</h1>
            <p class="text-surface-400 text-sm">We sent a verification link to your email address. Click the link to activate your account.</p>
            <p class="text-surface-500 text-xs mt-2">Didn't receive it? Check your spam folder or resend below.</p>

            <form @submit.prevent="resend" class="mt-4 space-y-3">
                <input type="email" x-model="resendEmail" required
                    placeholder="your@email.com"
                    class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors text-center">
                <button type="submit" :disabled="resending" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white bg-surface-800 hover:bg-surface-700 border border-surface-700 disabled:opacity-50 rounded-2xl transition-all">
                    <span x-show="!resending">Resend Verification Email</span>
                    <span x-show="resending">Sending…</span>
                </button>
                <div x-show="resendSuccess" x-transition class="p-3 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm">
                    ✓ Verification email sent. Please check your inbox.
                </div>
            </form>

            <a href="<?= $baseUrl ?>/login" class="block mt-2 text-sm text-surface-400 hover:text-white transition-colors">Back to Sign In</a>
        </div>
    </div>

    <script>
    function verifyEmailPage() {
        const baseUrl = '<?= $baseUrl ?>';
        // Read token from query string
        const params = new URLSearchParams(window.location.search);
        const token  = params.get('token') || '';
        const emailParam = params.get('email') || '';
        return {
            status: token ? 'verifying' : 'no-token',
            errorMessage: 'This verification link has expired or is invalid.',
            resendEmail: emailParam,
            resending: false,
            resendSuccess: false,
            async init() {
                if (!token) return;
                try {
                    const res  = await fetch(baseUrl + '/api/auth/verify-email?token=' + encodeURIComponent(token));
                    const data = await res.json();
                    if (res.ok) {
                        this.status = 'success';
                    } else {
                        this.errorMessage = data.message || 'This link has expired or is invalid.';
                        this.status = 'error';
                    }
                } catch(e) {
                    this.errorMessage = 'Network error. Please try again.';
                    this.status = 'error';
                }
            },
            async resend() {
                if (!this.resendEmail) return;
                this.resending = true;
                this.resendSuccess = false;
                try {
                    await fetch(baseUrl + '/api/auth/resend-verification', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.resendEmail })
                    });
                    this.resendSuccess = true;
                } catch(e) {}
                this.resending = false;
            }
        }
    }
    </script>
</body>
</html>
