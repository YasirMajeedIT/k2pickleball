<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — K2Pickleball.com</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="bg-navy-950 text-white min-h-screen flex items-center justify-center px-6 py-12">
    <div class="absolute inset-0 grid-bg opacity-20"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[300px] bg-gold-500/5 rounded-full blur-[120px]"></div>

    <div class="relative w-full max-w-md text-center" x-data="verifyEmailForm()">
        <div class="flex items-center justify-center gap-2 mb-10">
            <svg class="w-8 h-8 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
            </svg>
            <span class="font-display text-xl font-extrabold">K2 <span class="gradient-gold">Pickleball</span></span>
        </div>

        <!-- Verifying state -->
        <div x-show="verifying" class="text-center">
            <div class="h-16 w-16 rounded-full bg-gold-500/10 flex items-center justify-center mx-auto mb-6 animate-pulse">
                <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>
            <h1 class="font-display text-3xl font-extrabold text-white">Verifying Your Email...</h1>
            <p class="mt-3 text-sm text-slate-400">Please wait while we verify your email address.</p>
        </div>

        <!-- Success state -->
        <div x-show="verified" x-cloak class="text-center">
            <div class="h-16 w-16 rounded-full bg-gold-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
            </div>
            <h1 class="font-display text-3xl font-extrabold text-white">Email Verified!</h1>
            <p class="mt-3 text-sm text-slate-400">Your email has been verified successfully. You can now sign in to your account.</p>
            <a href="<?= $baseUrl ?>/login" class="mt-6 inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300">
                Sign In
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <!-- Waiting / No token state -->
        <div x-show="!verifying && !verified" x-cloak>
            <!-- Error message -->
            <div x-show="errorMsg" class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm text-center" x-text="errorMsg"></div>

            <div class="h-16 w-16 rounded-full bg-gold-500/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>

            <h1 class="font-display text-3xl font-extrabold text-white">Verify Your Email</h1>
            <p class="mt-3 text-sm text-slate-400 max-w-sm mx-auto leading-relaxed">
                We've sent a verification link to your email address. Please check your inbox and click the link to activate your account.
            </p>

            <div class="mt-8 p-5 rounded-xl bg-navy-900/40 border border-navy-700/40 text-left">
                <h3 class="text-sm font-bold text-white mb-2">Didn't receive the email?</h3>
                <ul class="space-y-1 text-xs text-slate-400">
                    <li>• Check your spam or junk folder</li>
                    <li>• Make sure you entered the correct email</li>
                    <li>• The email may take a few minutes to arrive</li>
                </ul>
            </div>

            <div class="mt-6">
                <div x-show="!showResendForm" class="space-y-3">
                    <button @click="showResendForm = true" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300">
                        Resend Verification Email
                    </button>
                </div>
                <div x-show="showResendForm" x-cloak class="space-y-3">
                    <input type="email" x-model="resendEmail" placeholder="Enter your email address" class="w-full px-4 py-3 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm">
                    <button @click="resendVerification()" :disabled="resending" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-lg shadow-gold-500/20 hover:shadow-gold-500/30 transition-all duration-300 disabled:opacity-70">
                        <span x-show="!resending && !resent">Send</span>
                        <span x-show="resending" x-cloak>Sending...</span>
                        <span x-show="resent" x-cloak>Email Sent!</span>
                    </button>
                </div>
            </div>
        </div>

        <p class="mt-8 text-sm text-slate-500">
            <a href="<?= $baseUrl ?>/login" class="font-medium text-gold-500 hover:text-gold-400 transition-colors">&larr; Back to sign in</a>
        </p>
    </div>

    <script>
    function verifyEmailForm() {
        const params = new URLSearchParams(window.location.search);
        const token = params.get('token') || '';
        return {
            verifying: !!token,
            verified: false,
            errorMsg: '',
            showResendForm: false,
            resendEmail: '',
            resending: false,
            resent: false,

            init() {
                if (token) {
                    this.verifyToken(token);
                }
            },

            async verifyToken(t) {
                try {
                    const res = await fetch('/api/auth/verify-email?token=' + encodeURIComponent(t), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    if (json.status === 'success') {
                        this.verified = true;
                    } else {
                        this.errorMsg = json.message || 'Verification failed. The link may have expired.';
                    }
                } catch (e) {
                    this.errorMsg = 'Network error. Please check your connection and try again.';
                } finally {
                    this.verifying = false;
                }
            },

            async resendVerification() {
                this.errorMsg = '';
                if (!this.resendEmail) {
                    this.errorMsg = 'Please enter your email address.';
                    return;
                }
                this.resending = true;
                try {
                    const res = await fetch('/api/auth/resend-verification', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ email: this.resendEmail })
                    });
                    const json = await res.json();
                    if (json.status === 'success') {
                        this.resent = true;
                    } else {
                        this.errorMsg = json.message || 'Failed to resend. Please try again.';
                    }
                } catch (e) {
                    this.errorMsg = 'Network error. Please check your connection and try again.';
                } finally {
                    this.resending = false;
                }
            }
        };
    }
    </script>
</body>
</html>
