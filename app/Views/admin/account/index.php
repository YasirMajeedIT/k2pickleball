<?php
$title = 'My Account';
$breadcrumbs = [['label' => 'My Account']];
ob_start();
?>

<div x-data="accountPage()" x-init="init()">
    <!-- Messages -->
    <div x-show="message" x-transition class="mb-6 p-4 rounded-xl text-sm"
         :class="messageType === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'"
         x-text="message"></div>

    <!-- Profile Section -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-6">
        <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
            <h5 class="text-base font-bold text-surface-900 dark:text-white">Profile Information</h5>
            <p class="text-xs text-surface-400 mt-0.5">Update your personal details</p>
        </div>
        <div class="p-6">
            <div x-show="loadingProfile" class="animate-pulse space-y-4">
                <div class="h-10 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                <div class="h-10 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                <div class="h-10 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
            </div>

            <form x-show="!loadingProfile" @submit.prevent="saveProfile" class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">First Name</label>
                        <input type="text" x-model="profile.first_name" required class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Last Name</label>
                        <input type="text" x-model="profile.last_name" required class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Email</label>
                    <input type="email" x-model="profile.email" disabled class="w-full px-4 py-3 rounded-xl bg-surface-100 dark:bg-surface-800/30 border border-surface-200 dark:border-surface-700/40 text-surface-400 cursor-not-allowed">
                    <p class="mt-1 text-xs text-surface-400">Email cannot be changed from here. Contact support for assistance.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Phone</label>
                    <input type="tel" x-model="profile.phone" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="(555) 123-4567">
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="savingProfile" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl shadow-soft transition-colors">
                        <span x-show="!savingProfile">Save Changes</span>
                        <span x-show="savingProfile" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-6">
        <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
            <h5 class="text-base font-bold text-surface-900 dark:text-white">Change Password</h5>
            <p class="text-xs text-surface-400 mt-0.5">Ensure your account uses a strong password</p>
        </div>
        <div class="p-6">
            <form @submit.prevent="changePassword" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Current Password</label>
                    <input type="password" x-model="passwords.current" required class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">New Password</label>
                    <input type="password" x-model="passwords.new_password" required minlength="8" class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="Min. 8 characters">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Confirm New Password</label>
                    <input type="password" x-model="passwords.confirm" required class="w-full px-4 py-3 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="Repeat new password">
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="savingPassword" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl shadow-soft transition-colors">
                        <span x-show="!savingPassword">Update Password</span>
                        <span x-show="savingPassword" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bank Account & Withdrawals -->
    <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-6">
        <div class="px-6 py-5 border-b border-surface-100 dark:border-surface-800 bg-surface-50/50 dark:bg-surface-800/30">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-base font-bold text-surface-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                        Bank Account & Withdrawals
                    </h5>
                    <p class="text-xs text-surface-400 mt-0.5">Manage your bank account for receiving payouts from Square transactions</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <!-- Balance Overview -->
            <div class="grid sm:grid-cols-3 gap-4 mb-6">
                <div class="rounded-xl bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 p-4">
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Available Balance</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1" x-text="'$' + bankData.availableBalance"></p>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20 p-4">
                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pending</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1" x-text="'$' + bankData.pendingBalance"></p>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-primary-500/10 to-primary-600/5 border border-primary-500/20 p-4">
                    <p class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wider">Total Withdrawn</p>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white mt-1" x-text="'$' + bankData.totalWithdrawn"></p>
                </div>
            </div>

            <!-- Connected Bank Account -->
            <div class="mb-6">
                <h6 class="text-sm font-semibold text-surface-700 dark:text-surface-300 mb-3">Connected Bank Account</h6>
                <template x-if="bankData.bankAccount">
                    <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-4 bg-surface-50/50 dark:bg-surface-800/30">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-lg bg-primary-500/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-surface-800 dark:text-white" x-text="bankData.bankAccount.brand || 'Bank Account'"></p>
                                <p class="text-xs text-surface-500" x-text="'•••• •••• •••• ' + (bankData.bankAccount.last_four || '****')"></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400" x-show="bankData.bankAccount.status === 'active'">Active</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400" x-show="bankData.bankAccount.status !== 'active'" x-text="bankData.bankAccount.status"></span>
                        </div>
                        <button @click="removeBankAccount()" class="text-red-400 hover:text-red-500 text-xs font-medium transition-colors">Remove</button>
                    </div>
                </template>
                <template x-if="!bankData.bankAccount">
                    <div class="rounded-xl border-2 border-dashed border-surface-200 dark:border-surface-700 px-6 py-8 text-center">
                        <svg class="w-10 h-10 mx-auto text-surface-300 dark:text-surface-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                        <p class="text-sm text-surface-500 mb-4">No bank account connected. Add one to receive payouts.</p>
                        <button @click="bankData.showAddForm = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-xl shadow-soft transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Bank Account
                        </button>
                    </div>
                </template>
            </div>

            <!-- Add Bank Account Form -->
            <div x-show="bankData.showAddForm" x-transition class="mb-6 rounded-xl border border-primary-200 dark:border-primary-500/30 bg-primary-50/50 dark:bg-primary-500/5 p-5">
                <h6 class="text-sm font-semibold text-surface-800 dark:text-white mb-4">Add Bank Account</h6>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Account Holder Name</label>
                        <input type="text" x-model="bankData.newAccount.cardholder_name" class="w-full px-3 py-2.5 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="John Smith">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Bank Name</label>
                        <input type="text" x-model="bankData.newAccount.brand" class="w-full px-3 py-2.5 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="Chase, Bank of America, etc.">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Routing Number</label>
                        <input type="text" x-model="bankData.newAccount.routing_number" maxlength="9" pattern="[0-9]{9}" class="w-full px-3 py-2.5 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="9 digits">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Account Number (Last 4)</label>
                        <input type="text" x-model="bankData.newAccount.last_four" maxlength="4" pattern="[0-9]{4}" class="w-full px-3 py-2.5 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none" placeholder="Last 4 digits">
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button @click="addBankAccount()" :disabled="bankData.savingAccount" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-lg transition-colors">
                        <span x-show="!bankData.savingAccount">Save Bank Account</span>
                        <span x-show="bankData.savingAccount">Saving...</span>
                    </button>
                    <button @click="bankData.showAddForm = false" class="px-4 py-2 text-sm font-medium text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 transition-colors">Cancel</button>
                </div>
            </div>

            <!-- Withdrawal / Payout History -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h6 class="text-sm font-semibold text-surface-700 dark:text-surface-300">Transaction History</h6>
                </div>
                <div x-show="bankData.loadingHistory" class="space-y-3">
                    <div class="h-12 bg-surface-100 dark:bg-surface-800 rounded-xl animate-pulse"></div>
                    <div class="h-12 bg-surface-100 dark:bg-surface-800 rounded-xl animate-pulse"></div>
                </div>
                <template x-if="!bankData.loadingHistory && bankData.transactions.length === 0">
                    <div class="text-center py-8">
                        <svg class="w-10 h-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-surface-500">No transactions yet</p>
                    </div>
                </template>
                <div x-show="!bankData.loadingHistory && bankData.transactions.length > 0" class="overflow-hidden rounded-xl border border-surface-200 dark:border-surface-700">
                    <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                        <thead class="bg-surface-50 dark:bg-surface-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                            <template x-for="tx in bankData.transactions" :key="tx.id">
                                <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30 transition-colors">
                                    <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400" x-text="new Date(tx.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
                                              :class="{
                                                  'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': tx.type === 'charge',
                                                  'bg-red-500/10 text-red-600 dark:text-red-400': tx.type === 'refund',
                                                  'bg-primary-500/10 text-primary-600 dark:text-primary-400': tx.type === 'payout',
                                                  'bg-amber-500/10 text-amber-600 dark:text-amber-400': tx.type === 'adjustment' || tx.type === 'credit',
                                              }"
                                              x-text="tx.type.charAt(0).toUpperCase() + tx.type.slice(1)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-surface-700 dark:text-surface-300" x-text="tx.description || '-'"></td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right"
                                        :class="tx.type === 'refund' || tx.type === 'payout' ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400'"
                                        x-text="(tx.type === 'refund' || tx.type === 'payout' ? '-' : '+') + '$' + (parseFloat(tx.amount) / 100).toFixed(2)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="rounded-2xl border border-red-500/20 bg-red-500/5 dark:bg-red-500/5 p-6">
        <h5 class="text-base font-bold text-surface-900 dark:text-white mb-2">Danger Zone</h5>
        <p class="text-sm text-surface-500 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
        <button @click="if(confirm('Are you sure you want to delete your account? This action cannot be undone.')) deleteAccount()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-500 border border-red-500/30 hover:bg-red-500/10 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
            Delete Account
        </button>
    </div>
</div>

<script>
function accountPage() {
    return {
        profile: { first_name: '', last_name: '', email: '', phone: '' },
        passwords: { current: '', new_password: '', confirm: '' },
        loadingProfile: true,
        savingProfile: false,
        savingPassword: false,
        message: '',
        messageType: 'success',
        bankData: {
            availableBalance: '0.00',
            pendingBalance: '0.00',
            totalWithdrawn: '0.00',
            bankAccount: null,
            transactions: [],
            loadingHistory: true,
            showAddForm: false,
            savingAccount: false,
            newAccount: { cardholder_name: '', brand: '', routing_number: '', last_four: '' },
        },
        init() {
            // Hydrate immediately from localStorage so the form is never blank
            try {
                const stored = JSON.parse(localStorage.getItem('user') || '{}');
                if (stored && (stored.first_name || stored.email)) {
                    this.profile = {
                        first_name: stored.first_name || '',
                        last_name:  stored.last_name  || '',
                        email:      stored.email      || '',
                        phone:      stored.phone      || '',
                    };
                    this.loadingProfile = false;
                }
            } catch (_) {}

            // Refresh from API (updates any stale cached data)
            authFetch(APP_BASE + '/api/auth/me')
            .then(r => r.json())
            .then(data => {
                const user = data.data || data;
                if (user && (user.first_name || user.email)) {
                    this.profile = {
                        first_name: user.first_name || '',
                        last_name:  user.last_name  || '',
                        email:      user.email      || '',
                        phone:      user.phone      || '',
                    };
                    // Keep localStorage in sync
                    try {
                        const stored = JSON.parse(localStorage.getItem('user') || '{}');
                        localStorage.setItem('user', JSON.stringify({ ...stored, ...this.profile }));
                    } catch (_) {}
                }
                this.loadingProfile = false;
            })
            .catch(() => { this.loadingProfile = false; });

            // Load bank account (payment methods with type=bank_account)
            authFetch(APP_BASE + '/api/payment-methods')
            .then(r => r.json())
            .then(data => {
                const methods = data.data || [];
                const bank = methods.find(m => m.type === 'bank_account' && m.status === 'active');
                if (bank) this.bankData.bankAccount = bank;
            }).catch(() => {});

            // Load transactions
            authFetch(APP_BASE + '/api/transactions')
            .then(r => r.json())
            .then(data => {
                const txs = data.data || [];
                this.bankData.transactions = txs;
                // Calculate balances from transactions
                let charges = 0, refunds = 0, payouts = 0;
                txs.forEach(tx => {
                    const amt = (parseFloat(tx.amount) || 0) / 100;
                    if (tx.type === 'charge') charges += amt;
                    else if (tx.type === 'refund') refunds += amt;
                    else if (tx.type === 'payout') payouts += amt;
                });
                this.bankData.availableBalance = (charges - refunds - payouts).toFixed(2);
                this.bankData.totalWithdrawn = payouts.toFixed(2);
                this.bankData.loadingHistory = false;
            }).catch(() => { this.bankData.loadingHistory = false; });
        },
        async addBankAccount() {
            const acc = this.bankData.newAccount;
            if (!acc.cardholder_name || !acc.last_four) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please fill in all required fields.' } }));
                return;
            }
            this.bankData.savingAccount = true;
            try {
                const res = await authFetch(APP_BASE + '/api/payment-methods', {
                    method: 'POST',
                    body: JSON.stringify({
                        type: 'bank_account',
                        brand: acc.brand,
                        last_four: acc.last_four,
                        cardholder_name: acc.cardholder_name,
                        is_default: true,
                    })
                });
                const json = await res.json();
                if (res.ok) {
                    this.bankData.bankAccount = json.data;
                    this.bankData.showAddForm = false;
                    this.bankData.newAccount = { cardholder_name: '', brand: '', routing_number: '', last_four: '' };
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Bank account added successfully.' } }));
                } else {
                    const errMsg = json.errors ? Object.values(json.errors).flat()[0] : (json.message || 'Failed to add bank account.');
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: errMsg } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Network error.' } }));
            }
            this.bankData.savingAccount = false;
        },
        async removeBankAccount() {
            if (!confirm('Are you sure you want to remove this bank account?')) return;
            if (!this.bankData.bankAccount) return;
            try {
                const res = await authFetch(APP_BASE + '/api/payment-methods/' + this.bankData.bankAccount.id, { method: 'DELETE' });
                if (res.ok) {
                    this.bankData.bankAccount = null;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Bank account removed.' } }));
                }
            } catch (e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Failed to remove account.' } }));
            }
        },
        async saveProfile() {
            this.savingProfile = true;
            this.message = '';
            try {
                const res = await authFetch(APP_BASE + '/api/auth/profile', {
                    method: 'PUT',
                    body: JSON.stringify({
                        first_name: this.profile.first_name,
                        last_name: this.profile.last_name,
                        phone: this.profile.phone
                    })
                });
                if (res.ok) {
                    this.message = 'Profile updated successfully.';
                    this.messageType = 'success';
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    user.first_name = this.profile.first_name;
                    user.last_name = this.profile.last_name;
                    localStorage.setItem('user', JSON.stringify(user));
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Profile updated successfully.' } }));
                } else {
                    const err = await res.json();
                    this.message = err.error || err.message || 'Failed to update profile.';
                    this.messageType = 'error';
                }
            } catch (e) {
                this.message = 'Network error. Please try again.';
                this.messageType = 'error';
            }
            this.savingProfile = false;
        },
        async changePassword() {
            this.message = '';
            if (this.passwords.new_password !== this.passwords.confirm) {
                this.message = 'New passwords do not match.';
                this.messageType = 'error';
                return;
            }
            if (this.passwords.new_password.length < 8) {
                this.message = 'Password must be at least 8 characters.';
                this.messageType = 'error';
                return;
            }
            this.savingPassword = true;
            try {
                const res = await authFetch(APP_BASE + '/api/auth/change-password', {
                    method: 'POST',
                    body: JSON.stringify({
                        current_password: this.passwords.current,
                        password: this.passwords.new_password,
                        password_confirmation: this.passwords.confirm
                    })
                });
                if (res.ok) {
                    this.message = 'Password updated successfully.';
                    this.messageType = 'success';
                    this.passwords = { current: '', new_password: '', confirm: '' };
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Password updated successfully.' } }));
                } else {
                    const err = await res.json();
                    this.message = err.error || err.message || 'Failed to update password.';
                    this.messageType = 'error';
                }
            } catch (e) {
                this.message = 'Network error. Please try again.';
                this.messageType = 'error';
            }
            this.savingPassword = false;
        },
        async deleteAccount() {
            try {
                await authFetch(APP_BASE + '/api/auth/account', { method: 'DELETE' });
            } catch (e) {}
            localStorage.clear();
            window.location.href = APP_BASE + '/';
        }
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
