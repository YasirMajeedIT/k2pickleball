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
    <?php
        $squareEnv = $_ENV['SQUARE_ENVIRONMENT'] ?? 'sandbox';
        $squareAppId = $_ENV['SQUARE_APPLICATION_ID'] ?? '';
        $squareLocationId = $_ENV['SQUARE_LOCATION_ID'] ?? '';
        $squareSdkUrl = $squareEnv === 'production'
            ? 'https://web.squarecdn.com/v1/square.js'
            : 'https://sandbox.web.squarecdn.com/v1/square.js';
    ?>
    <script src="<?= htmlspecialchars($squareSdkUrl, ENT_QUOTES, 'UTF-8') ?>" async defer></script>
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
        .shadow-gold { box-shadow: 0 4px 25px -5px rgba(212,175,55,0.2); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-navy-950 text-white min-h-screen">
    <div class="min-h-screen flex" x-data="registerWizard()" x-init="init()">

        <!-- Left Panel — Branding -->
        <div class="hidden lg:flex lg:w-5/12 relative bg-navy-900 items-center justify-center overflow-hidden">
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
                <p class="text-slate-400 leading-relaxed max-w-sm mx-auto">Create your organization account and start managing your pickleball facility with full platform access.</p>

                <!-- Steps indicator -->
                <div class="mt-10 space-y-4 text-left max-w-xs mx-auto">
                    <template x-for="(s, idx) in stepLabels" :key="idx">
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                 :class="step > idx + 1 ? 'bg-gold-500 text-navy-950' : step === idx + 1 ? 'bg-gold-500/20 border-2 border-gold-500 text-gold-400' : 'bg-navy-800 text-slate-500'">
                                <span x-show="step <= idx + 1" x-text="idx + 1"></span>
                                <svg x-show="step > idx + 1" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            </div>
                            <span :class="step === idx + 1 ? 'text-white font-semibold' : step > idx + 1 ? 'text-gold-400' : 'text-slate-500'" x-text="s"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right Panel — Wizard Steps -->
        <div class="w-full lg:w-7/12 flex items-start justify-center px-6 py-8 overflow-y-auto">
            <div class="w-full max-w-xl">
                <!-- Mobile header -->
                <div class="lg:hidden flex items-center justify-center gap-2 mb-8">
                    <svg class="w-8 h-8 text-gold-500" viewBox="0 0 40 40" fill="currentColor">
                        <path d="M20 2L23 14L35 14L25 22L28 34L20 27L12 34L15 22L5 14L17 14Z"/>
                    </svg>
                    <span class="text-xl font-extrabold">K2 <span class="gradient-gold">Pickleball</span></span>
                </div>

                <!-- Mobile step indicators -->
                <div class="lg:hidden flex items-center justify-center gap-2 mb-6">
                    <template x-for="i in 3" :key="i">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                                 :class="step > i ? 'bg-gold-500 text-navy-950' : step === i ? 'bg-gold-500/20 border-2 border-gold-500 text-gold-400' : 'bg-navy-800 text-slate-500'"
                                 x-text="i"></div>
                            <div x-show="i < 3" class="w-8 h-0.5" :class="step > i ? 'bg-gold-500' : 'bg-navy-700'"></div>
                        </div>
                    </template>
                </div>

                <!-- Error/Success Messages -->
                <div x-show="errorMsg" x-cloak class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/30 text-sm text-red-400" x-text="errorMsg"></div>
                <div x-show="successMsg" x-cloak class="mb-4 p-3 rounded-xl bg-green-500/10 border border-green-500/30 text-sm text-green-400" x-text="successMsg"></div>

                <!-- ═══════════ STEP 1: Choose Plan ═══════════ -->
                <div x-show="step === 1" x-cloak>
                    <h1 class="text-2xl font-extrabold text-white mb-1">Choose Your Plan</h1>
                    <p class="text-sm text-slate-400 mb-6">Select a plan to get started with K2Pickleball.</p>

                    <!-- Billing Toggle -->
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <span class="text-sm font-medium" :class="billingCycle === 'monthly' ? 'text-white' : 'text-slate-500'">Monthly</span>
                        <button type="button" @click="billingCycle = billingCycle === 'monthly' ? 'yearly' : 'monthly'" class="relative w-14 h-7 rounded-full transition-colors duration-300" :class="billingCycle === 'yearly' ? 'bg-gold-500' : 'bg-navy-700'">
                            <span class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow transition-transform duration-300" :class="billingCycle === 'yearly' ? 'translate-x-7' : ''"></span>
                        </button>
                        <span class="text-sm font-medium" :class="billingCycle === 'yearly' ? 'text-white' : 'text-slate-500'">Yearly <span class="text-gold-400 text-xs font-semibold">(Save up to 20%)</span></span>
                    </div>

                    <!-- Loading -->
                    <div x-show="plansLoading" class="text-center py-12">
                        <svg class="animate-spin h-8 w-8 text-gold-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>

                    <!-- Plans Grid -->
                    <div x-show="!plansLoading" class="grid gap-4" :class="plans.length > 2 ? 'md:grid-cols-2 lg:grid-cols-3' : 'md:grid-cols-2'">
                        <template x-for="plan in plans" :key="plan.id">
                            <div @click="selectPlan(plan)" class="relative rounded-xl p-5 cursor-pointer transition-all duration-300 border-2"
                                 :class="selectedPlan && selectedPlan.id === plan.id
                                    ? 'border-gold-500 bg-gold-500/5 shadow-gold'
                                    : 'border-navy-700/50 bg-navy-900/40 hover:border-navy-600'">
                                <!-- Selected check -->
                                <div x-show="selectedPlan && selectedPlan.id === plan.id" x-cloak class="absolute top-3 right-3">
                                    <svg class="w-6 h-6 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                </div>

                                <h3 class="font-bold text-lg text-white" x-text="plan.name"></h3>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2" x-text="plan.description"></p>

                                <div class="mt-3 flex items-baseline gap-1">
                                    <span class="text-2xl font-extrabold gradient-gold" x-text="'$' + getPrice(plan)"></span>
                                    <span class="text-slate-400 text-xs">/<span x-text="billingCycle === 'yearly' ? 'year' : 'month'"></span></span>
                                </div>
                                <p x-show="billingCycle === 'yearly'" x-cloak class="text-xs text-gold-400 mt-0.5" x-text="'$' + (parseFloat(plan.price_yearly) / 12).toFixed(2) + '/mo'"></p>

                                <!-- Features -->
                                <ul class="mt-3 space-y-1.5">
                                    <template x-for="feature in getPlanFeatures(plan)" :key="feature">
                                        <li class="flex items-start gap-1.5 text-xs">
                                            <svg class="w-3.5 h-3.5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                            <span class="text-slate-300" x-text="feature"></span>
                                        </li>
                                    </template>
                                    <li x-show="plan.max_facilities" class="flex items-start gap-1.5 text-xs">
                                        <svg class="w-3.5 h-3.5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                        <span class="text-slate-300" x-text="'Up to ' + plan.max_facilities + ' facilities'"></span>
                                    </li>
                                    <li x-show="plan.max_courts" class="flex items-start gap-1.5 text-xs">
                                        <svg class="w-3.5 h-3.5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                        <span class="text-slate-300" x-text="'Up to ' + plan.max_courts + ' courts'"></span>
                                    </li>
                                    <li x-show="plan.max_users" class="flex items-start gap-1.5 text-xs">
                                        <svg class="w-3.5 h-3.5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                        <span class="text-slate-300" x-text="'Up to ' + plan.max_users + ' staff'"></span>
                                    </li>
                                </ul>
                            </div>
                        </template>
                    </div>

                    <p x-show="!plansLoading && plans.length === 0" class="text-center text-slate-400 py-8">No plans available. Please contact support.</p>

                    <button @click="goToStep(2)" :disabled="!selectedPlan" class="mt-6 w-full py-3.5 text-sm font-bold rounded-xl transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="selectedPlan ? 'text-navy-950 gradient-gold-bg shadow-gold hover:shadow-lg' : 'bg-navy-800 text-slate-500'">
                        Continue with <span x-text="selectedPlan ? selectedPlan.name : '...'"></span> Plan
                    </button>
                </div>

                <!-- ═══════════ STEP 2: Account Details ═══════════ -->
                <div x-show="step === 2" x-cloak>
                    <div class="flex items-center gap-3 mb-6">
                        <button @click="step = 1" class="text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-extrabold text-white">Create Your Account</h1>
                            <p class="text-sm text-slate-400">Set up your organization and admin account.</p>
                        </div>
                    </div>

                    <!-- Selected plan summary -->
                    <div class="mb-6 p-3 rounded-xl bg-navy-900/60 border border-navy-700/40 flex items-center justify-between">
                        <div>
                            <span class="text-sm font-semibold text-white" x-text="selectedPlan?.name + ' Plan'"></span>
                            <span class="text-xs text-slate-400 ml-2" x-text="'$' + getPrice(selectedPlan) + '/' + (billingCycle === 'yearly' ? 'year' : 'month')"></span>
                        </div>
                        <button @click="step = 1" class="text-xs text-gold-500 hover:text-gold-400">Change</button>
                    </div>

                    <!-- Google Sign Up -->
                    <button @click="googleSignUp()" :disabled="loading" class="w-full flex items-center justify-center gap-3 px-6 py-3 text-sm font-medium text-white bg-navy-800/60 border border-navy-700/60 rounded-xl hover:bg-navy-800 hover:border-gold-500/30 transition-all duration-300 disabled:opacity-70">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Pre-fill with Google
                    </button>
                    <div id="google-btn-fallback" style="display:none" class="mt-3 flex justify-center"></div>

                    <div class="my-5 flex items-center gap-3">
                        <div class="flex-1 h-px bg-navy-700/60"></div>
                        <span class="text-xs text-slate-500 uppercase tracking-wider">or fill manually</span>
                        <div class="flex-1 h-px bg-navy-700/60"></div>
                    </div>

                    <form class="space-y-4" @submit.prevent="goToStep(3)">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">First Name</label>
                                <input type="text" x-model="firstName" required class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="John">
                                <p x-show="errors.firstName" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.firstName"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Last Name</label>
                                <input type="text" x-model="lastName" required class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Smith">
                                <p x-show="errors.lastName" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.lastName"></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                            <input type="email" x-model="email" required autocomplete="email" class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="you@example.com">
                            <p x-show="errors.email" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.email"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Phone Number</label>
                            <input type="tel" x-model="phone" required class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="(555) 123-4567">
                            <p x-show="errors.phone" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.phone"></p>
                        </div>

                        <div class="pt-2 pb-1">
                            <div class="flex items-center gap-2">
                                <div class="h-px flex-1 bg-navy-700/60"></div>
                                <span class="text-xs text-gold-500 uppercase tracking-wider font-semibold">Organization Details</span>
                                <div class="h-px flex-1 bg-navy-700/60"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Organization Name</label>
                            <input type="text" x-model="orgName" @input="autoSlug()" required class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="e.g. Tampa Bay Pickleball Club">
                            <p x-show="errors.orgName" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.orgName"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Organization Slug</label>
                            <div class="relative">
                                <input type="text" x-model="orgSlug" required class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="tampa-bay-pickleball">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500">.k2pickleball.com</div>
                            </div>
                            <p x-show="errors.orgSlug" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.orgSlug"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" x-model="password" required autocomplete="new-password" class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm pr-11" placeholder="Minimum 8 characters">
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                </button>
                            </div>
                            <p x-show="errors.password" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.password"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Confirm Password</label>
                            <input type="password" x-model="passwordConfirmation" required autocomplete="new-password" class="w-full px-4 py-2.5 rounded-xl bg-navy-900/60 border border-navy-700/60 text-white placeholder-slate-500 focus:border-gold-500/40 focus:ring-1 focus:ring-gold-500/20 transition-colors text-sm" placeholder="Repeat your password">
                            <p x-show="errors.passwordConfirmation" x-cloak class="mt-1 text-xs text-red-400" x-text="errors.passwordConfirmation"></p>
                        </div>

                        <div class="flex items-start gap-2">
                            <input type="checkbox" x-model="agreed" id="terms" class="w-4 h-4 mt-0.5 rounded border-navy-700 bg-navy-900/60 text-gold-500 focus:ring-gold-500/20">
                            <label for="terms" class="text-sm text-slate-400">I agree to the <a href="<?= $baseUrl ?>/terms" class="text-gold-500 hover:text-gold-400">Terms of Service</a> and <a href="<?= $baseUrl ?>/privacy-policy" class="text-gold-500 hover:text-gold-400">Privacy Policy</a>.</label>
                        </div>
                        <p x-show="errors.agreed" x-cloak class="text-xs text-red-400" x-text="errors.agreed"></p>

                        <button type="submit" class="w-full py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-lg transition-all duration-300">
                            Continue to Payment
                        </button>
                    </form>
                </div>

                <!-- ═══════════ STEP 3: Payment ═══════════ -->
                <div x-show="step === 3" x-cloak>
                    <div class="flex items-center gap-3 mb-6">
                        <button @click="step = 2" class="text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-extrabold text-white">Complete Payment</h1>
                            <p class="text-sm text-slate-400">Secure payment via Square.</p>
                        </div>
                    </div>

                    <!-- Order summary -->
                    <div class="mb-6 p-4 rounded-xl bg-navy-900/60 border border-navy-700/40">
                        <h3 class="font-semibold text-white text-sm mb-3">Order Summary</h3>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-400" x-text="selectedPlan?.name + ' Plan (' + billingCycle + ')'"></span>
                            <span class="text-white font-semibold" x-text="'$' + getPrice(selectedPlan)"></span>
                        </div>
                        <div class="border-t border-navy-700/40 pt-2 mt-2 flex justify-between">
                            <span class="font-semibold text-white text-sm">Total Due Now</span>
                            <span class="font-bold text-gold-400 text-lg" x-text="'$' + getPrice(selectedPlan)"></span>
                        </div>
                    </div>

                    <!-- Account summary -->
                    <div class="mb-6 p-3 rounded-xl bg-navy-900/40 border border-navy-700/30 text-xs text-slate-400 space-y-1">
                        <div><span class="text-slate-500">Account:</span> <span class="text-white" x-text="firstName + ' ' + lastName + ' (' + email + ')'"></span></div>
                        <div><span class="text-slate-500">Organization:</span> <span class="text-white" x-text="orgName"></span></div>
                    </div>

                    <!-- Square Card Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Card Details</label>
                        <div class="relative">
                            <!-- Loading spinner overlay -->
                            <div x-show="cardLoading" class="rounded-xl border border-navy-700/60 bg-navy-900/60 min-h-[60px] flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-gold-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span class="text-sm text-slate-400">Loading payment form...</span>
                            </div>
                            <!-- Actual Square card container (hidden while loading) -->
                            <div x-show="!cardLoading" id="sq-card" class="rounded-xl overflow-hidden border border-navy-700/60 bg-navy-900/60 p-1 min-h-[60px]"></div>
                        </div>
                        <p x-show="cardError" x-cloak class="mt-1 text-xs text-red-400" x-text="cardError"></p>
                    </div>

                    <button @click="submitRegistration()" :disabled="loading" class="w-full flex items-center justify-center gap-2 py-3.5 text-sm font-bold text-navy-950 gradient-gold-bg rounded-xl shadow-gold hover:shadow-lg transition-all duration-300 disabled:opacity-70">
                        <svg x-show="loading" x-cloak class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-show="!loading">Pay <span x-text="'$' + getPrice(selectedPlan)"></span> & Create Account</span>
                        <span x-show="loading" x-cloak>Processing payment...</span>
                    </button>

                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Secured by Square. Your card details are never stored on our servers.
                    </div>
                </div>

                <p class="mt-8 text-center text-sm text-slate-500">
                    Already have an account? <a href="<?= $baseUrl ?>/login" class="font-medium text-gold-500 hover:text-gold-400 transition-colors">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    function registerWizard() {
        return {
            step: 1,
            stepLabels: ['Choose Your Plan', 'Account Details', 'Payment'],
            plans: [],
            plansLoading: true,
            selectedPlan: null,
            billingCycle: 'monthly',
            firstName: '', lastName: '', email: '', phone: '',
            orgName: '', orgSlug: '',
            password: '', passwordConfirmation: '',
            showPassword: false, agreed: false,
            googleCredential: '',
            sqCard: null, cardError: '', cardLoading: false,
            loading: false, errorMsg: '', successMsg: '', errors: {},

            async init() {
                await this.loadPlans();
                const params = new URLSearchParams(window.location.search);
                const planSlug = params.get('plan');
                if (planSlug) {
                    const match = this.plans.find(p => p.slug === planSlug);
                    if (match) { this.selectedPlan = match; this.step = 2; }
                }
                if (params.get('cycle') === 'yearly') this.billingCycle = 'yearly';

                // Pre-fill from Google redirect (when login detects unregistered Google user)
                if (params.get('google_email')) {
                    this.email = params.get('google_email') || '';
                    this.firstName = params.get('google_first') || '';
                    this.lastName = params.get('google_last') || '';
                    this.orgName = this.firstName ? this.firstName + "'s Organization" : '';
                    this.orgSlug = this.orgName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                    this.successMsg = 'Your Google profile has been pre-filled. Please choose a plan, set a password, and complete registration.';
                }
            },

            async loadPlans() {
                this.plansLoading = true;
                try {
                    const res = await fetch('/api/plans');
                    const json = await res.json();
                    const allPlans = json.data || [];
                    this.plans = allPlans.filter(p => p.is_active && (parseFloat(p.price_monthly) > 0 || parseFloat(p.price_yearly) > 0));
                } catch (e) {
                    this.errorMsg = 'Could not load plans. Please refresh the page.';
                } finally {
                    this.plansLoading = false;
                }
            },

            selectPlan(plan) { this.selectedPlan = plan; },

            getPrice(plan) {
                if (!plan) return '0.00';
                return this.billingCycle === 'yearly'
                    ? parseFloat(plan.price_yearly).toFixed(2)
                    : parseFloat(plan.price_monthly).toFixed(2);
            },

            getPlanFeatures(plan) {
                if (!plan.features) return [];
                try {
                    const f = typeof plan.features === 'string' ? JSON.parse(plan.features) : plan.features;
                    return Array.isArray(f) ? f : [];
                } catch { return []; }
            },

            autoSlug() {
                this.orgSlug = this.orgName.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-')
                    .replace(/-+/g, '-').replace(/^-|-$/g, '');
            },

            validateStep2() {
                this.errors = {};
                if (!this.firstName.trim()) this.errors.firstName = 'First name is required';
                if (!this.lastName.trim()) this.errors.lastName = 'Last name is required';
                if (!this.email.trim()) this.errors.email = 'Email is required';
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) this.errors.email = 'Enter a valid email';
                if (!this.phone.trim()) this.errors.phone = 'Phone number is required';
                if (!this.orgName.trim()) this.errors.orgName = 'Organization name is required';
                if (!this.orgSlug.trim()) this.errors.orgSlug = 'Organization slug is required';
                else if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(this.orgSlug)) this.errors.orgSlug = 'Only lowercase letters, numbers, and hyphens';
                if (!this.password) this.errors.password = 'Password is required';
                else if (this.password.length < 8) this.errors.password = 'Minimum 8 characters';
                if (this.password !== this.passwordConfirmation) this.errors.passwordConfirmation = 'Passwords do not match';
                if (!this.agreed) this.errors.agreed = 'You must agree to the terms';
                return Object.keys(this.errors).length === 0;
            },

            async goToStep(n) {
                this.errorMsg = '';
                if (n === 2 && !this.selectedPlan) { this.errorMsg = 'Please select a plan first.'; return; }
                if (n === 3 && !this.validateStep2()) return;
                this.step = n;
                if (n === 3) this.$nextTick(() => this.initSquareCard());
            },

            async initSquareCard() {
                if (this.sqCard) return;
                const appId = '<?= htmlspecialchars($squareAppId, ENT_QUOTES, "UTF-8") ?>';
                const locationId = '<?= htmlspecialchars($squareLocationId, ENT_QUOTES, "UTF-8") ?>';
                if (!appId) { this.cardError = 'Square is not configured.'; this.cardLoading = false; return; }

                this.cardLoading = true;
                this.cardError = '';

                const waitForSquare = () => new Promise((resolve, reject) => {
                    if (window.Square) return resolve();
                    let tries = 0;
                    const iv = setInterval(() => {
                        tries++;
                        if (window.Square) { clearInterval(iv); resolve(); }
                        else if (tries > 50) { clearInterval(iv); reject(new Error('Square SDK timeout')); }
                    }, 200);
                });

                try {
                    await waitForSquare();
                    const payments = window.Square.payments(appId, locationId);
                    this.sqCard = await payments.card();
                    this.cardLoading = false;
                    await this.$nextTick();
                    await this.sqCard.attach('#sq-card');
                } catch (e) {
                    this.cardError = 'Could not load payment form. Please refresh.';
                    this.cardLoading = false;
                    console.error('Square init:', e);
                }
            },

            async submitRegistration() {
                this.errorMsg = ''; this.cardError = '';
                if (!this.sqCard) { this.cardError = 'Payment form is not ready.'; return; }
                this.loading = true;
                try {
                    const tokenResult = await this.sqCard.tokenize();
                    if (tokenResult.status !== 'OK') {
                        this.cardError = tokenResult.errors?.[0]?.message || 'Invalid card details.';
                        this.loading = false; return;
                    }
                    const res = await fetch('/api/auth/register-with-payment', {
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
                            organization_slug: this.orgSlug.trim(),
                            plan_id: this.selectedPlan.id,
                            billing_cycle: this.billingCycle,
                            source_id: tokenResult.token,
                        })
                    });
                    const json = await res.json();
                    if (json.status === 'success') {
                        this.successMsg = 'Account created and payment processed! Redirecting...';
                        this.errorMsg = '';
                        const loginRes = await fetch('/api/auth/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ email: this.email.trim(), password: this.password })
                        });
                        const loginJson = await loginRes.json();
                        if (loginJson.status === 'success') {
                            const d = loginJson.data || loginJson;
                            localStorage.setItem('access_token', d.access_token);
                            localStorage.setItem('refresh_token', d.refresh_token);
                            setTimeout(() => { window.location.href = '/admin'; }, 800);
                        } else {
                            setTimeout(() => { window.location.href = '<?= $baseUrl ?>/login'; }, 1500);
                        }
                    } else {
                        if (json.errors && typeof json.errors === 'object') {
                            const fieldErrs = Object.entries(json.errors);
                            const step2Fields = ['first_name','last_name','email','phone','password','password_confirmation','organization_name','organization_slug'];
                            const hasStep2Err = fieldErrs.some(([k]) => step2Fields.includes(k));
                            if (hasStep2Err) {
                                this.errors = {};
                                const keyMap = { first_name:'firstName', last_name:'lastName', organization_name:'orgName', organization_slug:'orgSlug', password_confirmation:'passwordConfirmation' };
                                fieldErrs.forEach(([k, v]) => { this.errors[keyMap[k] || k] = Array.isArray(v) ? v[0] : v; });
                                this.step = 2;
                                this.errorMsg = json.message || 'Please fix the errors below.';
                            } else {
                                this.errorMsg = fieldErrs.map(([k, v]) => Array.isArray(v) ? v[0] : v).join('. ');
                            }
                        } else {
                            this.errorMsg = json.message || 'Registration failed. Please try again.';
                        }
                    }
                } catch (e) {
                    this.errorMsg = 'Connection error. Please try again.';
                } finally { this.loading = false; }
            },

            googleSignUp() {
                this.errorMsg = '';
                const clientId = '<?= htmlspecialchars($_ENV["GOOGLE_CLIENT_ID"] ?? "", ENT_QUOTES, "UTF-8") ?>';
                if (!clientId) { this.errorMsg = 'Google sign-in is not configured.'; return; }
                google.accounts.id.initialize({
                    client_id: clientId,
                    callback: (response) => {
                        try {
                            const base64 = response.credential.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
                            const payload = JSON.parse(atob(base64));
                            this.firstName = payload.given_name || '';
                            this.lastName  = payload.family_name || '';
                            this.email     = payload.email || '';
                            this.googleCredential = response.credential;
                            this.errorMsg = '';
                            this.successMsg = 'Google profile loaded! Complete the remaining fields.';
                        } catch (e) { this.errorMsg = 'Could not read Google profile.'; }
                    },
                    context: 'signup', auto_select: false, cancel_on_tap_outside: true,
                });
                google.accounts.id.prompt((notification) => {
                    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                        const wrap = document.getElementById('google-btn-fallback');
                        if (wrap) { wrap.style.display = 'block'; google.accounts.id.renderButton(wrap, { type: 'standard', theme: 'outline', size: 'large', text: 'signup_with', width: wrap.offsetWidth || 400 }); }
                    }
                });
            }
        }
    }
    </script>
</body>
</html>
