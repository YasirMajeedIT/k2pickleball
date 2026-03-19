<?php
/**
 * Player Dashboard — Profile — K2 Navy/Gold Theme
 * Edit name, email, phone + Change password section.
 */
?>

<div x-data="profilePage()" x-init="load()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-display font-extrabold text-white">My Profile</h1>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="py-10 text-center">
        <svg class="animate-spin w-6 h-6 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <div x-show="!loading" class="grid gap-6 lg:grid-cols-2">
        <!-- Personal Info -->
        <div class="glass-card rounded-2xl gold-border p-6">
            <h2 class="text-lg font-display font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Personal Information
            </h2>

            <!-- Avatar circle -->
            <div class="w-16 h-16 rounded-full gradient-gold-bg flex items-center justify-center mb-5">
                <span class="text-xl font-extrabold text-navy-950" x-text="(form.first_name?.[0] || '') + (form.last_name?.[0] || '')"></span>
            </div>

            <!-- Profile Error -->
            <div x-show="profileError" class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-2 rounded-lg mb-4" x-text="profileError"></div>
            <!-- Profile Success -->
            <div x-show="profileSuccess" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm px-4 py-2 rounded-lg mb-4" x-text="profileSuccess"></div>

            <form @submit.prevent="saveProfile()" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-400 font-semibold mb-1">First Name</label>
                        <input type="text" x-model="form.first_name" required
                               class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 font-semibold mb-1">Last Name</label>
                        <input type="text" x-model="form.last_name" required
                               class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Email</label>
                    <input type="email" x-model="form.email" required
                           class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Phone</label>
                    <input type="tel" x-model="form.phone"
                           class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                </div>
                <button type="submit" :disabled="savingProfile"
                        class="w-full py-2.5 rounded-lg gradient-gold-bg text-navy-950 text-sm font-bold hover:shadow-gold transition-all disabled:opacity-50">
                    <span x-show="!savingProfile">Save Changes</span>
                    <span x-show="savingProfile" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Saving...
                    </span>
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="glass-card rounded-2xl gold-border p-6">
            <h2 class="text-lg font-display font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Change Password
            </h2>

            <!-- PW Error -->
            <div x-show="pwError" class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-2 rounded-lg mb-4" x-text="pwError"></div>
            <!-- PW Success -->
            <div x-show="pwSuccess" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm px-4 py-2 rounded-lg mb-4" x-text="pwSuccess"></div>

            <form @submit.prevent="changePassword()" class="space-y-4">
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Current Password</label>
                    <input type="password" x-model="pw.current_password" required
                           class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">New Password</label>
                    <input type="password" x-model="pw.new_password" required minlength="8"
                           class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 font-semibold mb-1">Confirm New Password</label>
                    <input type="password" x-model="pw.new_password_confirmation" required
                           class="w-full bg-navy-900 border border-navy-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-gold-500/50 focus:ring-1 focus:ring-gold-500/30 transition-colors">
                </div>
                <button type="submit" :disabled="savingPw"
                        class="w-full py-2.5 rounded-lg bg-navy-800 hover:bg-navy-700 text-white text-sm font-bold border border-navy-600 transition-all disabled:opacity-50">
                    <span x-show="!savingPw">Update Password</span>
                    <span x-show="savingPw" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Updating...
                    </span>
                </button>
            </form>

            <!-- Account Info -->
            <div class="mt-6 pt-4 border-t border-navy-800">
                <p class="text-xs text-slate-600">Member since <span x-text="memberSince"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
function profilePage() {
    return {
        loading: true,
        form: { first_name: '', last_name: '', email: '', phone: '' },
        pw: { current_password: '', new_password: '', new_password_confirmation: '' },
        savingProfile: false,
        savingPw: false,
        profileError: '',
        profileSuccess: '',
        pwError: '',
        pwSuccess: '',
        memberSince: '',

        async load() {
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            if (!app?.player) {
                this.loading = false;
                return;
            }
            const p = app.player;
            this.form.first_name = p.first_name || '';
            this.form.last_name = p.last_name || '';
            this.form.email = p.email || '';
            this.form.phone = p.phone || '';
            if (p.created_at) {
                this.memberSince = new Date(p.created_at).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            }
            this.loading = false;
        },

        async saveProfile() {
            this.profileError = '';
            this.profileSuccess = '';
            this.savingProfile = true;
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            try {
                const resp = await app.authFetch(baseApi + '/api/auth/profile', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const json = await resp.json();
                if (json.success) {
                    this.profileSuccess = 'Profile updated successfully.';
                    if (json.data) {
                        app.player = { ...app.player, ...json.data };
                    }
                } else {
                    this.profileError = json.message || 'Failed to update profile.';
                }
            } catch (e) { this.profileError = 'Network error. Please try again.'; }
            finally { this.savingProfile = false; }
        },

        async changePassword() {
            this.pwError = '';
            this.pwSuccess = '';
            if (this.pw.new_password !== this.pw.new_password_confirmation) {
                this.pwError = 'New passwords do not match.';
                return;
            }
            this.savingPw = true;
            const app = document.querySelector('[x-data*=dashboardApp]')?.__x?.$data;
            try {
                const resp = await app.authFetch(baseApi + '/api/auth/change-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.pw)
                });
                const json = await resp.json();
                if (json.success) {
                    this.pwSuccess = 'Password changed successfully.';
                    this.pw = { current_password: '', new_password: '', new_password_confirmation: '' };
                } else {
                    this.pwError = json.message || 'Failed to change password.';
                }
            } catch (e) { this.pwError = 'Network error. Please try again.'; }
            finally { this.savingPw = false; }
        }
    };
}
</script>
