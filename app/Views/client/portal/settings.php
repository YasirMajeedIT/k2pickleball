<?php $pageTitle = 'Settings — K2 Portal'; ?>

<div x-data="settingsPage()" x-init="init()">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Account Settings</h1>
        <p class="mt-1 text-surface-400">Manage your profile and account preferences.</p>
    </div>

    <!-- Messages -->
    <div x-show="message" x-transition class="mb-6 p-4 rounded-xl text-sm" :class="messageType === 'success' ? 'bg-brand-500/10 border border-brand-500/20 text-brand-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'" x-text="message"></div>

    <!-- Profile Section -->
    <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30 mb-6">
        <h2 class="text-lg font-semibold text-white mb-6">Profile Information</h2>

        <div x-show="loadingProfile" class="animate-pulse space-y-4">
            <div class="h-10 bg-surface-800 rounded"></div>
            <div class="h-10 bg-surface-800 rounded"></div>
            <div class="h-10 bg-surface-800 rounded"></div>
        </div>

        <form x-show="!loadingProfile" @submit.prevent="saveProfile" class="space-y-5">
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">First Name</label>
                    <input type="text" x-model="profile.first_name" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-2">Last Name</label>
                    <input type="text" x-model="profile.last_name" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-2">Email</label>
                <input type="email" x-model="profile.email" disabled class="w-full px-4 py-3 rounded-xl bg-surface-800/50 border border-surface-700/40 text-surface-400 cursor-not-allowed">
                <p class="mt-1 text-xs text-surface-500">Email cannot be changed from here. Contact support for assistance.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-2">Phone (optional)</label>
                <input type="tel" x-model="profile.phone" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="(555) 123-4567">
            </div>
            <div class="flex justify-end">
                <button type="submit" :disabled="savingProfile" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-xl transition-colors">
                    <span x-show="!savingProfile">Save Changes</span>
                    <span x-show="savingProfile" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="p-6 rounded-2xl border border-surface-800/60 bg-surface-900/30 mb-6">
        <h2 class="text-lg font-semibold text-white mb-6">Change Password</h2>
        <form @submit.prevent="changePassword" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-2">Current Password</label>
                <input type="password" x-model="passwords.current" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-2">New Password</label>
                <input type="password" x-model="passwords.new_password" required minlength="8" class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Min. 8 characters">
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-300 mb-2">Confirm New Password</label>
                <input type="password" x-model="passwords.confirm" required class="w-full px-4 py-3 rounded-xl bg-surface-900/50 border border-surface-700/60 text-white placeholder-surface-500 focus:border-brand-500/50 focus:ring-1 focus:ring-brand-500/30 transition-colors" placeholder="Repeat new password">
            </div>
            <div class="flex justify-end">
                <button type="submit" :disabled="savingPassword" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 disabled:opacity-50 rounded-xl transition-colors">
                    <span x-show="!savingPassword">Update Password</span>
                    <span x-show="savingPassword" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        Updating...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="p-6 rounded-2xl border border-red-500/20 bg-red-500/5">
        <h2 class="text-lg font-semibold text-white mb-2">Danger Zone</h2>
        <p class="text-sm text-surface-400 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
        <button @click="if(confirm('Are you sure you want to delete your account? This action cannot be undone.')) deleteAccount()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-400 border border-red-500/30 hover:bg-red-500/10 rounded-xl transition-colors">
            Delete Account
        </button>
    </div>
</div>

<script>
function settingsPage() {
    return {
        profile: { first_name: '', last_name: '', email: '', phone: '' },
        passwords: { current: '', new_password: '', confirm: '' },
        loadingProfile: true,
        savingProfile: false,
        savingPassword: false,
        message: '',
        messageType: 'success',
        init() {
            const token = localStorage.getItem('access_token');
            if (!token) return;
            fetch(APP_BASE + '/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(r => r.json())
            .then(data => {
                const user = data.data || data;
                this.profile = {
                    first_name: user.first_name || '',
                    last_name: user.last_name || '',
                    email: user.email || '',
                    phone: user.phone || ''
                };
                this.loadingProfile = false;
            })
            .catch(() => { this.loadingProfile = false; });
        },
        async saveProfile() {
            this.savingProfile = true;
            this.message = '';
            const token = localStorage.getItem('access_token');
            try {
                const res = await fetch(APP_BASE + '/api/auth/profile', {
                    method: 'PUT',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        first_name: this.profile.first_name,
                        last_name: this.profile.last_name,
                        phone: this.profile.phone
                    })
                });
                if (res.ok) {
                    this.message = 'Profile updated successfully.';
                    this.messageType = 'success';
                    // Update local storage
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    user.first_name = this.profile.first_name;
                    user.last_name = this.profile.last_name;
                    localStorage.setItem('user', JSON.stringify(user));
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
            const token = localStorage.getItem('access_token');
            try {
                const res = await fetch(APP_BASE + '/api/auth/change-password', {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        current_password: this.passwords.current,
                        new_password: this.passwords.new_password,
                        new_password_confirmation: this.passwords.confirm
                    })
                });
                if (res.ok) {
                    this.message = 'Password updated successfully.';
                    this.messageType = 'success';
                    this.passwords = { current: '', new_password: '', confirm: '' };
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
            const token = localStorage.getItem('access_token');
            try {
                await fetch(APP_BASE + '/api/auth/account', {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + token }
                });
            } catch (e) {}
            localStorage.clear();
            window.location.href = APP_BASE + '/';
        }
    }
}
</script>
