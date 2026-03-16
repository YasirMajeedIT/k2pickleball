<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — K2 Pickleball</title>
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
    <div class="w-full max-w-md" x-data="resetPage()" x-init="init()">
        <div class="text-center mb-8">
            <a href="<?= $baseUrl ?>/" class="inline-flex items-center gap-3 mb-8">
                <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xl font-bold">K2 Pickleball</span>
            </a>
        </div>

        <!-- No token state -->
        <div x-show="!token" x-transition class="text-center">
            <div class="h-16 w-16 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold">Invalid Reset Link</h2>
            <p class="mt-2 text-sm text-surface-400">This password reset link is missing or invalid. Please request a new one.</p>
            <a href="<?= $baseUrl ?>/forgot-password" class="mt-6 inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all">Request New Link</a>
        </div>

        <!-- Reset form -->
        <div x-show="token && !done" x-transition>
            <h2 class="text-2xl font-extrabold text-center">Reset your password</h2>
            <p class="mt-2 text-sm text-surface-400 text-center">Enter your new password below.</p>

            <form @submit.prevent="doReset()" class="mt-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">New Password</label>
                    <input type="password" x-model="password" required minlength="8" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Min. 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Confirm Password</label>
                    <input type="password" x-model="confirmation" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Repeat your password">
                </div>
                <button type="submit" :disabled="loading" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-2xl shadow-lg shadow-brand-600/25 transition-all">
                    <span x-show="!loading">Reset Password</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Resetting...
                    </span>
                </button>
            </form>
        </div>

        <!-- Success state -->
        <div x-show="done" x-transition class="text-center">
            <div class="h-16 w-16 rounded-full bg-brand-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-brand-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold">Password reset</h2>
            <p class="mt-2 text-sm text-surface-400">Your password has been reset successfully. You can now sign in.</p>
            <a href="<?= $baseUrl ?>/login" class="mt-6 inline-flex items-center justify-center gap-2 px-8 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all">Sign In</a>
        </div>
    </div>

    <script>
    const BASE_URL = '<?= $baseUrl ?>';
    function resetPage() {
        return {
            password: '', confirmation: '', loading: false, done: false, token: '',
            showAlert(message, icon = 'error', title = 'Reset Password') {
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
                const params = new URLSearchParams(window.location.search);
                this.token = params.get('token') || '';
            },
            async doReset() {
                if (this.password.length < 8) {
                    await this.showAlert('Password must be at least 8 characters.', 'warning', 'Validation Error');
                    return;
                }
                if (this.password !== this.confirmation) {
                    await this.showAlert('Passwords do not match.', 'warning', 'Validation Error');
                    return;
                }
                this.loading = true;
                try {
                    const res = await fetch(BASE_URL + '/api/auth/reset-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token: this.token, password: this.password, password_confirmation: this.confirmation })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        await this.showAlert(data.message || 'Reset failed. The link may have expired.', 'error', 'Reset Failed');
                        this.loading = false;
                        return;
                    }
                    await this.showAlert('Your password has been reset successfully. You can now sign in.', 'success', 'Password Reset');
                    window.location.href = BASE_URL + '/login';
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
