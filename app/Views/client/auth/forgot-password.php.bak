<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<body class="h-full bg-surface-950 font-sans text-white antialiased flex items-center justify-center p-6">
    <div class="w-full max-w-md" x-data="forgotPage()" x-init="init()">
        <div class="text-center mb-8">
            <a href="<?= $baseUrl ?>/" class="inline-flex items-center gap-3 mb-8">
                <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xl font-bold">K2 Pickleball</span>
            </a>
        </div>

        <!-- Request form -->
        <div x-show="!sent" x-transition>
            <h2 class="text-2xl font-extrabold text-center">Forgot your password?</h2>
            <p class="mt-2 text-sm text-surface-400 text-center">Enter your email and we'll send you a reset link.</p>

            <form @submit.prevent="sendReset()" class="mt-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Email address</label>
                    <input type="email" x-model="email" required autofocus class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="you@example.com">
                </div>
                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-2xl shadow-lg shadow-brand-600/25 transition-all">
                    <span x-show="!loading">Send Reset Link</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Sending...
                    </span>
                </button>
            </form>
        </div>

        <!-- Success state -->
        <div x-show="sent" x-transition class="text-center">
            <div class="h-16 w-16 rounded-full bg-brand-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold">Check your email</h2>
            <p class="mt-2 text-sm text-surface-400">If an account exists for <span class="text-white" x-text="email"></span>, we've sent password reset instructions.</p>
            <button @click="sent = false; email = ''" class="mt-6 text-sm text-brand-400 hover:text-brand-300 font-medium">Try a different email</button>
        </div>

        <p class="mt-8 text-center text-sm text-surface-500">
            <a href="<?= $baseUrl ?>/login" class="text-brand-400 hover:text-brand-300 font-medium">&larr; Back to sign in</a>
        </p>
    </div>

    <script>
    const BASE_URL = '<?= $baseUrl ?>';
    function forgotPage() {
        return {
            email: '', loading: false, sent: false,
            showAlert(message, icon = 'success', title = 'Password Reset') {
                return Swal.fire({
                    title,
                    text: message,
                    icon,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#059669',
                    background: '#020617',
                    color: '#e2e8f0'
                });
            },
            init() {
                // Pre-fill email if passed as query param
                const params = new URLSearchParams(window.location.search);
                if (params.get('email')) this.email = params.get('email');
            },
            async sendReset() {
                this.loading = true;
                try {
                    await fetch(BASE_URL + '/api/auth/forgot-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: this.email })
                    });
                    await this.showAlert('If an account exists for this email, password reset instructions have been sent.', 'success', 'Reset Link Sent');
                } catch {
                    await this.showAlert('Network error. Please try again.', 'error', 'Connection Error');
                }
                this.loading = false;
            }
        }
    }
    </script>
</body>
</html>
