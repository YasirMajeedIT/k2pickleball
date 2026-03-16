<?php
// Write the new register.php content
$target = __DIR__ . '/../app/Views/client/auth/register.php';
$content = <<<'PHPEOF'
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — K2 Pickleball</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                    colors: {
                        brand:   { 400:'#34d399',500:'#10b981',600:'#059669',700:'#047857' },
                        surface: { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' }
                    }
                }
            }
        }
    </script>
    <style>.gradient-text{background:linear-gradient(135deg,#34d399 0%,#10b981 50%,#059669 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}</style>
</head>
<body class="h-full bg-surface-950 font-sans text-white antialiased" x-data="registerPage()" x-init="init()">
<div class="min-h-full flex">
    <!-- Left branding panel -->
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
            <p class="mt-4 text-surface-400">Create your free account in minutes. No credit card required for the free plan.</p>
            <div class="mt-12 space-y-6">
                <template x-for="(s,i) in stepLabels" :key="i">
                    <div class="flex items-center gap-4">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors"
                             :class="step>i+1?'bg-brand-600 text-white':step===i+1?'bg-brand-500/20 border border-brand-500 text-brand-400':'bg-surface-800 text-surface-500'">
                            <template x-if="step>i+1"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg></template>
                            <template x-if="step<=i+1"><span x-text="i+1"></span></template>
                        </div>
                        <span class="text-sm" :class="step===i+1?'text-white font-medium':'text-surface-500'" x-text="s"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 overflow-y-auto">
        <div class="w-full max-w-lg">
            <!-- Mobile logo -->
            <div class="lg:hidden mb-8">
                <a href="<?= $baseUrl ?>/" class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-brand-600 flex items-center justify-center"><svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                    <span class="text-xl font-bold">K2 Pickleball</span>
                </a>
            </div>
            <!-- Step progress (mobile) -->
            <div class="lg:hidden flex gap-2 mb-6"><template x-for="i in 3" :key="i"><div class="h-1.5 flex-1 rounded-full transition-colors" :class="step>=i?'bg-brand-500':'bg-surface-800'"></div></template></div>

            <!-- Errors -->
            <div x-show="errors.length>0" x-transition class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 space-y-1">
                <template x-for="(e,i) in errors" :key="i"><p class="text-sm text-red-400" x-text="e"></p></template>
            </div>
            <div x-show="infoMsg" x-transition class="mb-5 p-4 rounded-xl bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm" x-text="infoMsg"></div>

            <!-- ===== STEP 1: Account ===== -->
            <div x-show="step===1" x-transition>
                <h2 class="text-2xl font-extrabold">Create your account</h2>
                <p class="mt-2 text-sm text-surface-400">Already have an account? <a href="<?= $baseUrl ?>/login" class="text-brand-400 hover:text-brand-300 font-medium">Sign in</a></p>

                <?php if (!empty($_ENV['GOOGLE_CLIENT_ID'] ?? '')): ?>
                <div class="mt-6">
                    <div id="google-signup-btn" class="flex justify-center"></div>
                    <div class="relative my-5 flex items-center gap-3">
                        <div class="flex-1 border-t border-surface-800"></div>
                        <span class="text-xs text-surface-500 uppercase tracking-wide">or sign up with email</span>
                        <div class="flex-1 border-t border-surface-800"></div>
                    </div>
                </div>
                <?php endif; ?>

                <form @submit.prevent="nextStep()" class="mt-4 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">First Name</label>
                            <input type="text" x-model="form.first_name" required placeholder="John"
                                class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border text-white placeholder-surface-500 focus:outline-none focus:ring-1 focus:ring-brand-500/30 transition-colors"
                                :class="fieldErrors.first_name?'border-red-500/60':'border-surface-700/60 focus:border-brand-500/50'">
                            <p x-show="fieldErrors.first_name" class="mt-1 text-xs text-red-400" x-text="fieldErrors.first_name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-300 mb-2">Last Name</label>
                            <input type="text" x-model="form.last_name" required placeholder="Doe"
                                class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border text-white placeholder-surface-500 focus:outline-none focus:ring-1 focus:ring-brand-500/30 transition-colors"
                                :class="fieldErrors.last_name?'border-red-500/60':'border-surface-700/60 focus:border-brand-500/50'">
                            <p x-show="fieldErrors.last_name" class="mt-1 text-xs text-red-400" x-text="fieldErrors.last_name"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Email</label>
                        <input type="email" x-model="form.email" required placeholder="john@example.com"
                            class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border text-white placeholder-surface-500 focus:outline-none focus:ring-1 focus:ring-brand-500/30 transition-colors"
                            :class="fieldErrors.email?'border-red-500/60':'border-surface-700/60 focus:border-brand-500/50'">
                        <p x-show="fieldErrors.email" class="mt-1 text-xs text-red-400" x-text="fieldErrors.email"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Password</label>
                        <div class="relative">
                            <input :type="showPw?'text':'password'" x-model="form.password" required @input="checkPw()"
                                placeholder="Min. 8 chars, upper, lower, number"
                                class="w-full px-4 py-3 pr-12 rounded-xl bg-surface-900/50 border text-white placeholder-surface-500 focus:outline-none focus:ring-1 focus:ring-brand-500/30 transition-colors"
                                :class="fieldErrors.password?'border-red-500/60':'border-surface-700/60 focus:border-brand-500/50'">
                            <button type="button" @click="showPw=!showPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-500 hover:text-surface-300 transition-colors">
                                <svg x-show="!showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <!-- Strength bars -->
                        <div class="mt-2 grid grid-cols-4 gap-1">
                            <div class="h-1 rounded-full transition-colors" :class="strength>=1?'bg-red-500':'bg-surface-800'"></div>
                            <div class="h-1 rounded-full transition-colors" :class="strength>=2?'bg-amber-500':'bg-surface-800'"></div>
                            <div class="h-1 rounded-full transition-colors" :class="strength>=3?'bg-yellow-400':'bg-surface-800'"></div>
                            <div class="h-1 rounded-full transition-colors" :class="strength>=4?'bg-brand-500':'bg-surface-800'"></div>
                        </div>
                        <div class="mt-1 flex gap-3 text-xs text-surface-500">
                            <span :class="pwOk.length?'text-brand-400':''">8+ chars</span>
                            <span :class="pwOk.upper?'text-brand-400':''">A-Z</span>
                            <span :class="pwOk.lower?'text-brand-400':''">a-z</span>
                            <span :class="pwOk.number?'text-brand-400':''">0-9</span>
                        </div>
                        <p x-show="fieldErrors.password" class="mt-1 text-xs text-red-400" x-text="fieldErrors.password"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Confirm Password</label>
                        <input :type="showPw?'text':'password'" x-model="form.password_confirmation" required placeholder="Repeat your password"
                            class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border text-white placeholder-surface-500 focus:outline-none focus:ring-1 focus:ring-brand-500/30 transition-colors"
                            :class="fieldErrors.password_confirmation?'border-red-500/60':'border-surface-700/60 focus:border-brand-500/50'">
                        <p x-show="fieldErrors.password_confirmation" class="mt-1 text-xs text-red-400" x-text="fieldErrors.password_confirmation"></p>
                    </div>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                        Continue <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>
            </div>

            <!-- ===== STEP 2: Organization ===== -->
            <div x-show="step===2" x-transition>
                <h2 class="text-2xl font-extrabold">Set up your organization</h2>
                <p class="mt-2 text-sm text-surface-400">Tell us about your facility so we can personalize your experience.</p>
                <form @submit.prevent="nextStep()" class="mt-8 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Organization Name <span class="text-red-400">*</span></label>
                        <input type="text" x-model="form.org_name" required placeholder="Ace Pickleball Club" @input="autoSlug()"
                            class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border text-white placeholder-surface-500 focus:outline-none focus:ring-1 focus:ring-brand-500/30 transition-colors"
                            :class="fieldErrors.org_name?'border-red-500/60':'border-surface-700/60 focus:border-brand-500/50'">
                        <p x-show="fieldErrors.org_name" class="mt-1 text-xs text-red-400" x-text="fieldErrors.org_name"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">URL Identifier <span class="text-red-400">*</span></label>
                        <div class="flex items-stretch rounded-xl overflow-hidden border border-surface-700/60 focus-within:border-brand-500/50 transition-colors">
                            <span class="flex items-center px-3 text-sm text-surface-500 bg-surface-900/90 border-r border-surface-700/60">k2pk.com/</span>
                            <input type="text" x-model="form.org_slug" required pattern="[a-z0-9\-]+" @input="form._slugManual=true"
                                class="flex-1 px-3 py-3 bg-surface-900/50 text-white placeholder-surface-500 focus:outline-none"
                                placeholder="ace-pickleball">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Phone <span class="text-xs text-surface-500 font-normal">(optional)</span></label>
                        <input type="tel" x-model="form.org_phone" placeholder="(555) 123-4567"
                            class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:outline-none focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-2">Address <span class="text-xs text-surface-500 font-normal">(optional)</span></label>
                        <input type="text" x-model="form.org_address" placeholder="123 Main St, City, State"
                            class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:outline-none focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="step=1;errors=[];fieldErrors={}" class="px-6 py-3.5 font-medium text-surface-300 hover:text-white border border-surface-700 hover:border-surface-600 rounded-2xl transition-all">Back</button>
                        <button type="submit" class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                            Continue <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ===== STEP 3: Plan + Payment ===== -->
            <div x-show="step===3" x-transition>
                <h2 class="text-2xl font-extrabold">Choose your plan</h2>
                <p class="mt-2 text-sm text-surface-400">Start free, upgrade anytime.</p>
                <div class="mt-6 space-y-3">
                    <template x-for="plan in plans" :key="plan.id">
                        <label class="block cursor-pointer">
                            <div class="p-5 rounded-2xl border transition-all" :class="form.plan_id==plan.id?'border-brand-500/60 bg-brand-500/5':'border-surface-700/60 bg-surface-900/30 hover:border-surface-600'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="plan" :value="plan.id" x-model="form.plan_id" class="text-brand-600 focus:ring-brand-500 bg-surface-900 border-surface-600">
                                        <div>
                                            <div class="text-base font-semibold" x-text="plan.name"></div>
                                            <div class="text-xs text-surface-400 mt-0.5" x-text="plan.description"></div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold" x-text="plan.price_monthly==0?'Free':'$'+plan.price_monthly.toFixed(2)"></div>
                                        <div x-show="plan.price_monthly>0" class="text-xs text-surface-500">/month</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- Square payment fields for paid plans -->
                <?php if (!empty($_ENV['SQUARE_APPLICATION_ID'] ?? '')): ?>
                <div x-show="selectedPlanPrice>0" x-transition class="mt-6 p-5 rounded-2xl border border-surface-700/60 bg-surface-900/30 space-y-4">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <h3 class="text-sm font-semibold text-surface-300">Secure Payment</h3>
                        <span class="ml-auto text-xs text-surface-500">Powered by Square</span>
                    </div>
                    <p class="text-xs text-surface-500">You will be charged <strong class="text-white" x-text="'$'+selectedPlanPrice.toFixed(2)"></strong> today.</p>
                    <div>
                        <label class="block text-xs font-medium text-surface-400 mb-2">Card Number</label>
                        <div id="sq-card-number" class="px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 min-h-[52px]"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-400 mb-2">Expiry Date</label>
                            <div id="sq-expiration-date" class="px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 min-h-[52px]"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-400 mb-2">CVV</label>
                            <div id="sq-cvv" class="px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 min-h-[52px]"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-400 mb-2">ZIP / Postal Code</label>
                        <div id="sq-postal-code" class="px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 min-h-[52px]"></div>
                    </div>
                    <p x-show="fieldErrors.payment" class="text-xs text-red-400 mt-1" x-text="fieldErrors.payment"></p>
                </div>
                <?php endif; ?>

                <div class="mt-6 flex gap-3">
                    <button type="button" @click="step=2;errors=[];fieldErrors={}" class="px-6 py-3.5 font-medium text-surface-300 hover:text-white border border-surface-700 hover:border-surface-600 rounded-2xl transition-all">Back</button>
                    <button @click="submit()" :disabled="loading" class="flex-1 flex items-center justify-center gap-2 px-6 py-3.5 font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-2xl shadow-lg shadow-brand-600/25 transition-all hover:-translate-y-0.5">
                        <span x-show="!loading" x-text="selectedPlanPrice>0?'Pay &amp; Create Account':'Create Free Account'"></span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            <span x-text="loadingMsg"></span>
                        </span>
                    </button>
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-surface-500">By creating an account, you agree to our <a href="#" class="hover:text-surface-300 underline">Terms</a> and <a href="#" class="hover:text-surface-300 underline">Privacy Policy</a>.</p>
        </div>
    </div>
</div>

<?php
$sqAppId  = htmlspecialchars($_ENV['SQUARE_APPLICATION_ID'] ?? '', ENT_QUOTES);
$sqLocId  = htmlspecialchars($_ENV['SQUARE_LOCATION_ID'] ?? '', ENT_QUOTES);
$sqEnv    = ($_ENV['SQUARE_ENVIRONMENT'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';
$gClientId = htmlspecialchars($_ENV['GOOGLE_CLIENT_ID'] ?? '', ENT_QUOTES);
if ($sqAppId): ?>
<script src="https://<?= $sqEnv === 'production' ? 'web' : 'sandbox' ?>.squarecdn.com/v1/square.js"></script>
<?php endif; ?>

<script>
const BASE_URL    = '<?= $baseUrl ?>';
const SQ_APP_ID   = '<?= $sqAppId ?>';
const SQ_LOC_ID   = '<?= $sqLocId ?>';
const GOOGLE_CID  = '<?= $gClientId ?>';

function registerPage() {
    return {
        step: 1,
        stepLabels: ['Create your account', 'Set up organization', 'Choose your plan'],
        form: { first_name:'', last_name:'', email:'', password:'', password_confirmation:'',
                org_name:'', org_slug:'', org_phone:'', org_address:'', plan_id:null, _slugManual:false },
        plans: [],
        loading: false, loadingMsg: 'Creating account…',
        errors: [], fieldErrors: {}, infoMsg: '',
        showPw: false, strength: 0,
        pwOk: { length:false, upper:false, lower:false, number:false },
        sqPayments: null, sqCard: null,

        get selectedPlanPrice() {
            const p = this.plans.find(pl => pl.id == this.form.plan_id);
            return p ? p.price_monthly : 0;
        },

        async init() {
            // Load plans
            try {
                const r = await fetch(BASE_URL + '/api/plans');
                const d = await r.json();
                const list = (d.data || d);
                this.plans = list.map(p => ({ ...p, price_monthly: parseFloat(p.price_monthly)||0, description: p.description||'' }));
            } catch {
                this.plans = [
                    { id:1, name:'Free',         price_monthly:0,      description:'Up to 4 courts, 10 users' },
                    { id:2, name:'Professional', price_monthly:49.99,  description:'20 courts, 100 users, payments' },
                    { id:3, name:'Enterprise',   price_monthly:149.99, description:'Unlimited everything' }
                ];
            }
            const free = this.plans.find(p => p.price_monthly === 0);
            if (free) this.form.plan_id = free.id;

            // Google sign-up button
            if (GOOGLE_CID && typeof google !== 'undefined') {
                google.accounts.id.initialize({ client_id: GOOGLE_CID, callback: r => this.googleCallback(r) });
                google.accounts.id.renderButton(document.getElementById('google-signup-btn'), { theme:'filled_black', size:'large', text:'signup_with', width:400 });
            }
        },

        checkPw() {
            const p = this.form.password;
            this.pwOk = { length: p.length>=8, upper: /[A-Z]/.test(p), lower: /[a-z]/.test(p), number: /[0-9]/.test(p) };
            this.strength = Object.values(this.pwOk).filter(Boolean).length;
        },

        autoSlug() {
            if (!this.form._slugManual) {
                this.form.org_slug = this.form.org_name.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
            }
        },

        setErrors(apiErrors, fallback) {
            this.errors = []; this.fieldErrors = {};
            if (!apiErrors) { if (fallback) this.errors = [fallback]; return; }
            if (typeof apiErrors === 'string') { this.errors = [apiErrors]; return; }
            for (const [field, msgs] of Object.entries(apiErrors)) {
                const msg = Array.isArray(msgs) ? msgs.join(' ') : String(msgs);
                this.fieldErrors[field] = msg;
                this.errors.push(msg);
            }
        },

        nextStep() {
            this.errors = []; this.fieldErrors = {};
            if (this.step === 1) {
                let ok = true;
                if (!this.form.first_name.trim()) { this.fieldErrors.first_name = 'First name is required.'; ok = false; }
                if (!this.form.last_name.trim())  { this.fieldErrors.last_name  = 'Last name is required.';  ok = false; }
                if (!this.form.email.trim())       { this.fieldErrors.email      = 'Email is required.';      ok = false; }
                if (!this.pwOk.length)  { this.fieldErrors.password = 'Password must be at least 8 characters.'; ok = false; }
                else if (!this.pwOk.upper)  { this.fieldErrors.password = 'Password must contain an uppercase letter.'; ok = false; }
                else if (!this.pwOk.lower)  { this.fieldErrors.password = 'Password must contain a lowercase letter.'; ok = false; }
                else if (!this.pwOk.number) { this.fieldErrors.password = 'Password must contain a number.'; ok = false; }
                if (this.form.password !== this.form.password_confirmation) {
                    this.fieldErrors.password_confirmation = 'Passwords do not match.'; ok = false;
                }
                if (!ok) { this.errors = Object.values(this.fieldErrors).filter(Boolean); return; }
            }
            if (this.step === 2) {
                if (!this.form.org_name.trim()) { this.fieldErrors.org_name = 'Organization name is required.'; this.errors = [this.fieldErrors.org_name]; return; }
                if (!this.form.org_slug.trim()) this.autoSlug();
            }
            this.step++;
            // Init Square card fields when reaching step 3
            if (this.step === 3 && SQ_APP_ID && typeof Square !== 'undefined' && !this.sqCard) {
                this.$nextTick(async () => {
                    try {
                        this.sqPayments = Square.payments(SQ_APP_ID, SQ_LOC_ID);
                        this.sqCard = await this.sqPayments.card({
                            style: {
                                input: { color:'#fff', backgroundColor:'transparent', fontSize:'15px', fontFamily:'Inter,sans-serif' },
                                'input::placeholder': { color:'#64748b' },
                                '.input-container': { borderColor:'transparent' },
                            }
                        });
                        await this.sqCard.attach('#sq-card-number');
                    } catch(e) { console.warn('Square init:', e); }
                });
            }
        },

        async submit() {
            this.errors = []; this.fieldErrors = {};
            this.loading = true; this.loadingMsg = 'Creating account…';
            try {
                const regRes  = await fetch(BASE_URL + '/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        first_name:            this.form.first_name,
                        last_name:             this.form.last_name,
                        email:                 this.form.email,
                        password:              this.form.password,
                        password_confirmation: this.form.password_confirmation,
                    })
                });
                const regData = await regRes.json();
                if (!regRes.ok) {
                    this.setErrors(regData.errors, regData.message || 'Registration failed.');
                    if (regData.errors) this.step = 1; // go back so field errors are visible
                    this.loading = false; return;
                }

                // Store form data in sessionStorage for post-verify setup
                sessionStorage.setItem('k2_pending_reg', JSON.stringify({
                    email:       this.form.email,
                    password:    this.form.password,
                    org_name:    this.form.org_name,
                    org_slug:    this.form.org_slug,
                    org_phone:   this.form.org_phone,
                    org_address: this.form.org_address,
                    plan_id:     this.form.plan_id,
                }));

                // Redirect to email verification page
                window.location.href = BASE_URL + '/verify-email?email=' + encodeURIComponent(this.form.email);
            } catch(e) {
                this.errors = ['Network error. Please check your connection and try again.'];
                this.loading = false;
            }
        },

        // Called after user verifies email and logs in — complete org + subscription setup
        async completeSetup(token, email) {
            // Create org
            this.loadingMsg = 'Setting up organization…';
            const orgRes  = await fetch(BASE_URL + '/api/organizations', {
                method:'POST', headers:{'Content-Type':'application/json','Authorization':'Bearer '+token},
                body: JSON.stringify({ name:this.form.org_name, slug:this.form.org_slug, phone:this.form.org_phone||undefined, address:this.form.org_address||undefined })
            });
            const orgData = await orgRes.json();
            const orgId   = orgData.data?.id || orgData.id;
            if (!orgId) return;

            // Payment for paid plans
            if (this.selectedPlanPrice > 0 && this.sqCard) {
                this.loadingMsg = 'Processing payment…';
                const tokenResult = await this.sqCard.tokenize();
                if (tokenResult.status !== 'OK') {
                    this.fieldErrors.payment = tokenResult.errors?.map(e=>e.message).join(', ') || 'Card declined.';
                    this.errors = [this.fieldErrors.payment];
                    this.loading = false; return;
                }
                const amount = Math.round(this.selectedPlanPrice * 100);
                await fetch(BASE_URL + '/api/payments', {
                    method:'POST', headers:{'Content-Type':'application/json','Authorization':'Bearer '+token},
                    body: JSON.stringify({ source_id:tokenResult.token, amount, currency:'USD', description:'Subscription payment' })
                });
            }

            // Create subscription
            this.loadingMsg = 'Setting up subscription…';
            await fetch(BASE_URL + '/api/subscriptions', {
                method:'POST', headers:{'Content-Type':'application/json','Authorization':'Bearer '+token},
                body: JSON.stringify({ organization_id:orgId, plan_id:this.form.plan_id, billing_cycle:'monthly' })
            });

            window.location.href = BASE_URL + '/portal';
        },

        async googleCallback(response) {
            this.loading = true; this.loadingMsg = 'Signing in with Google…'; this.errors = [];
            try {
                const res  = await fetch(BASE_URL + '/api/auth/google', {
                    method:'POST', headers:{'Content-Type':'application/json'},
                    body: JSON.stringify({ credential: response.credential })
                });
                const data = await res.json();
                if (!res.ok) { this.errors = [data.message || 'Google sign-in failed.']; this.loading=false; return; }
                const d = data.data || data;
                localStorage.setItem('access_token',  d.access_token);
                localStorage.setItem('refresh_token', d.refresh_token);
                if (d.user) localStorage.setItem('user', JSON.stringify(d.user));
                window.location.href = BASE_URL + '/portal';
            } catch(e) { this.errors = ['Google sign-in failed. Please try again.']; this.loading = false; }
        },
    }
}
</script>
</body>
</html>
PHPEOF;

file_put_contents($target, $content);
echo "Written: " . strlen($content) . " bytes to " . $target . PHP_EOL;
